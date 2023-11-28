<?php
/**
 * @author Serge Rodovnichenko <serge@syrnik.com>
 * @copyright Serge Rodovnichenko, 2023
 * @license Webasyst
 */

declare(strict_types=1);

class dostavistaShippingApiEntityOrder implements JsonSerializable
{
    protected ?dostavistaShippingApiEntityEnumOrderType $type = null;
    protected ?string $matter = null;
    protected ?dostavistaShippingApiEntityEnumVehicleType $vehicle_type = null;
    protected ?int $total_weight = null;
    protected ?dostavistaShippingApiEntityMoney $insurance_amount = null;
    protected ?bool $is_client_notification_enabled = null;
    protected ?bool $is_contact_person_notification_enabled = null;
    protected ?bool $is_route_optimizer_enabled = null;
    protected ?int $loaders_count = null;
    protected ?string $backpayment_details = null;
    protected ?bool $is_motobox_required = null;
    protected ?dostavistaShippingEntityEnumPaymentMethod $payment_method = null;
    protected ?int $bank_card_id = null;
    protected ?string $promo_code = null;

    /** @var dostavistaShippingApiEntityPoint[] */
    protected array $points = [];

    /**
     * @inheritDoc
     */
    public function jsonSerialize():array
    {
        $data = [
            'type'                                   => $this->getType(),
            'matter'                                 => $this->getMatter(),
            'vehicle_type_id'                        => $this->getVehicleType(),
            'total_weight_kg'                        => $this->getTotalWeight(),
            'insurance_amount'                       => $this->getInsuranceAmount(),
            'is_client_notification_enabled'         => $this->getIsClientNotificationEnabled(),
            'is_contact_person_notification_enabled' => $this->getIsContactPersonNotificationEnabled(),
            'is_route_optimizer_enabled'             => $this->getIsRouteOptimizerEnabled(),
            'loaders_count'                          => $this->getLoadersCount(),
            'backpayment_details'                    => $this->getBackpaymentDetails(),
            'is_motobox_required'                    => $this->getIsMotoboxRequired(),
            'payment_method'                         => $this->getPaymentMethod(),
            'bank_card_id'                           => $this->getBankCardId(),
            'promo_code'                             => $this->getPromoCode(),
            'points'                                 => $this->getPoints()
        ];

        return array_filter($data, fn($value): bool => null !== $value);
    }

    public function getType(): ?dostavistaShippingApiEntityEnumOrderType
    {
        return $this->type;
    }

    public function getMatter(): ?string
    {
        return $this->matter;
    }

    public function getVehicleType(): ?dostavistaShippingApiEntityEnumVehicleType
    {
        return $this->vehicle_type;
    }

    public function getTotalWeight(): ?int
    {
        return $this->total_weight;
    }

    public function getInsuranceAmount(): ?dostavistaShippingApiEntityMoney
    {
        return $this->insurance_amount;
    }

    public function getIsClientNotificationEnabled(): ?bool
    {
        return $this->is_client_notification_enabled;
    }

    public function getIsContactPersonNotificationEnabled(): ?bool
    {
        return $this->is_contact_person_notification_enabled;
    }

    public function getIsRouteOptimizerEnabled(): ?bool
    {
        return $this->is_route_optimizer_enabled;
    }

    public function getLoadersCount(): ?int
    {
        return $this->loaders_count;
    }

    public function getBackpaymentDetails(): ?string
    {
        return $this->backpayment_details;
    }

    public function getIsMotoboxRequired(): ?bool
    {
        return $this->is_motobox_required;
    }

    public function getPaymentMethod(): ?dostavistaShippingEntityEnumPaymentMethod
    {
        return $this->payment_method;
    }

    public function getBankCardId(): ?int
    {
        return $this->bank_card_id;
    }

    public function getPromoCode(): ?string
    {
        return $this->promo_code;
    }

    public function getPoints(): array
    {
        return $this->points;
    }

    public function setType(?dostavistaShippingApiEntityEnumOrderType $type): dostavistaShippingApiEntityOrder
    {
        $this->type = $type;
        return $this;
    }

    public function setMatter(?string $matter): dostavistaShippingApiEntityOrder
    {
        $this->matter = $matter;
        return $this;
    }

    public function setVehicleType(?dostavistaShippingApiEntityEnumVehicleType $vehicle_type): dostavistaShippingApiEntityOrder
    {
        $this->vehicle_type = $vehicle_type;
        return $this;
    }

    public function setTotalWeight(?int $total_weight): dostavistaShippingApiEntityOrder
    {
        $this->total_weight = $total_weight;
        return $this;
    }

    public function setInsuranceAmount(?dostavistaShippingApiEntityMoney $insurance_amount): dostavistaShippingApiEntityOrder
    {
        $this->insurance_amount = $insurance_amount;
        return $this;
    }

    public function setIsClientNotificationEnabled(?bool $is_client_notification_enabled): dostavistaShippingApiEntityOrder
    {
        $this->is_client_notification_enabled = $is_client_notification_enabled;
        return $this;
    }

    public function setIsContactPersonNotificationEnabled(?bool $is_contact_person_notification_enabled): dostavistaShippingApiEntityOrder
    {
        $this->is_contact_person_notification_enabled = $is_contact_person_notification_enabled;
        return $this;
    }

    public function setIsRouteOptimizerEnabled(?bool $is_route_optimizer_enabled): dostavistaShippingApiEntityOrder
    {
        $this->is_route_optimizer_enabled = $is_route_optimizer_enabled;
        return $this;
    }

    public function setLoadersCount(?int $loaders_count): dostavistaShippingApiEntityOrder
    {
        $this->loaders_count = $loaders_count;
        return $this;
    }

    public function setBackpaymentDetails(?string $backpayment_details): dostavistaShippingApiEntityOrder
    {
        $this->backpayment_details = $backpayment_details;
        return $this;
    }

    public function setIsMotoboxRequired(?bool $is_motobox_required): dostavistaShippingApiEntityOrder
    {
        $this->is_motobox_required = $is_motobox_required;
        return $this;
    }

    public function setPaymentMethod(?dostavistaShippingEntityEnumPaymentMethod $payment_method): dostavistaShippingApiEntityOrder
    {
        $this->payment_method = $payment_method;
        return $this;
    }

    public function setBankCardId(?int $bank_card_id): dostavistaShippingApiEntityOrder
    {
        $this->bank_card_id = $bank_card_id;
        return $this;
    }

    public function setPromoCode(?string $promo_code): dostavistaShippingApiEntityOrder
    {
        $this->promo_code = $promo_code;
        return $this;
    }

    public function setPoints(dostavistaShippingApiEntityPoint ...$points): dostavistaShippingApiEntityOrder
    {
        $this->points = $points;
        return $this;
    }
}
