<?php
/**
 * @author Serge Rodovnichenko <serge@syrnik.com>
 * @copyright Serge Rodovnichenko, 2026
 * @license Webasyst
 */

declare(strict_types=1);


class dostavistaShippingDeliveryIntervals implements dostavistaShippingApiQueryInterface
{

    /**
     * @readonly
     * @var string|null
     */
    public ?string $date;

    /**
     * @param string|null $date
     */
    public function __construct(?string $date = null)
    {
        $this->date = $date;
    }


    public function getEndPoint(): string
    {
        return '/delivery-intervals';
    }

    public function getHttpMethod(): string
    {
        return waNet::METHOD_GET;
    }

    /**
     * @inheritDoc
     */
    public function getData(): array
    {
        return $this->date ? ['date' => $this->date] : [];
    }
}
