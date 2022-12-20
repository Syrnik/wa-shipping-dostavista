<?php
/**
 * @author Serge Rodovnichenko <serge@syrnik.com>
 * @copyright (c) 2019, Serge Rodovnichenko
 * @license http://www.webasyst.com/terms/#eula Webasyst
 */

require_once 'classes/InstanceCache.trait.php';
require_once 'classes/LoggerForShippingPlugins.trait.php';

use Psr\Log\LogLevel;
use SergeR\CakeUtility\Hash;
use Syrnik\dostavistaShipping\Address;
use Syrnik\dostavistaShipping\InstanceCache;
use Syrnik\dostavistaShipping\LoggerForShippingPlugins;
use Syrnik\dostavistaShipping\Surcharge;
use Syrnik\WaShippingUtils;
use Webit\Util\EvalMath\EvalMath;

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
 * @property-read bool $cash_on_delivery
 * @property-read array{type:string, value:string] $insurance
 * @property-read array<string> $holidays
 * @property-read array<string> $workdays
 * @property-read array{type:string, value:string} $location_rule
 * @property-read bool $detailed_log
 * @property-read array{client:bool, receiver:string} $sms_notify
 */
class dostavistaShipping extends waShipping
{
    use InstanceCache, LoggerForShippingPlugins;

    /** @var waSmarty3View|null */
    protected $_view;

    /** @var waSystem|null */
    protected $_system;

    /** @var null|array{time:int, holidays:array<string>, workdays:array<string>} */
    protected $_schedule;

    /**
     * @return string
     */
    public function allowedCurrency(): string
    {
        return 'RUB';
    }

    /**
     * @return string
     */
    public function allowedWeightUnit(): string
    {
        return 'kg';
    }

    /**
     * @return array
     */
    public function allowedAddress(): array
    {
        return [['country' => 'rus', 'region' => ['77', '50']]];
    }

    /**
     * @return array
     */
    public function requestedAddressFields(): array
    {
        return [
            'country' => ['hidden' => true, 'value' => 'rus'],
            'region'  => ['cost' => true, 'required' => true],
            'city'    => ['cost' => true, 'required' => true],
            'street'  => ['cost' => true, 'required' => true]
        ];
    }

    /**
     * @param array $params
     * @return string
     * @throws SmartyException
     * @throws waException
     */
    public function getSettingsHTML($params = array()): string
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
     * @param null $name
     * @return array|string|void
     */
    public function getSettings($name = null)
    {
        $settings = parent::getSettings($name);

        if ($name === null) {
            $settings = $this->_typecastSettings($settings);
        } else {
            $settings = $this->_typecastSettings([$name => $settings])[$name];
        }

        return $settings;
    }

    /**
     * @param array $settings
     * @return array
     */
    protected function _typecastSettings(array $settings): array
    {
        foreach ($settings as $key => $data) {
            switch ($key) {
                case 'customer_interval':
                    $data = $this->castCustomerInterval((array)$data);
                    break;
                case 'location_rule':
                    $type = (string)ifset($data, 'type', 'except');
                    $value = (string)ifset($data, 'value', '');
                    $type = trim(strtolower($type));
                    if (!in_array($type, ['except', 'only'])) {
                        $type = 'except';
                    }
                    $data = ['type' => $type, 'value' => WaShippingUtils::mb_trim($value)];
                    break;
                case 'exact_delivery_time':
                    $data = (int)$data;
                    break;
                case 'cash_on_delivery':
                case 'detailed_log':
                    $data = (bool)$data;
                    break;
                case 'free_delivery':
                    if (is_string($data)) {
                        $data = trim($data);
                        $data = strlen($data) ? WaShippingUtils::strToFloat($data) : null;
                    }
                    break;
                case 'surcharge':
                    $data = trim($data);
                    break;
                case 'sms_notify':
                    $data = (array)$data;
                    $client = (bool)ifset($data, 'client', false);
                    $receiver = (string)ifset($data, 'receiver', 'no');
                    if (!in_array($receiver, ['no', 'yes', 'ask_no', 'ask_yes'])) {
                        $receiver = 'no';
                    }
                    $data = ['client' => $client, 'receiver' => $receiver];
                    break;
            }
            $settings[$key] = $data;
        }

        return $settings;
    }

    /**
     * Приводит содержимое настройки с интервалами к нужному типу
     *
     * @param array $data
     * @return array
     */
    private function castCustomerInterval(array $data): array
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
     * @return bool
     */
    private function isDadataAppReady(): bool
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
    private function hasBackendSettingsSupport(): bool
    {
        return $this->app_id === 'shop';
    }

    /**
     * @return string|null
     */
    private function getBackendSettingsCallbackUrl(): ?string
    {
        return $this->hasBackendSettingsSupport() ? "?module=settings&action=shippingSetup&plugin_id=$this->id" : null;
    }

    /**
     * @param bool $clean
     * @return waSmarty3View
     * @throws SmartyException
     * @throws waException
     * @deprecated
     */
    private function getView(bool $clean = false): waSmarty3View
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
     * @throws waException
     * @deprecated
     */
    private function getSystem(): waSystem
    {
        if ($this->_system) {
            return $this->_system;
        }

        $this->_system = wa();
        return $this->_system;
    }

    /**
     * @param array $settings
     * @return array
     * @throws waException
     */
    public function saveSettings($settings = array()): array
    {
        if (isset($settings['customer_interval'])) {
            $settings['customer_interval'] = $this->castCustomerInterval((array)$settings['customer_interval']);
        }
        $settings['cash_on_delivery'] = (bool)ifset($settings, 'cash_on_delivery', false);

        $settings = $this->_typecastSettings($settings);

        return parent::saveSettings($settings);
    }

    /**
     * @param waOrder $order
     * @return array
     * @throws waException
     * @noinspection DuplicatedCode
     */
    public function customFields(waOrder $order): array
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
                'title'        => 'Желаемое время доставки',
                'control_type' => waHtmlControl::DATETIME,
                'params'       => $params
            ];

        }

//        if (in_array($this->sms_notify['receiver'], ['ask_yes', 'ask_no'])) {
//            $fields['sms_notification'] = [
//                'title'        => 'SMS уведомление',
//                'label'        => 'Отправить SMS с интервалом прибытия и телефоном курьера',
//                'control_type' => waHtmlControl::CHECKBOX,
//                'value'        => ifset($shipping_params, 'sms_notification', '0') ? '1' : '0',
//                'data'         => ['affects-rate' => true]
//            ];
//        }

        return $fields;
    }

    /**
     * @return array{timestamp:int|array<int>, estimate:string}
     * @throws waException
     */
    protected function getDeliveryTimes(): array
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
    protected function getSchedule(): array
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
     * @param int|array $timestamp
     * @return array{
     *     offset: int,
     *     from: string,
     *     to: string,
     *     interval: string,
     *     days: array,
     *     start_date: string
     * }
     * @noinspection DuplicatedCode
     */
    protected function getInterval(array $interval, $timestamp): array
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
        foreach ($interval['day'] as $value) {
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

    /**
     * @return array|string
     */
    protected function calculate()
    {
        $this->startLogger(waSystemConfig::isDebug() ? ($this->detailed_log ? LogLevel::DEBUG : LogLevel::INFO) : LogLevel::ALERT);
        $this->logProcess('start');

        $limits_checker = new dostavistaShippingLimitsChecker($this);
        if (!$limits_checker->isAllowed()) return [['rate' => null, 'currency' => 'RUB', 'comment' => $limits_checker->getMessage()]];

        $address = new Address($this->getAddress());
        $this->logProcess('address', ['data' => ['address' => $address]]);

        $query = [
                'matter'                                 => 'Shopping',
                'total_weight_kg'                        => (int)$this->getTotalWeight(),
                'is_client_notification_enabled'         => $this->sms_notify['client'],
                'is_contact_person_notification_enabled' => $this->getContactPersonNotificationOption(),
                'points'                                 => [
                    [
                        'address' => $this->location_from['name']
                    ],
                    [
                        'address' => $this->stringifyAddress()
                    ] + $this->getCashOnDeliveryQueryField()
                ]
            ]
            + $this->getInsuranceQueryField();

        $this->logProcess('json_dump', ['message' => "Данные для запроса\n{json}", 'data' => $query]);

        $cache = $this->getInstanceCache();
        $cache_group = $this->getInstanceCacheGroup('calc');
        $cache_key = $this->getInstanceCacheKeyForCalc($query);

        $response = $cache->get($cache_key, $cache_group);
        if (!is_array($response)) {
            $this->logProcess('start_timer', ['data' => ['name' => 'calc']]);
            try {
                $response = $this->queryDostavistaApi('calculate-order', waNet::METHOD_POST, $query);
            } catch (Exception $e) {
                $this->logProcess('exception', ['data' => ['exception' => $e], 'loglevel' => LogLevel::CRITICAL]);
                $this->logProcess('flush', ['data' => ['message' => 'Ошибка при выполнении запроса к серверу. Окончание']]);
                return 'Ошибка расчёта';
            }
            $this->logProcess('end_timer', ['data' => ['name' => 'calc'], 'message' => 'Запрос к серверу Dostavista выполнен за {total}с.', 'loglevel' => LogLevel::INFO]);
            $cache->set($cache_key, $response, 600, $cache_group);
        } else {
            $this->logProcess('custom', ['message' => 'Результат расчёта извлечён из кэша', 'loglevel' => LogLevel::INFO]);
        }

        $this->logProcess('json_dump', ['message' => "Ответ сервера Dostavista:\n{json}", 'data' => $response]);

        if (!$response['is_successful']) {
            $this->logProcess('flush', ['message' => 'Расчёт доставки не удался', 'loglevel' => LogLevel::INFO]);
            return [
                'rate'    => null,
                'comment' => 'Доставка по указанному адресу невозможна'
            ];
        }

        $result = [
            'dostavista_courier' => [
                'rate'     => round((float)Hash::get($response, 'order.payment_amount'), 2),
                'currency' => 'RUB',
                'type'     => waShipping::TYPE_TODOOR
            ]
        ];

        $surcharge = (new Surcharge([
            'CalculatedDeliveryCost' => $result['dostavista_courier']['rate'],
            'OrderTotal'             => $this->getTotalPrice(),
            'OrderRawTotal'          => $this->getTotalRawPrice(),
            'FreeDelivery'           => $this->getSettings('free_delivery')
        ]))->setFormula($this->getSettings('surcharge'));
        $this->logProcess('surcharge', ['data' => ['surcharge' => $surcharge]]);

        try {
            $result['dostavista_courier']['rate'] = $surcharge->calculate();
            $this->logProcess('custom', ['message' => 'Итоговая стоимость доставки: {price}', 'data' => ['price' => number_format($result['dostavista_courier']['rate'], 2, '.', '')], 'loglevel' => LogLevel::INFO]);
        } catch (waException $e) {
            $this->logProcess('exception', ['data' => ['exception' => $e]]);
            $this->logProcess('flush', ['message' => 'Расчёт прерван']);
            return 'Ошибка расчёта';
        }

        if (($destination_address = (string)Hash::get($response, 'order.points.1.address'))) {
            $result['dostavista_courier']['comment'] = "курьером по адресу: " . waString::escapeAll($destination_address);
            $result = Hash::insert(
                $result,
                'dostavista_courier.custom_data.' . waShipping::TYPE_TODOOR,
                ['additional' => "Доставка курьером по адресу: " . waString::escapeAll($destination_address)]
            );
        }

        try {
            $delivery_times = $this->getDeliveryTimes();
        } catch (waException $e) {
            $this->logProcess('exception', ['data' => ['exception' => $e], 'loglevel' => LogLevel::ERROR]);
            $this->logProcess('flush', ['message' => 'Расчёт прерван']);
            return 'Ошибка расчёта';
        }
        $result['dostavista_courier']['est_delivery'] = $delivery_times['estimate'];

        $setting = $this->customer_interval;
        if (!empty($setting['intervals'])) {
            $intervals = [];
            try {
                $date_format = waDateTime::getFormat('date');
            } catch (waException $e) {
                $this->logProcess('exception', ['data' => ['exception' => $e], 'loglevel' => LogLevel::ERROR]);
                $date_format = 'd.m.Y';
            }
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

            try {
                $placeholder = waDateTime::format($date_format, $delivery['delivery_date']);
            } catch (waException $e) {
                $this->logProcess('exception', ['data' => ['exception' => $e], 'loglevel' => LogLevel::ERROR]);
                $placeholder = "";
            }
            $custom_data = array(
                'offset'      => $offset,
                'intervals'   => $intervals,
                'placeholder' => $placeholder,
                'holidays'    => $this->holidays,
                'workdays'    => $this->workdays,
            );
            $result['dostavista_courier']['custom_data'][waShipping::TYPE_TODOOR] = (array)Hash::get($result, 'dostavista_courier.custom_data.' . waShipping::TYPE_TODOOR) + $custom_data;
        }
        $this->logProcess('flush');

        return $result;
    }

    /**
     * @param $field
     * @return array|mixed|null
     */
    public function getAddress($field = null)
    {
        return parent::getAddress($field);
    }

    /**
     * @return float
     */
    protected function getTotalWeight(): float
    {
        return max(1, ceil(parent::getTotalWeight()));
    }

    /**
     * Потом сделаем возможность выбора покупателем
     *
     * @return bool
     */
    private function getContactPersonNotificationOption(): bool
    {
        if ($this->sms_notify['receiver'] == 'yes') {
            return true;
        }

        return false;
    }

    /**
     * @return string
     */
    protected function stringifyAddress(): string
    {
        $address = $this->getAddress();
        $city = trim($address['city'] ?? '');
        $searchable_city = WaShippingUtils::replaceYo(WaShippingUtils::mb_trim(mb_strtolower($city, 'UTF-8')));
        if ('москва' === $searchable_city) $region_name = '';
        else {
            $region_data = (new waRegionModel)->get('rus', $address['region']);
            $region_name = $region_data['name'] ?? '';
        }

        return implode(', ', array_filter([
            trim($address['zip'] ?? ''),
            $region_name,
            $city,
            trim($address['street'] ?? '')
        ]));
    }

    /**
     * Возвращает сумму наложки для получения с клиента в виде массива для добавления к point
     * при расчёте
     *
     * @return array
     */
    protected function getCashOnDeliveryQueryField(): array
    {
        return $this->cash_on_delivery ? ['taking_amount' => number_format($this->getTotalPrice(), 2, '.', '')] : [];
    }

    /**
     * Настройка с оценочной стоимостью (стоимостью страховки)
     * Выдаёт массив со значением для добавления к запросу
     *
     * @return array
     */
    protected function getInsuranceQueryField(): array
    {
        switch ($this->insurance['type']) {
            case 'raw':
                return ['insurance_amount' => number_format($this->getTotalRawPrice(), 2, '.', '')];
            case 'total':
                return ['insurance_amount' => number_format($this->getTotalPrice(), 2, '.', '')];
            case 'custom':
                $formula = trim($this->insurance['value']);
                if (!strlen($formula)) {
                    break;
                }
                $formula = strtolower($formula);
                $formula = str_replace(',', '.', $formula);
                if ((strpos($formula, 'w') === false) && (strpos($formula, 't') === false)) {
                    return ['insurance_amount' => number_format((float)$formula, 2, '.', '')];
                }
                $math = new EvalMath();
                $math->suppress_errors = true;
                $math->evaluate('w=' . str_replace(',', '.', $this->getTotalRawPrice()));
                $math->evaluate('t=' . str_replace(',', '.', $this->getTotalPrice()));
                $value = $math->evaluate($formula);
                if ($value === false) {
                    //todo log
                }
                return ['insurance_amount' => number_format((float)$value, 2, '.', '')];
        }

        return [];
    }

    /**
     * Изготавливает ключ для кэширования результата запроса на расчёт
     *
     * @param $query
     * @return string
     */
    private function getInstanceCacheKeyForCalc($query): string
    {
        $query['points'] = array_map(function ($v) {
            $v['address'] = WaShippingUtils::mb_trim(mb_strtolower($v['address'], 'UTF-8'));
            return $v;
        }, $query['points']);

        return md5(waUtils::jsonEncode($query));
    }

    /**
     * @param $method
     * @param $http_method
     * @param array $data
     * @param array $options
     * @return array
     * @throws waException
     */
    public function queryDostavistaApi($method, $http_method, array $data = [], array $options = []): array
    {
        $url = ifset($options, 'api_url', $this->getDostavistaApiUrl()) . "/$method";
        $token = ifset($options, 'token', $this->token);

        $headers = ['X-DV-Auth-Token' => $token] + (array)ifset($options, 'headers', []);

        return (new waNet(['format' => waNet::FORMAT_JSON, 'expected_http_code' => '200,400'], $headers))->query($url, $data, $http_method);
    }

    /**
     * @return string
     */
    protected function getDostavistaApiUrl(): string
    {
        return $this->api_server === 'production' ? 'https://robot.dostavista.ru/api/business/1.1' : 'https://robotapitest.dostavista.ru/api/business/1.1';
    }

    /**
     * @see waShipping::init()
     * @todo Убрать это. Refactor!
     */
    protected function init()
    {
        require_once 'vendors/autoload.php';
        parent::init();
        waAutoload::getInstance()->add([
            'Syrnik\\dostavistaShipping\\Address'               => "wa-plugins/shipping/dostavista/lib/classes/Address.class.php",
            'Syrnik\\dostavistaShipping\\Surcharge'             => "wa-plugins/shipping/dostavista/lib/classes/Surcharge.class.php",
            'Syrnik\\dostavistaShipping\\LoggerActionFormatter' => "wa-plugins/shipping/dostavista/lib/classes/LoggerActionFormatter.class.php"
        ]);
    }
}
