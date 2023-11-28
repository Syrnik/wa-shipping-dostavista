<?php
/**
 * @author Serge Rodovnichenko <serge@syrnik.com>
 * @copyright Serge Rodovnichenko, 2023
 * @license Webasyst
 */

declare(strict_types=1);

class dostavistaShippingApi
{
    protected bool $test_mode;

    protected string $api_token;

    protected const API_VERSION = "1.4";

    protected const API_URL = [
        'https://robot.dostavista.ru/api/business',
        'https://robotapitest.dostavista.ru/api/business'
    ];

    /**
     * @param string $api_token
     * @param bool $test_mode
     */
    public function __construct(string $api_token, bool $test_mode = false)
    {
        $this->api_token = $api_token;
        $this->test_mode = $test_mode;
    }

    public function isTestMode(): bool
    {
        return $this->test_mode;
    }

    public function getApiUrl(): string
    {
        return self::API_URL[(int)$this->isTestMode()] . '/' . self::API_VERSION;
    }

    public function getApiToken(): string
    {
        return $this->api_token;
    }

    /**
     * @param dostavistaShippingApiEntityOrder $order
     * @return array
     * @throws waException
     */
    public function CalculateOrder(dostavistaShippingApiEntityOrder $order): array
    {
        return $this->_query(new dostavistaShippingApiQueryCalculateOrder($order));
    }

    /**
     * @param dostavistaShippingApiQueryInterface $query
     * @return array
     * @throws waException
     */
    protected function _query(dostavistaShippingApiQueryInterface $query): array
    {
        $headers = ['X-DV-Auth-Token' => $this->getApiToken()];

        return (new waNet(['format' => waNet::FORMAT_JSON, 'expected_http_code' => [200, 400]], $headers))
            ->query($this->getApiUrl() . $query->getEndPoint(), $query->getData(), $query->getHttpMethod()) ?: [];
    }
}
