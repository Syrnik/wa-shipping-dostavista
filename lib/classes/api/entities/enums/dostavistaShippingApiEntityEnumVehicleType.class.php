<?php
/**
 * @author Serge Rodovnichenko <serge@syrnik.com>
 * @copyright Serge Rodovnichenko, 2023
 * @license Webasyst
 */

class dostavistaShippingApiEntityEnumVehicleType implements JsonSerializable
{
    const CAR_500KG = 1;
    const CAR_CARGO_700KG = 2;
    const BUS_1000KG = 3;
    const SMALL_TRUCK_1500KG = 4;
    const TRUCK = 5;
    const PEDESTRIAN = 6;
    const PASSENGER_CAR = 7;

    private int $value;

    /**
     * @param int $value
     */
    public function __construct(int $value)
    {
        if (!in_array($value, [
            self::CAR_500KG,
            self::CAR_CARGO_700KG,
            self::BUS_1000KG,
            self::SMALL_TRUCK_1500KG,
            self::TRUCK,
            self::PEDESTRIAN,
            self::PASSENGER_CAR
        ], true))
            throw new InvalidArgumentException('Invalid vehicle type');

        $this->value = $value;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function jsonSerialize(): int
    {
        return $this->getValue();
    }
}
