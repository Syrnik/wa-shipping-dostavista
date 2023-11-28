<?php
/**
 * @author Serge Rodovnichenko <serge@syrnik.com>
 * @copyright Serge Rodovnichenko, 2023
 * @license Webasyst
 */

declare(strict_types=1);

class dostavistaShippingApiEntityTimestamp implements JsonSerializable
{
    protected DateTimeInterface $value;

    /**
     * @param DateTimeInterface $value
     */
    public function __construct(DateTimeInterface $value)
    {
        $this->value = $value;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): string
    {
        return (string)$this;
    }

    public function __toString(): string
    {
        return $this->getValue()->format('c');
    }

    public function getValue(): DateTimeInterface
    {
        return $this->value;
    }
}
