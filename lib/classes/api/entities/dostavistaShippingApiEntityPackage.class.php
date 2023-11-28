<?php
/**
 * @author Serge Rodovnichenko <serge@syrnik.com>
 * @copyright Serge Rodovnichenko, 2023
 * @license Webasyst
 */

declare(strict_types=1);

class dostavistaShippingApiEntityPackage implements JsonSerializable
{
    protected ?string $ware_code = null;
    protected ?string $description = null;
    protected ?float $items_count = null;
    protected ?dostavistaShippingApiEntityMoney $item_payment_amount = null;
    protected ?string $nomenclature_code = null;

    public function jsonSerialize(): array
    {
        return array_filter([
            'ware_code'           => $this->getWareCode(),
            'description'         => $this->getDescription(),
            'items_count'         => $this->getItemsCount(),
            'item_payment_amount' => $this->getItemPaymentAmount(),
            'nomenclature_code'   => $this->getNomenclatureCode()
        ], function ($value, $key): bool {
            if (null === $value) return false;
            switch ($key) {
                case 'items_count':
                    return $value > 0;
                case 'item_payment_amount':
                    /** @var dostavistaShippingApiEntityMoney $value */
                    return round($value->getValue(), 2) > 0;
                default:
                    return !!strlen(trim($value));
            }
        }, ARRAY_FILTER_USE_BOTH);
    }

    public function getWareCode(): ?string
    {
        return $this->ware_code;
    }

    public function setWareCode(?string $ware_code): dostavistaShippingApiEntityPackage
    {
        $this->ware_code = $ware_code;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): dostavistaShippingApiEntityPackage
    {
        $this->description = $description;
        return $this;
    }

    public function getItemsCount(): ?float
    {
        return $this->items_count;
    }

    public function setItemsCount(?float $items_count): dostavistaShippingApiEntityPackage
    {
        $this->items_count = $items_count;
        return $this;
    }

    public function getItemPaymentAmount(): ?dostavistaShippingApiEntityMoney
    {
        return $this->item_payment_amount;
    }

    public function setItemPaymentAmount(?dostavistaShippingApiEntityMoney $item_payment_amount): dostavistaShippingApiEntityPackage
    {
        $this->item_payment_amount = $item_payment_amount;
        return $this;
    }

    public function getNomenclatureCode(): ?string
    {
        return $this->nomenclature_code;
    }

    public function setNomenclatureCode(?string $nomenclature_code): dostavistaShippingApiEntityPackage
    {
        $this->nomenclature_code = $nomenclature_code;
        return $this;
    }
}
