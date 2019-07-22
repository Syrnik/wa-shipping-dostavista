<?php
/**
 * @author Serge Rodovnichenko <serge@syrnik.com>
 * @copyright (c) 2019, Serge Rodovnichenko
 * @license http://www.webasyst.com/terms/#eula Webasyst
 */

use SergeR\CakeUtility\Hash;
use Syrnik\dostavistaShipping\Address;

/**
 * @property-read string $token
 * @property-read array $location_from = ['name'=>(string)]
 * @property-read string $api_server = 'test'|'production'
 * @property-read string $delivery_time Время доставки
 * @property-read int $exact_delivery_time Среднее количество часов доставки (если выбрано Время Доставки - указанное
 *     кол-во часов
 * @property-read array{
 *     date: bool,
 *     interval: bool,
 *     intervals: array<array{
 *          from: string,
 *          from_m: string,
 *          to: string,
 *          to_m: string,
 *          day: array<int>,
 *          workday: bool,
 *          holiday: bool
 *      }>
 * } $customer_interval
 * @property-read array<string> $holidays
 * @property-read array<string> $workdays
 */
class dostavistaShipping extends waShipping
{
    /** @var waSmarty3View|null */
    protected $_view;

    /** @var waSystem|null */
    protected $_system;

    /** @var null|array{time:int, holidays:array<string>, workdays:array<string>} */
    protected $_schedule;

    /**
     * @return string|array|bool
     * @throws waException
     */
    protected function calculate()
    {
        $address = new Address($this->getAddress());
        if (($address_errors = $this->addressValidationErrors($address)) !== true) {
            return $address_errors;
        }

        $response = $this->queryDostavistaApi('calculate-order', waNet::METHOD_POST, [
            'matter'          => 'Shopping',
            'total_weight_kg' => (int)$this->getTotalWeight(),
            'points'          => [
                [
                    'address' => $this->location_from['name']
                ],
                [
                    'address' => (string)$address
                ]
            ]
        ]);

        if (!$response['is_successful']) {
            return [
                'rate'    => null,
                'comment' => 'Доставка по указанному адресу невозможна'
            ];
        }

        $result = [
            'dostavista_courier' => [
                'rate'     => (float)Hash::get($response, 'order.payment_amount'),
                'currency' => 'RUB',
                'type'     => waShipping::TYPE_TODOOR
            ]
        ];

        if (($destination_address = (string)Hash::get($response, 'order.points.1.address'))) {
            $result['dostavista_courier']['comment'] = "курьером по адресу: " . waString::escapeAll($destination_address);
            $result = Hash::insert(
                $result,
                'dostavista_courier.custom_data.' . waShipping::TYPE_TODOOR,
                ['additional' => "Доставка курьером по адресу: " . waString::escapeAll($destination_address)]
            );
        }

        $delivery_times = $this->getDeliveryTimes();
        $result['dostavista_courier']['est_delivery'] = $delivery_times['estimate'];

        $setting = $this->customer_interval;
        if (!empty($setting['intervals'])) {
            $intervals = [];
            $date_format = waDateTime::getFormat('date');
            $offset = null;

            foreach ($setting['intervals'] as $interval) {
                $interval = $this->getInterval($interval, $delivery_times['timestamp']);

                if (!empty($interval['start_date'])) {
                    $key = $interval['interval'];
                    $intervals[$key] = array_keys($interval['day']);
                    $intervals[$key]['offset'] = $interval['offset'];
                    if (!isset($result['dostavista_courier']['delivery_date'])
                        || (strtotime($result['dostavista_courier']['delivery_date']) > strtotime($interval['start_date']))
                    ) {
                        $delivery['delivery_date'] = $interval['start_date'];
                    }

                    if (($offset === null) || ($offset > $interval['offset'])) {
                        $offset = $interval['offset'];
                    }
                }
            }

            $custom_data = array(
                'offset'      => $offset,
                'intervals'   => $intervals,
                'placeholder' => waDateTime::format($date_format, $delivery['delivery_date']),
                'holidays'    => $this->holidays,
                'workdays'    => $this->workdays,
            );
            $result['dostavista_courier']['custom_data'][waShipping::TYPE_TODOOR] = (array)Hash::get($result, 'dostavista_courier.custom_data.' . waShipping::TYPE_TODOOR) + $custom_data;
        }

        return $result;
    }

    /**
     * @return string
     */
    public function allowedCurrency()
    {
        return 'RUB';
    }

    /**
     * @return string
     */
    public function allowedWeightUnit()
    {
        return 'kg';
    }

    /**
     * @return array
     */
    public function allowedAddress()
    {
        return [['country' => 'rus', 'region' => ['77', '50']]];
    }

    /**
     * @return array
     */
    public function requestedAddressFields()
    {
        return [
            'country' => ['hidden' => true, 'value' => 'rus'],
            'region'  => ['cost' => true, 'required' => true],
            'city'    => ['cost' => true, 'required' => true],
            'street'  => ['cost' => true, 'required' => true]
        ];
    }

    /**
     * @see waShipping::init()
     */
    protected function init()
    {
        require_once 'vendors/autoload.php';
        parent::init();
        waAutoload::getInstance()->add([
            'Syrnik\\dostavistaShipping\\Address' => "wa-plugins/shipping/dostavista/lib/classes/Address.class.php",
        ]);
    }

    /**
     * @param mixed $data
     */
    private function sendJsonData($data)
    {
        $response = array(
            'status' => 'ok',
            'data'   => $data,
        );
        $this->sendJsonResponse($response);
    }

    /**
     * @param mixed $response
     */
    private function sendJsonResponse($response)
    {
        if (waRequest::isXMLHttpRequest()) {
            wa()->getResponse()->addHeader('Content-Type', 'application/json')->sendHeaders();
        }
        echo json_encode($response);
        exit;
    }

    /**
     * @param mixed $error
     */
    private function sendJsonError($error)
    {
        $response = array(
            'status' => 'fail',
            'errors' => array($error),
        );
        $this->sendJsonResponse($response);
    }

    /**
     * @return string
     */
    protected function getDostavistaApiUrl()
    {
        return $this->api_server === 'production' ? 'https://robot.dostavista.ru/api/business/1.1' : 'https://robotapitest.dostavista.ru/api/business/1.1';
    }

    /**
     * @param $method
     * @param $http_method
     * @param array $data
     * @param array $options
     * @return array
     * @throws waException
     */
    public function queryDostavistaApi($method, $http_method, array $data = [], array $options = [])
    {
        $url = ifset($options, 'api_url', $this->getDostavistaApiUrl()) . "/$method";
        $token = ifset($options, 'token', $this->token);

        $headers = ['X-DV-Auth-Token' => $token] + (array)ifset($options, 'headers', []);

        return (new waNet(['format' => waNet::FORMAT_JSON, 'expected_http_code' => '200,400'], $headers))->query($url, $data, $http_method);
    }

    /**
     * @param Address $address
     * @return array|bool
     * @throws waException
     */
    protected function addressValidationErrors(Address $address)
    {
        if (($address_validation = $address->validate()) !== true) {
            if (!is_array($address_validation)) {
//                $this->logProcess('Проверка адреса вернула неожиданное значение: {data}', ['data' => var_export($address_validation, true)], 'flush');
                return false;
            }
            if ($address_validation['code'] === Address::ERR_VALIDATION_FATAL_RECOVERABLE) {
//                $this->logProcess('Исправимая ошибка проверки адреса: {msg}', ['msg' => $address_validation['message']], 'flush');
                return [['rate' => null, 'comment' => $address_validation['message']]];
            }
//            $this->logProcess('Проверка полей дреса не пройдена: {data}', ['data' => var_export($address_validation, true)], 'flush');
            throw new waException('Ошибка проверки полей адреса');
        }

        return true;
    }

    /**
     * @return float
     */
    protected function getTotalWeight()
    {
        return max(1, ceil(parent::getTotalWeight()));
    }

    /**
     * @param array $params
     * @return string
     */
    public function getSettingsHTML($params = array())
    {
        $errors = [];
        $settings = $this->getSettings();
        $info = array(
            'version'                 => $this->getProperties('version'),
            'build'                   => $this->getProperties('build'),
            'name'                    => $this->getProperties('name'),
            'namespace'               => (string)Hash::get($params, 'namespace'),
            'use_address_suggestions' => $this->isDadataAppReady(),
            'url'                     => ['autocomplete' =>
                                              wa()->getRouteUrl(
                                                  sprintf("%s/frontend/shippingPlugin", $this->app_id),
                                                  ['plugin_id' => $this->key, 'action_id' => 'dispatchAutocomplete'],
                                                  true)],
            'callback_support'        => $this->hasBackendSettingsSupport(),
            'callback_url'            => $this->getBackendSettingsCallbackUrl()
        );
        /* // размеры плагин не использует
                try {
                    $info['dimensions'] = $this->getAppDimensionSupport();
                } catch (waException $e) {
                    $info['dimensions'] = 'not_supported';
                    $errors[] = ['code' => $e->getCode(), 'message' => $e->getMessage()];
                }
        */
        $view = $this->getView();
        $view->assign(compact('settings', 'info', 'errors'));
        $view->assign(['plugin_id' => $this->id, 'plugin_js_object' => 'ShippingDostavistaPluginSettings']);

        return $view->fetch($this->path . '/templates/settings.html');
    }

    /**
     * @param bool $clean
     * @return waSmarty3View
     */
    private function getView($clean = false)
    {

        if ($clean || !$this->_view) {
            $view = new waSmarty3View($this->getSystem());
            if (!$clean) {
                $this->_view = $view;
            }

            return $view;
        }

        return $this->_view;
    }

    /**
     * @return waSystem
     */
    private function getSystem()
    {
        if ($this->_system) {
            return $this->_system;
        }

        $this->_system = wa();
        return $this->_system;
    }

    /**
     * @return bool
     */
    private function isDadataAppReady()
    {
        try {
            wa('alldadata');
        } catch (Exception $e) {
            return false;
        }

        return (new alldadataApi)->tokenAvailable();
    }

    /**
     * В плагине есть поддержка для колбэков из настроек
     *
     * @return bool
     */
    private function hasBackendSettingsSupport()
    {
        return $this->app_id === 'shop';
    }

    /**
     * @return string|null
     */
    private function getBackendSettingsCallbackUrl()
    {
        return $this->hasBackendSettingsSupport() ? "?module=settings&action=shippingSetup&plugin_id={$this->id}" : null;
    }

    /**
     * Приводит содержимое настройки с интервалами к нужному типу
     *
     * @param array $data
     * @return array
     */
    private function castCustomerInterval(array $data)
    {
        $data['date'] = (bool)Hash::get($data, 'date');
        $data['interval'] = (bool)Hash::get($data, 'interval');
        $_intervals = (array)Hash::get($data, 'intervals');
        $data['intervals'] = array_map(function ($v) {
            $v['from'] = sprintf('%02d', max(1, min(23, (int)Hash::get($v, 'from'))));
            $v['to'] = sprintf('%02d', max(1, min(23, (int)Hash::get($v, 'to'))));
            $v['from_m'] = sprintf('%02d', max(0, min(59, (int)Hash::get($v, 'from_m'))));
            $v['to_m'] = sprintf('%02d', max(0, min(59, (int)Hash::get($v, 'to_m'))));
            $v['workday'] = (bool)Hash::get($v, 'workday');
            $v['holiday'] = (bool)Hash::get($v, 'holiday');
            $v['day'] = array_map('intval', (array)Hash::get($v, 'day'));
            return $v;
        }, $_intervals);

        return $data;
    }

    /**
     * @param null $name
     * @return array|string|void
     */
    public function getSettings($name = null)
    {
        $settings = parent::getSettings($name);

        $castingFn = function ($key, $data) {
            switch ($key) {
                case 'customer_interval':
                    $data = $this->castCustomerInterval((array)$data);
                    break;
                case 'exact_delivery_time':
                    $data = (int)$data;
                    break;
            }

            return $data;
        };

        if ($name === null) {
            foreach ($settings as $key => $setting) {
                $settings[$key] = $castingFn($key, $setting);
            }
        } else {
            $settings = $castingFn($name, $settings);
        }

        return $settings;
    }

    /**
     * @param array $settings
     * @return array
     * @throws waException
     */
    public function saveSettings($settings = array())
    {
        if (isset($settings['customer_interval'])) {
            $settings['customer_interval'] = $this->castCustomerInterval((array)$settings['customer_interval']);
        }
        return parent::saveSettings($settings);
    }

    /**
     * @param waOrder $order
     * @return array
     * @throws waException
     */
    public function customFields(waOrder $order)
    {
        $fields = parent::customFields($order);
        $setting = $this->customer_interval;

        if (!empty($this->customer_interval['interval']) || !empty($this->customer_interval['date'])) {
            $from = (!strlen($this->delivery_time) || ($this->delivery_time === 'exact_delivery_time')) ? time() : strtotime(preg_replace('/,.+$/', '', $this->delivery_time));
            $offset = max(0, round(($from - time()) / (24 * 3600)));
            $shipping_params = $order->shipping_params;
            $value = array();

            if (!empty($shipping_params['desired_delivery.interval'])) {
                $value['interval'] = $shipping_params['desired_delivery.interval'];
            }
            if (!empty($shipping_params['desired_delivery.date_str'])) {
                $value['date_str'] = $shipping_params['desired_delivery.date_str'];
            }
            if (!empty($shipping_params['desired_delivery.date'])) {
                $value['date'] = $shipping_params['desired_delivery.date'];
            }

            if (!empty($setting['intervals'])) {
                $delivery_times = $this->getDeliveryTimes();
                foreach ($setting['intervals'] as &$interval) {
                    $interval = $this->getInterval($interval, $delivery_times['timestamp']);
                }
                unset($interval);
            }

            $params = [
                'date'      => empty($this->customer_interval['date']) ? null : (int)$offset,
                'interval'  => ifset($setting['interval']),
                'intervals' => ifset($setting['intervals']),
                'holidays'  => $this->holidays,
                'workdays'  => $this->workdays
            ];

            $fields['desired_delivery'] = [
                'value'        => $value,
                'title'        => 'Желательное время доставки',
                'control_type' => waHtmlControl::DATETIME,
                'params'       => $params
            ];

        }

        return $fields;
    }

    /**
     * @return array{timestamp:int|array<int>, estimate:string}
     * @throws waException
     */
    protected function getDeliveryTimes()
    {
        if (!$this->delivery_time) {
            return ['timestamp' => null, 'estimate' => null];
        }

        /** @var string $departure_datetime SQL DATETIME */
        $departure_datetime = $this->getPackageProperty('departure_datetime');

        $departure_timestamp = $departure_datetime ? max(0, strtotime($departure_datetime) - $this->getSchedule()['time']) : 0;

        if ('exact_delivery_time' === $this->delivery_time) {
            $delivery_date = [
                $this->getSchedule()['time'] + max(0, $this->exact_delivery_time) * 3600 + $departure_timestamp,
            ];
        } else {
            $delivery_date = array_map('strtotime', explode(',', $this->delivery_time, 2));
            foreach ($delivery_date as & $date) {
                $date += $departure_timestamp;
            }
            unset($date);
            $delivery_date = array_unique($delivery_date);
        }

        $est_delivery = array();
        foreach ($delivery_date as $date) {
            $est_delivery[] = waDateTime::format('humandate', $date);
        }
        $est_delivery = implode(' — ', $est_delivery);

        if (count($delivery_date) == 1) {
            $delivery_date = reset($delivery_date);
        }

        return array(
            'timestamp' => $delivery_date,
            'estimate'  => $est_delivery,
        );
    }

    /**
     * @return array{time:int, holidays:array<string>, workdays:array<string>}
     */
    protected function getSchedule()
    {
        if ($this->_schedule === null) {
            $this->_schedule['holidays'] = $this->holidays;
            $this->_schedule['workdays'] = $this->workdays;
            $this->_schedule['time'] = time();
        }

        return $this->_schedule;
    }

    /**
     * @param array{
     *          from: string,
     *          from_m: string,
     *          to: string,
     *          to_m: string,
     *          day: array<int>,
     *          workday: bool,
     *          holiday: bool
     *      } $interval
     * @param int $timestamp
     * @return array{
     *     offset: int,
     *     from: string,
     *     to: string,
     *     interval: string,
     *     days: array,
     *     start_date: string
     * }
     */
    protected function getInterval($interval, $timestamp)
    {
        $result = [
            'offset' => 0,
            'from'   => sprintf('%02d:%02d', $interval['from'], $interval['from_m']),
            'to'     => sprintf('%02d:%02d', $interval['to'], $interval['to_m'])
        ];
        $result['interval'] = sprintf('%s-%s', $result['from'], $result['to']);

        $start = is_array($timestamp) ? reset($timestamp) : $timestamp;

        do {
            $service_datetime = strtotime(sprintf('+%d days', $result['offset']++), $start);
            $service_date = date('Y-m-d', $service_datetime);
            $week_day = date('N', $service_datetime);
            $is_holiday = in_array($service_date, $this->holidays, true);
            $is_extra_holiday = $is_holiday && $interval['holiday'];

            if (!$is_holiday || $is_extra_holiday) {

                $is_extra_workday = $interval['workday'] && in_array($service_date, $this->workdays, true);
                $is_workday = $is_extra_holiday || $is_extra_workday || in_array($week_day, $interval['day']);

                if ($is_workday) {
                    $is_same_day = date('Y-m-d', $this->time) === $service_date;
                    if ($is_same_day) {
                        if ((int)date('H', $this->time) >= (int)$interval['to']) {
                            continue;
                        }
                    }
                    $service_delivery_date = $service_date;
                    $service_delivery_date .= sprintf(' %02d:00', $interval['from']);
                }
            }
            if ($result['offset'] > 60) {
                break;
            }
        } while (empty($service_delivery_date));

        $_days = [];
        foreach ($interval['day'] as $key => $value) {
            $_days[$value - 1] = 1;
        }

        $result['day'] = $_days;

        $result['start_date'] = date('Y-m-d', strtotime($service_delivery_date));
        if ($interval['holiday']) {
            $result['day']['holiday'] = 1;
        }

        if ($interval['workday']) {
            $result['day']['workday'] = 1;
        }

        return $result;
    }
}
