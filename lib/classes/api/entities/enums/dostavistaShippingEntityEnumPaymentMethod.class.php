<?php
/**
 * @author Serge Rodovnichenko <serge@syrnik.com>
 * @copyright Serge Rodovnichenko, 2023
 * @license Webasyst
 */

class dostavistaShippingEntityEnumPaymentMethod implements JsonSerializable
{
    const CASH = 'cash';
    const NON_CASH = 'non_cash';
    const BANK_CARD = 'bank_card';

    private string $value;

    /**
     * @param string $value
     */
    public function __construct(string $value)
    {
        if (!in_array($value, [self::CASH, self::NON_CASH, self::BANK_CARD], true))
            throw new InvalidArgumentException('Invalid pay type');

        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function jsonSerialize(): string
    {
        return $this->getValue();
    }
}
