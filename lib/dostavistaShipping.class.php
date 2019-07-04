<?php
/**
 * @author Serge Rodovnichenko <serge@syrnik.com>
 * @copyright (c) 2019, Serge Rodovnichenko
 * @license http://www.webasyst.com/terms/#eula Webasyst
 */

use SergeR\CakeUtility\Hash;
use Syrnik\dostavistaShipping\Address;

/**
 * @property-read string $token
 */
class dostavistaShipping extends waShipping
{
    /**
     * @return string|array|bool
     * @throws waException
     */
    protected function calculate()
    {
        $address = new Address($this->getAddress());
        if (($address_errors = $this->addressValidationErrors($address)) !== true) {
            return $address_errors;
        }

        $response = $this->queryDostavistaApi('calculate-order', waNet::METHOD_POST, [
            'matter'          => 'Shopping',
            'total_weight_kg' => (int)$this->getTotalWeight(),
            'points'          => [
                [
                    'address' => 'Москва, Покровка, 11'
                ],
                [
                    'address' => (string)$address
                ]
            ]
        ]);

        if (!$response['is_successful']) {
            return [
                'rate'    => null,
                'comment' => 'Доставка по указанному адресу невозможна'
            ];
        }

        $result = [
            'dostavista_courier' => [
                'rate'     => (float)Hash::get($response, 'order.payment_amount'),
                'currency' => 'RUB'
            ]
        ];

        if (($destination_address = (string)Hash::get($response, 'order.points.1.address'))) {
            $result['dostavista_courier']['comment'] = "курьером по адресу: " . waString::escapeAll($destination_address);
        }


        return $result;
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
     * @see waShipping::init()
     */
    protected function init()
    {
        require_once 'vendors/autoload.php';
        parent::init();
        waAutoload::getInstance()->add([
            'Syrnik\\dostavistaShipping\\Address' => "wa-plugins/shipping/dostavista/lib/classes/Address.class.php",
        ]);
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

    /**
     * @param Address $address
     * @return array|bool
     * @throws waException
     */
    protected function addressValidationErrors(Address $address)
    {
        if (($address_validation = $address->validate()) !== true) {
            if (!is_array($address_validation)) {
//                $this->logProcess('Проверка адреса вернула неожиданное значение: {data}', ['data' => var_export($address_validation, true)], 'flush');
                return false;
            }
            if ($address_validation['code'] === Address::ERR_VALIDATION_FATAL_RECOVERABLE) {
//                $this->logProcess('Исправимая ошибка проверки адреса: {msg}', ['msg' => $address_validation['message']], 'flush');
                return [['rate' => null, 'comment' => $address_validation['message']]];
            }
//            $this->logProcess('Проверка полей дреса не пройдена: {data}', ['data' => var_export($address_validation, true)], 'flush');
            throw new waException('Ошибка проверки полей адреса');
        }

        return true;
    }

    /**
     * @return float
     */
    protected function getTotalWeight()
    {
        return max(1, ceil(parent::getTotalWeight()));
    }
}
