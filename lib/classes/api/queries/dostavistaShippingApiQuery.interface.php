<?php
/**
 * @author Serge Rodovnichenko <serge@syrnik.com>
 * @copyright Serge Rodovnichenko, 2023
 * @license Webasyst
 */

declare(strict_types=1);

interface dostavistaShippingApiQueryInterface
{
    public function getEndPoint(): string;

    public function getHttpMethod(): string;

    /** mixed */
    public function getData();
}
