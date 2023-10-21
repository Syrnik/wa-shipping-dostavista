<?php
/**
 * @author Serge Rodovnichenko <serge@syrnik.com>
 * @copyright Serge Rodovnichenko, 2023
 * @license Webasyst
 */

class dostavistaShippingApiEntityEnumOrderType implements JsonSerializable
{
    const STANDARD = 'standard';
    const SAME_DAY = 'same_day';

    private string $value = self::STANDARD;

    /**
     * @param string $value
     */
    public function __construct(string $value)
    {
        if (!in_array($value, [self::STANDARD, self::SAME_DAY], true))
            throw new InvalidArgumentException('Invalid order type');

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
