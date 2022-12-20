<?php
/**
 * @author Serge Rodovnichenko <serge@syrnik.com>
 * @copyright Serge Rodovnichenko, 2022
 * @license Webasyst
 */

declare(strict_types=1);

use Syrnik\WaShippingUtils;

/**
 *
 */
class dostavistaShippingLimitsChecker
{
    /** @var dostavistaShipping */
    protected $plugin;

    /**
     * @var string
     */
    protected $message = '';

    /**
     * @param dostavistaShipping $plugin
     */
    public function __construct(dostavistaShipping $plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * @param array|null $address
     * @return void
     */
    public function isAllowed(?array $address = null): bool
    {
        if (null === $address) $this->plugin->getAddress();
        if (!$this->isAddressComplete($address)) return false;

        $logger = $this->plugin->getLogger();

        if (!$this->isLocationAllowed()) {
            if ($logger) $logger->info("Доставка по выбранному адресу запрещена настройкой ограничения географии");
            $this->message = "Нет подходящих вариантов доставки для указанного города";
        }
        return true;
    }

    /**
     * @param array|null $address
     * @return bool
     * @noinspection DuplicatedCode
     */
    public function isAddressComplete(?array $address = null): bool
    {
        if (null === $address) $this->plugin->getAddress();

        $empty_fields = [];
        if (!($country = $address['country'] ?? '')) $empty_fields[] = 'страну';
        if (!($address['region'] ?? '')) $empty_fields[] = 'регион';
        if (!(trim($address['city'] ?? ''))) $empty_fields[] = 'город';
        if ($empty_fields) {
            if (count($empty_fields) > 1)
                $fields = implode(', ', array_slice($empty_fields, 0, -1)) . ' и ' . array_pop($empty_fields);
            else
                $fields = array_pop($empty_fields);

            if ($this->plugin->getLogger())
                $this->plugin->getLogger()->info("Не указаны поля адреса: $fields");

            $this->message = "Для выполнения расчёта стоимости доставки укажите $fields";

            return false;
        }

        if ('rus' !== $country) {
            $this->message = "Расчёт доставки возможен только по Российской Федерации";
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    public function isLocationAllowed(): bool
    {
        /** @var array{type:string, locations:string} $setting */
        $setting = $this->plugin->getSettings('location_limits');

        if (!$setting['locations']) return true;

        $matched = WaShippingUtils::isBannedLocation($this->plugin->getAddress('city'), $this->plugin->getAddress('region'), $setting['location_rule']);

        return $setting['type'] === 'only' ? $matched : !$matched;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }
}
