<?php
/**
 * @author Serge Rodovnichenko <serge@syrnik.com>
 * @copyright Serge Rodovnichenko, 2023
 * @license Webasyst
 */

declare(strict_types=1);

class dostavistaShippingApiEntityContactPerson implements JsonSerializable
{
    protected string $name = '';
    protected string $phone = '';

    public function __construct(?string $name = null, ?string $phone = null)
    {
        $this->name = trim((string)$name);
        $this->phone = trim((string)$phone);
    }

    public function jsonSerialize(): array
    {
        return array_filter(['name' => $this->name, 'phone' => $this->phone], 'strlen');
    }
}
