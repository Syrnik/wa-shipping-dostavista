<?php
/**
 * @author Serge Rodovnichenko <serge@syrnik.com>
 * @copyright Serge Rodovnichenko, 2019
 * @license Webasyst
 */

namespace Syrnik\dostavistaShipping;

use Exception;
use Psr\Log\LogLevel;

/**
 * Class LoggerActionFormatter
 * @package Syrnik\neograstinShipping
 */
class LoggerActionFormatter
{
    public static function __callStatic($name, $arguments)
    {
        if ($arguments && isset($arguments[0]) && is_array($arguments[0])) {
            return $arguments[0];
        }
        return null;
    }

    public static function Start(array $options = [])
    {
        return ['message' => 'Начат расчёт стоимости доставки', 'loglevel' => LogLevel::INFO, 'data' => []];
    }

    /**
     * Логирует адрес
     *
     * @param array $options
     * @return array
     */
    public static function Address(array $options = [])
    {
        /** @var Address $a */
        $a = $options['data']['address'];
        $loglevel = ifset($options, 'data', 'loglevel', LogLevel::INFO);

        if ($loglevel == LogLevel::DEBUG) {
            return [
                'message'  => "Для расчёта получен адрес:\n\tСтрана: '{country}\n\tРегион: '{region}'\n\tГород: {city}\n\tИндекс: {zip}",
                'data'     => ['country' => $a->getCountry(), 'region' => $a->getRegion(), 'city' => $a->getCity(), 'zip' => $a->getZip()],
                'loglevel' => LogLevel::DEBUG
            ];
        }

        return [
            'message'  => 'Для расчёта получен адрес: {address}',
            'data'     => ['address' => (string)$a],
            'loglevel' => $loglevel
        ];
    }

    /**
     * @param array $options
     * @return array
     */
    public static function BannedLocationSetting(array $options = [])
    {
        $type = $options['data']['type'] == 'exact' ? 'только' : 'кроме';
        $rule = trim($options['data']['value']);

        if ($rule) {
            return [
                'message'  => 'Правило фильтра городов: *{type}* \'{rule}\'',
                'data'     => ['type' => $type, 'rule' => $rule],
                'loglevel' => isset($options['loglevel']) && $options['loglevel'] ? $options['loglevel'] : LogLevel::INFO
            ];
        }

        return [
            'message'  => 'Ограничения по городам нет',
            'data'     => [],
            'loglevel' => (isset($options['loglevel']) && $options['loglevel'] ? $options['loglevel'] : LogLevel::INFO)
        ];
    }

    public static function Surcharge(array $options = [])
    {
        $surcharge = ifset($options, 'surcharge', '');
        $loglevel = isset($options['loglevel']) && $options['loglevel'] ? $options['loglevel'] : LogLevel::DEBUG;
        if ($surcharge instanceof Surcharge) {
            $free_delivery = $surcharge->getFreeDelivery();
            $formula = $surcharge->getFormula();
            if (($free_delivery === null) && !strlen($formula)) {
                return [
                    'message'  => 'Порог бесплатной доставки и корректировка стоимости не заданы',
                    'data'     => [],
                    'loglevel' => $loglevel
                ];
            }
            return [
                'message'  => "Корректировка стоимости\n\t- Стоимость заказа: {total}\n\t- Стоимость заказа без скидок: {raw}\n\t- Рассчитанная стоимость доставки: {cost}\n\t- Бесплатный порог: {free}\n\t- Формула: {formula}",
                'data'     => [
                    'total'   => number_format($surcharge->getOrderTotal(), 2, '.', ''),
                    'raw'     => number_format($surcharge->getOrderRawTotal(), 2, '.', ''),
                    'cost'    => number_format($surcharge->getCalculatedDeliveryCost(), 2, '.', ''),
                    'free'    => ($free_delivery === null ? 'нет' : $free_delivery),
                    'formula' => (strlen($formula) ? $formula : 'нет')
                ],
                'loglevel' => $loglevel
            ];
        }

        return ['message' => 'Корерктировка стоимости не настроена', 'data' => [], 'loglevel' => LogLevel::ERROR];
    }

    /**
     * @param array $options
     * @return array
     */
    public static function Exception(array $options = [])
    {
        $e = ifset($options, 'data', 'exception', null);

        if ($e instanceof Exception) {
            $options['data']['code'] = $e->getCode();
            $options['data']['message'] = $e->getMessage();
            $options['data']['exception_class'] = get_class($e);

            $message = ifset($options, 'message', '');
            if (!$message) {
                $options['message'] = 'Получено исключение {exception_class}: {message} ({code})';
            }
        }

        $options['loglevel'] = ifset($options, 'loglevel', LogLevel::ERROR);

        return $options;
    }
}