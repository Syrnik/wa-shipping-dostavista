<?php
/**
 * @author Serge Rodovnichenko <serge@syrnik.com>
 * @copyright (c) 2019, Serge Rodovnichenko
 * @license http://www.webasyst.com/terms/#eula Webasyst
 */

/**
 * @property-read string $token
 */
class dostavistaShipping extends waShipping
{
    /**
     * @return string
     */
    protected function calculate()
    {
        //TODO put here code to calculate delivery cost
        return 'Calculate method not implemented yet';
    }

    /**
     * @return string
     */
    public function allowedCurrency()
    {
        return 'RUB';
    }

    /**
     * @return string
     */
    public function allowedWeightUnit()
    {
        return 'kg';
    }

    /**
     * @return array
     */
    public function allowedAddress()
    {
        return [['country' => 'rus', 'region' => ['77', '50']]];
    }

    /**
     * @return array
     */
    public function requestedAddressFields()
    {
        return [
            'country' => ['hidden' => true, 'value' => 'rus'],
            'region'  => ['cost' => true, 'required' => true],
            'city'    => ['cost' => true, 'required' => true],
            'street'  => ['cost' => true, 'required' => true]
        ];
    }

    /**
     * @param mixed $data
     */
    private function sendJsonData($data)
    {
        $response = array(
            'status' => 'ok',
            'data'   => $data,
        );
        $this->sendJsonResponse($response);
    }

    /**
     * @param mixed $response
     */
    private function sendJsonResponse($response)
    {
        if (waRequest::isXMLHttpRequest()) {
            wa()->getResponse()->addHeader('Content-Type', 'application/json')->sendHeaders();
        }
        echo json_encode($response);
        exit;
    }

    /**
     * @param mixed $error
     */
    private function sendJsonError($error)
    {
        $response = array(
            'status' => 'fail',
            'errors' => array($error),
        );
        $this->sendJsonResponse($response);
    }

    /**
     * @return string
     */
    protected function getDostavistaApiUrl()
    {
        return 'https://robotapitest.dostavista.ru/api/business/1.1';
    }

    /**
     * @param $method
     * @param $http_method
     * @param array $data
     * @param array $options
     * @return array
     * @throws waException
     */
    public function queryDostavistaApi($method, $http_method, array $data = [], array $options = [])
    {
        $url = ifset($options, 'api_url', $this->getDostavistaApiUrl()) . "/$method";
        $token = ifset($options, 'token', $this->token);

        $headers = ['X-DV-Auth-Token' => $token] + (array)ifset($options, 'headers', []);

        return (new waNet(['format' => waNet::FORMAT_JSON, 'expected_http_code' => '200,400'], $headers))->query($url, $data, $http_method);
    }
}
