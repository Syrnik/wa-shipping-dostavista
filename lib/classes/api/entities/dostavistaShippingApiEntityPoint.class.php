<?php
/**
 * @author Serge Rodovnichenko <serge@syrnik.com>
 * @copyright Serge Rodovnichenko, 2023
 * @license Webasyst
 */

declare(strict_types=1);

class dostavistaShippingApiEntityPoint implements JsonSerializable
{
    protected ?string $address = null;
    protected ?dostavistaShippingApiEntityContactPerson $contact_person = null;
    protected ?string $client_order_id = null;
    protected ?dostavistaShippingApiEntityCoodinate $latitude = null;
    protected ?dostavistaShippingApiEntityCoodinate $longitude = null;
    protected ?dostavistaShippingApiEntityTimestamp $required_start_datetime = null;
    protected ?dostavistaShippingApiEntityTimestamp $required_finish_datetime = null;
    protected ?dostavistaShippingApiEntityMoney $taking_amount = null;
    protected ?dostavistaShippingApiEntityMoney $buyout_amount = null;
    protected ?string $note = null;
    protected ?bool $is_order_payment_here = null;
    protected ?string $building_number = null;
    protected ?string $entrance_number = null; //подъезд-парадная
    protected ?string $intercom_code = null; //код домофона
    protected ?string $floor_number = null;
    protected ?string $apartment_number = null;

    /**
     * @var string|null Инструкция для курьера, как пройти до получателя на месте.
     */
    protected ?string $invisible_mile_navigation_instructions = null;

    /**
     * @var bool|null Требуется ли выдать кассовый чек получателю на точке. Если да, то необходимо передать список товаров в поле packages.
     */
    protected ?bool $is_cod_cash_voucher_required = null;

    /**
     * @var int|null Уникальный номер доставки (на первой точке не применяется)
     */
    protected ?int $delivery_id = null;

    /** @var dostavistaShippingApiEntityPackage[] */
    protected array $packages = [];

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        $data = [
            'address'                                => $this->getAddress(),
            'contact_person'                         => $this->getContactPerson(),
            'client_order_id'                        => $this->getClientOrderId(),
            'latitude'                               => $this->getLatitude(),
            'longitude'                              => $this->getLongitude(),
            'required_start_datetime'                => $this->getRequiredStartDatetime(),
            'required_finish_datetime'               => $this->getRequiredFinishDatetime(),
            'taking_amount'                          => $this->getTakingAmount(),
            'buyout_amount'                          => $this->getBuyoutAmount(),
            'note'                                   => $this->getNote(),
            'is_order_payment_here'                  => $this->getIsOrderPaymentHere(),
            'building_number'                        => $this->getBuildingNumber(),
            'entrance_number'                        => $this->getEntranceNumber(),
            'intercom_code'                          => $this->getIntercomCode(),
            'floor_number'                           => $this->getFloorNumber(),
            'apartment_number'                       => $this->getApartmentNumber(),
            'invisible_mile_navigation_instructions' => $this->getInvisibleMileNavigationInstructions(),
            'is_cod_cash_voucher_required'           => $this->getIsCodCashVoucherRequired(),
            'delivery_id'                            => $this->getDeliveryId(),
            'packages'                               => $this->getPackages()
        ];
        $data = array_filter($data, function ($value, string $key): bool {
            if (null === $value) return false;
            switch ($key) {
                case 'packages':
                    return !empty($packages);
            }
            return true;
        }, ARRAY_FILTER_USE_BOTH);

        return $data;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function getContactPerson(): ?dostavistaShippingApiEntityContactPerson
    {
        return $this->contact_person;
    }

    public function getClientOrderId(): ?string
    {
        return $this->client_order_id;
    }

    public function getLatitude(): ?dostavistaShippingApiEntityCoodinate
    {
        return $this->latitude;
    }

    public function getLongitude(): ?dostavistaShippingApiEntityCoodinate
    {
        return $this->longitude;
    }

    public function getRequiredStartDatetime(): ?dostavistaShippingApiEntityTimestamp
    {
        return $this->required_start_datetime;
    }

    public function getRequiredFinishDatetime(): ?dostavistaShippingApiEntityTimestamp
    {
        return $this->required_finish_datetime;
    }

    public function getTakingAmount(): ?dostavistaShippingApiEntityMoney
    {
        return $this->taking_amount;
    }

    public function getBuyoutAmount(): ?dostavistaShippingApiEntityMoney
    {
        return $this->buyout_amount;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function getIsOrderPaymentHere(): ?bool
    {
        return $this->is_order_payment_here;
    }

    public function getBuildingNumber(): ?string
    {
        return $this->building_number;
    }

    public function getEntranceNumber(): ?string
    {
        return $this->entrance_number;
    }

    public function getIntercomCode(): ?string
    {
        return $this->intercom_code;
    }

    public function getFloorNumber(): ?string
    {
        return $this->floor_number;
    }

    public function getApartmentNumber(): ?string
    {
        return $this->apartment_number;
    }

    public function getInvisibleMileNavigationInstructions(): ?string
    {
        return $this->invisible_mile_navigation_instructions;
    }

    public function getIsCodCashVoucherRequired(): ?bool
    {
        return $this->is_cod_cash_voucher_required;
    }

    public function getDeliveryId(): ?int
    {
        return $this->delivery_id;
    }

    public function getPackages(): array
    {
        return $this->packages;
    }

    public function setAddress(?string $address): dostavistaShippingApiEntityPoint
    {
        $this->address = $address;
        return $this;
    }

    public function setContactPerson(?dostavistaShippingApiEntityContactPerson $contact_person): dostavistaShippingApiEntityPoint
    {
        $this->contact_person = $contact_person;
        return $this;
    }

    public function setClientOrderId(?string $client_order_id): dostavistaShippingApiEntityPoint
    {
        $this->client_order_id = $client_order_id;
        return $this;
    }

    public function setLatitude(?dostavistaShippingApiEntityCoodinate $latitude): dostavistaShippingApiEntityPoint
    {
        $this->latitude = $latitude;
        return $this;
    }

    public function setLongitude(?dostavistaShippingApiEntityCoodinate $longitude): dostavistaShippingApiEntityPoint
    {
        $this->longitude = $longitude;
        return $this;
    }

    public function setRequiredStartDatetime(?dostavistaShippingApiEntityTimestamp $required_start_datetime): dostavistaShippingApiEntityPoint
    {
        $this->required_start_datetime = $required_start_datetime;
        return $this;
    }

    public function setRequiredFinishDatetime(?dostavistaShippingApiEntityTimestamp $required_finish_datetime): dostavistaShippingApiEntityPoint
    {
        $this->required_finish_datetime = $required_finish_datetime;
        return $this;
    }

    public function setTakingAmount(?dostavistaShippingApiEntityMoney $taking_amount): dostavistaShippingApiEntityPoint
    {
        $this->taking_amount = $taking_amount;
        return $this;
    }

    public function setBuyoutAmount(?dostavistaShippingApiEntityMoney $buyout_amount): dostavistaShippingApiEntityPoint
    {
        $this->buyout_amount = $buyout_amount;
        return $this;
    }

    public function setNote(?string $note): dostavistaShippingApiEntityPoint
    {
        $this->note = $note;
        return $this;
    }

    public function setIsOrderPaymentHere(?bool $is_order_payment_here): dostavistaShippingApiEntityPoint
    {
        $this->is_order_payment_here = $is_order_payment_here;
        return $this;
    }

    public function setBuildingNumber(?string $building_number): dostavistaShippingApiEntityPoint
    {
        $this->building_number = $building_number;
        return $this;
    }

    public function setEntranceNumber(?string $entrance_number): dostavistaShippingApiEntityPoint
    {
        $this->entrance_number = $entrance_number;
        return $this;
    }

    public function setIntercomCode(?string $intercom_code): dostavistaShippingApiEntityPoint
    {
        $this->intercom_code = $intercom_code;
        return $this;
    }

    public function setFloorNumber(?string $floor_number): dostavistaShippingApiEntityPoint
    {
        $this->floor_number = $floor_number;
        return $this;
    }

    public function setApartmentNumber(?string $apartment_number): dostavistaShippingApiEntityPoint
    {
        $this->apartment_number = $apartment_number;
        return $this;
    }

    public function setInvisibleMileNavigationInstructions(?string $invisible_mile_navigation_instructions): dostavistaShippingApiEntityPoint
    {
        $this->invisible_mile_navigation_instructions = $invisible_mile_navigation_instructions;
        return $this;
    }

    public function setIsCodCashVoucherRequired(?bool $is_cod_cash_voucher_required): dostavistaShippingApiEntityPoint
    {
        $this->is_cod_cash_voucher_required = $is_cod_cash_voucher_required;
        return $this;
    }

    public function setDeliveryId(?int $delivery_id): dostavistaShippingApiEntityPoint
    {
        $this->delivery_id = $delivery_id;
        return $this;
    }

    public function setPackages(dostavistaShippingApiEntityPackage ...$packages): dostavistaShippingApiEntityPoint
    {
        $this->packages = $packages;
        return $this;
    }
}
