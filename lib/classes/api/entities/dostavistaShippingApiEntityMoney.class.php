<?php
/**
 * @author Serge Rodovnichenko <serge@syrnik.com>
 * @copyright Serge Rodovnichenko, 2023
 * @license Webasyst
 */

declare(strict_types=1);

class dostavistaShippingApiEntityMoney implements JsonSerializable
{
    protected float $value = 0;

    /**
     * @param float|int $value
     */
    public function __construct(float $value = 0)
    {
        $this->value = $value;
    }

    public function jsonSerialize(): string
    {
        return (string)$this;
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public function setValue(float $value): void
    {
        $this->value = $value;
    }

    public function __toString(): string
    {
        return number_format($this->getValue(), 2, '.', '');
    }
}
