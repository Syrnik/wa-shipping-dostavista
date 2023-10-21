<?php
/**
 * @author Serge Rodovnichenko <serge@syrnik.com>
 * @copyright Serge Rodovnichenko, 2023
 * @license Webasyst
 */

declare(strict_types=1);

class dostavistaShippingApiQueryCalculateOrder implements dostavistaShippingApiQueryInterface
{
    protected dostavistaShippingApiEntityOrder $order;

    public function __construct(dostavistaShippingApiEntityOrder $order)
    {
        $this->order = $order;
    }

    public function getOrder(): dostavistaShippingApiEntityOrder
    {
        return $this->order;
    }

    public function getEndPoint(): string
    {
        return '/calculate-order';
    }

    public function getHttpMethod(): string
    {
        return waNet::METHOD_POST;
    }

    public function getData(): dostavistaShippingApiEntityOrder
    {
        return $this->getOrder();
    }
}
