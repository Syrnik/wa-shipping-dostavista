<?php
/**
 * @author Serge Rodovnichenko <serge@syrnik.com>
 * @copyright (c) 2019, Serge Rodovnichenko
 * @license http://www.webasyst.com/terms/#eula Webasyst
 */

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
    /** @var null|array{time:int, holidays:array<string>, workdays:array<string>} */
    protected ?array $_schedule = null;

    protected dostavistaShippingCache $cache;

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
        return [['country' => 'rus']];
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
            'version'   => $this->getProperties('version'),
            'build'     => $this->getProperties('build'),
            'name'      => $this->getProperties('name'),
            'namespace' => $params['namespace'] ?? '',
        );
        $view = new waSmarty3View(wa());
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
                        $data = WaShippingUtils::toFloat($data, true);
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
        $data['date'] = boolval($data['date'] ?? false);
        $data['interval'] = boolval($data['interval'] ?? false);
        $_intervals = (array)($data['intervals'] ?? []);
        $data['intervals'] = array_map(function ($v) {
            $v['from'] = sprintf('%02d', max(1, min(23, intval($v['from'] ?? 1))));
            $v['to'] = sprintf('%02d', max(1, min(23, intval($v['to'] ?? 1))));
            $v['from_m'] = sprintf('%02d', max(0, min(59, intval($v['from_m'] ?? 0))));
            $v['to_m'] = sprintf('%02d', max(0, min(59, intval($v['to_m'] ?? 0))));
            $v['workday'] = boolval($v['workday'] ?? false);
            $v['holiday'] = boolval($v['holiday'] ?? false);
            $v['day'] = array_map('intval', (array)($v['day'] ?? []));
            return $v;
        }, $_intervals);

        return $data;
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
        $service_delivery_date = '';

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
//        $limits_checker = new dostavistaShippingLimitsChecker($this);
//        if (!$limits_checker->isAllowed()) return [['rate' => null, 'currency' => 'RUB', 'comment' => $limits_checker->getMessage()]];

        $calculation_order = $this->createDostavistaOrder();
        if (!($response = $this->getCache()->getCalculation($calculation_order, $this->token, 'test' === $this->api_server))) {
            $response = (new dostavistaShippingApi($this->token, 'test' === $this->api_server))
                ->CalculateOrder($calculation_order);
            $this->getCache()->saveCalculation($response, $calculation_order, $this->token, 'test' === $this->api_server);
        }

        if ($error = $this->getError($response))
            return [['rate' => null, 'comment' => $error]];


        $result = [
            'dostavista_courier' => [
                'rate'     => round((float)$response['order']['payment_amount'], 2),
                'currency' => 'RUB',
                'type'     => waShipping::TYPE_TODOOR
            ]
        ];

        if ($destination_address = $response['order']['points'][1]['address'] ?? null) {
            $result['dostavista_courier']['comment'] = "курьером по адресу: " . waString::escapeAll($destination_address);
            $result['dostavista_courier']['custom_data'][waShipping::TYPE_TODOOR]['additional'] = "Доставка курьером по адресу: " . waString::escapeAll($destination_address);
        }


        try {
            $delivery_times = $this->getDeliveryTimes();
        } catch (waException $e) {
            return 'Ошибка расчёта';
        }
        $result['dostavista_courier']['est_delivery'] = $delivery_times['estimate'];

        $setting = $this->customer_interval;
        if (!empty($setting['intervals'])) {
            $intervals = [];
            try {
                $date_format = waDateTime::getFormat('date');
            } catch (waException $e) {
                $date_format = 'd.m.Y';
            }
            $offset = null;
            $delivery = null;
            $placeholder = "";

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

                    if ((null === $offset) || ($offset > $interval['offset'])) {
                        $offset = $interval['offset'];
                    }
                }
            }

            try {
                if ($delivery ?? false)
                    $placeholder = waDateTime::format($date_format, $delivery['delivery_date']);
            } catch (waException $e) {

            }
            $custom_data = array(
                'offset'      => $offset,
                'intervals'   => $intervals,
                'placeholder' => $placeholder,
                'holidays'    => $this->holidays,
                'workdays'    => $this->workdays,
            );
            $result['dostavista_courier']['custom_data'][waShipping::TYPE_TODOOR] =
                ($result['dostavista_courier']['custom_data'][waShipping::TYPE_TODOOR] ?? []) + $custom_data;
        }

        return $result;
    }

    protected function getError(array $response): ?string
    {
        if (!$response['is_successful']) return 'Доставка по указанному адресу невозможна';
        if ($warnings = $response['warnings'] ?? []) {
            if (!in_array('invalid_parameters', $warnings)) {
                return 'Доставка по указанному адресу невозможна';
            }
        }
        if (($parameter_warnings = $response['parameter_warnings'] ?? []) || in_array('invalid_parameters', $warnings)) {
            return 'Доставка по указанному адресу невозможна';
        }

        return null;
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
        $searchable_city = WaShippingUtils::replaceYo(trim(mb_strtolower($city, 'UTF-8')));
        if (
            ('москва' === $searchable_city && in_array($address['region'], ['77', '50'])) ||
            ('санкт-петербург' === $searchable_city && in_array($address['region'], ['78', '47']))
        ) $region_name = '';
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

    protected function createDostavistaOrder(): dostavistaShippingApiEntityOrder
    {
        $order = new dostavistaShippingApiEntityOrder;
        $order->setMatter('Shopping');
        $order->setTotalWeight((int)max(1, ceil((float)$this->getTotalWeight())));
        $start_point = (new dostavistaShippingApiEntityPoint)
            ->setAddress($this->location_from['name'])
            ->setContactPerson(new dostavistaShippingApiEntityContactPerson('Отправитель', '79999999999'));
        $destination_point = (new dostavistaShippingApiEntityPoint)
            ->setAddress($this->stringifyAddress())
            ->setContactPerson(new dostavistaShippingApiEntityContactPerson('Отправитель', '79999999999'));

        $order->setInsuranceAmount(new dostavistaShippingApiEntityMoney((float)$this->getTotalRawPrice()));

        if ($this->isCashOnDeliverySelected())
            $destination_point->setTakingAmount(new dostavistaShippingApiEntityMoney((float)$this->getTotalPrice()));

        $order->setPoints($start_point, $destination_point);

        return $order;
    }

    protected function init()
    {
        require_once 'vendors/autoload.php';
        parent::init();
    }

    /**
     * Истина, если заказ с оплатой при получении. Учитывает настройки плагина
     *
     * @return bool
     */
    public function isCashOnDeliverySelected(): bool
    {
        $selected = $this->getSelectedPaymentTypes();
        return !in_array(waShipping::PAYMENT_TYPE_PREPAID, $selected, true);
    }

    /**
     * @return dostavistaShippingCache
     * @throws waException
     */
    public function getCache(): dostavistaShippingCache
    {
        if (isset($this->cache)) return $this->cache;
        $cache = wa()->getCache('dostavista_shipping', 'webasyst');
        if (!$cache) $cache = wa()->getCache('default', 'webasyst');
        if (!$cache) $cache = new waCache(new waFileCacheAdapter([]), 'webasyst');

        return new dostavistaShippingCache($cache);
    }
}
