<?php
/**
 * @author Serge Rodovnichenko <serge@syrnik.com>
 * @copyright Serge Rodovnichenko, 2023
 * @license Webasyst
 */

declare(strict_types=1);

class dostavistaShippingApiEntityCoodinate implements JsonSerializable
{
    protected float $value;

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
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
        return rtrim(rtrim(number_format($this->getValue(), 7, '.', ''), '0'), '.');
    }
}
