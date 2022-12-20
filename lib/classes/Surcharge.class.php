<?php
/**
 * @author Serge Rodovnichenko <serge@syrnik.com>
 * @copyright Serge Rodovnichenko, 2019
 * @license Webasyst
 */

namespace Syrnik\dostavistaShipping;

use waException;
use Webit\Util\EvalMath\EvalMath;

/**
 * Class Surcharge
 * @package Syrnik\vozovozShipping
 * @deprecated
 */
class Surcharge
{
    const CALCULATED_DELIVERY_COST = 's';
    const ORDER_TOTAL = 'z';
    const ORDER_TOTAL_RAW = 'w';

    protected $CalculatedDeliveryCost;
    protected $OrderTotal = 0.0;
    protected $OrderRawTotal = 0.0;
    protected $FreeDelivery = null;

    /** @var null|string */
    protected $Formula = null;

    /**
     * Surcharge constructor.
     * @param array $settings
     */
    public function __construct(array $settings = [])
    {
        foreach ($settings as $key => $setting) {
            if (property_exists($this, $key)) {
                $this->$key = $setting;
            }
        }
    }

    /**
     * @return bool
     */
    public function isFreeDelivery()
    {
        return ($this->getFreeDelivery() !== null) && ($this->getOrderTotal() >= $this->getFreeDelivery());
    }

    /**
     * @return bool
     */
    public function isFormula()
    {
        return $this->getFormula() !== null;
    }

    /**
     * @return bool
     */
    public function isFormulaRequiresDeliveryCost()
    {
        return strpos($this->getFormula(), self::CALCULATED_DELIVERY_COST) !== -1;
    }

    /**
     * Нужно ли вызывать внешний расчёт для определения стоимости доставки или можем обойтись без
     * обращения к внешнему сервису
     *
     * - Если доставка бесплатна, расчёт не нужен
     * - Если доставка фиксирована, расчёт не нужен
     * - Если в формуле отстутствует переменная "стоимость доставки", расчёт не нужен
     *
     * @return bool
     */
    public function isRemoteCallRequired()
    {
        if ($this->isFreeDelivery()) {
            return false;
        }

        if (!$this->isFormula()) {
            return true;
        }

        if (!$this->isFormulaRequiresDeliveryCost()) {
            return false;
        }

        return true;
    }

    /**
     * @return float|null
     */
    public function getCalculatedDeliveryCost()
    {
        return $this->CalculatedDeliveryCost;
    }

    /**
     * @param float $CalculatedDeliveryCost
     * @return Surcharge
     */
    public function setCalculatedDeliveryCost($CalculatedDeliveryCost)
    {
        $this->CalculatedDeliveryCost = $CalculatedDeliveryCost;
        return $this;
    }

    /**
     * @return float
     */
    public function getOrderTotal()
    {
        return $this->OrderTotal;
    }

    /**
     * @param float $OrderTotal
     * @return Surcharge
     */
    public function setOrderTotal($OrderTotal)
    {
        $this->OrderTotal = $OrderTotal;
        return $this;
    }

    /**
     * @return float
     */
    public function getOrderRawTotal()
    {
        return $this->OrderRawTotal;
    }

    /**
     * @param float $OrderRawTotal
     * @return Surcharge
     */
    public function setOrderRawTotal($OrderRawTotal)
    {
        $this->OrderRawTotal = $OrderRawTotal;
        return $this;
    }

    /**
     * @return null
     */
    public function getFreeDelivery()
    {
        return $this->FreeDelivery;
    }

    /**
     * @param null $FreeDelivery
     * @return Surcharge
     */
    public function setFreeDelivery($FreeDelivery)
    {
        $this->FreeDelivery = $FreeDelivery;
        return $this;
    }

    /**
     * @return null
     */
    public function getFormula()
    {
        return $this->Formula;
    }

    /**
     * @param null|string $Formula
     * @return Surcharge
     */
    public function setFormula($Formula)
    {
        if (is_string($Formula)) {
            $Formula = trim($Formula);
            if (!strlen($Formula)) {
                $Formula = null;
            }
        }

        $this->Formula = is_string($Formula) ? strtolower(str_replace(',', '.', $Formula)) : $Formula;
        return $this;
    }

    /**
     * @return float|null
     * @throws waException
     */
    public function calculate()
    {
        if ($this->isFreeDelivery()) {
            return 0.0;
        }

        if (!$this->isFormula()) {
            return $this->getCalculatedDeliveryCost();
        }

        /**
         * fixed price, formula contains only numbers and decimal point
         */
        if (preg_match('/^[\d.]+$/', $this->getFormula())) {
            return (float)$this->getFormula();
        }

        $math = new EvalMath();
        $math->suppress_errors = true;
        $math->evaluate(self::ORDER_TOTAL . '=' . str_replace(',', '.', $this->getOrderTotal()));
        $math->evaluate(self::ORDER_TOTAL_RAW . '=' . str_replace(',', '.', $this->getOrderRawTotal()));
        if ($this->isFormulaRequiresDeliveryCost()) {
            $math->evaluate(self::CALCULATED_DELIVERY_COST . '=' . str_replace(',', '.', (float)$this->getCalculatedDeliveryCost()));
        }

        $result = $math->evaluate($this->getFormula());

        if ($result === false) {
            throw new waException($math->last_error);
        }

        return (float)$result;
    }
}
