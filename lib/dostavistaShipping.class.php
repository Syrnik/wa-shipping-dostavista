<?php

/**
 *
 */
class dostavistaShipping extends waShipping
{
    protected function calculate()
    {
        //TODO put here code to calculate delivery cost
        return 'Calculate method not implemented yet';
    }

    public function allowedCurrency()
    {
        return 'USD';
    }

    public function allowedWeightUnit()
    {
        return 'kg';
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
}
