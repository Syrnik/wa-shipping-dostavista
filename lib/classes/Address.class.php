<?php
/**
 * @author Serge Rodovnichenko <serge@syrnik.com>
 * @copyright Serge Rodovnichenko, 2019
 * @license Webasyst
 */

namespace Syrnik\dostavistaShipping;

use SergeR\CakeUtility\Inflector;
use SergeR\CakeUtility\Text;
use SergeR\CakeUtility\Hash;
use Syrnik\WaShippingUtils;
use waContactField;
use waContactFields;
use waCountryModel;
use waException;
use waRegionModel;

/**
 * Class Address
 * @package Syrnik\dostavistaShipping
 *
 * @method string getRegionCode()
 * @method string getRegionIso3()
 * @method string getRegionName()
 */
class Address
{
    const ERR_VALIDATION_FATAL = 1;
    const ERR_VALIDATION_FATAL_RECOVERABLE = 2;

    /** @var string */
    protected $City = '';
    /** @var string */
    protected $Zip = '';
    /** @var string */
    protected $Region = '';
    /** @var string */
    protected $Country = '';

    /** @var string */
    protected $Street = '';

    protected $address_fileds;

    /**
     * Address constructor.
     * @param array $Address
     */
    public function __construct(array $Address = [])
    {
        $this->setCountry((string)Hash::get($Address, 'country'))
            ->setZip(trim((string)Hash::get($Address, 'zip')))
            ->setCity(trim((string)Hash::get($Address, 'city')))
            ->setRegion(trim((string)Hash::get($Address, 'region')))
            ->setStreet(trim((string)Hash::get($Address, 'street')));
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->City;
    }

    /**
     * @return string
     */
    public function getSearchableCity()
    {
        return self::makeSearchable($this->getCity());
    }

    /**
     * @return string павловский посад => Павловский Посад
     */
    public function getCityCaseTitle()
    {
        return mb_convert_case($this->getCity(), MB_CASE_TITLE, 'UTF-8');
    }

    /**
     * @param string $City
     * @return Address
     */
    public function setCity($City)
    {
        $this->City = trim($City);
        return $this;
    }

    /**
     * @return string
     */
    public function getZip()
    {
        return $this->Zip;
    }

    /**
     * @param string $Zip
     * @return Address
     */
    public function setZip($Zip)
    {
        $this->Zip = trim($Zip);
        return $this;
    }

    /**
     * @return string
     */
    public function getRegion()
    {
        return $this->Region;
    }

    /**
     * @param string $Region
     * @return Address
     */
    public function setRegion($Region)
    {
        $this->Region = trim($Region);
        return $this;
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->Country;
    }

    /**
     * @return string
     */
    public function getCountryName()
    {
        $country = (new waCountryModel)->get($this->getCountry());

        return $country ? (string)Hash::get($country, 'name') : '';
    }

    /**
     * @param string $Country
     * @return Address
     */
    public function setCountry($Country)
    {
        $this->Country = trim($Country);
        return $this;
    }

    /**
     * @return string
     */
    public function getStreet()
    {
        return $this->Street;
    }

    /**
     * @param string $Street
     * @return Address
     */
    public function setStreet($Street)
    {
        $this->Street = trim($Street);
        return $this;
    }

    /**
     * @param $name
     * @param array $arguments
     * @return string
     */
    public function __call($name, array $arguments)
    {
        if ((substr($name, 0, 9) === 'getRegion') && (strlen($name) > 9)) {
            return $this->getRegionData(Inflector::underscore(substr($name, 9)));
        }
    }

    /**
     * @param null $field
     * @return array|string|null
     */
    public function getRegionData($field = null)
    {
        static $region;

        if ($region && (((string)Hash::get($region, 'code') !== $this->getRegion()) || ((string)Hash::get($region, 'country_iso3') !== $this->getCountry()))) {
            $region = null;
        }

        if (!$region) {
            if (!$this->getRegion() || !$this->getCountry()) {
                return '';
            }

            $_r = (array)(new waRegionModel)->get($this->getCountry(), $this->getRegion());
            if (empty($_r)) {
                return '';
            }
            $region = $_r;
        }

        return $field === null ? $region : (string)Hash::get($region, $field);
    }

    public function validate()
    {
        $empty_fileds = [];
        $required_fields = ['country', 'region', 'city'];

        foreach ($required_fields as $field_id) {
            $getter = 'get' . ucfirst($field_id);
            $field_value = $this->$getter();
            $field_value = trim($field_value);
            if (empty($field_value)) {
                $empty_fileds[$field_id] = "'" . $this->getAddressFieldName($field_id) . "'";
            }
        }
        if (!empty($empty_fileds)) {
            return [
                'code'    => self::ERR_VALIDATION_FATAL_RECOVERABLE,
                'message' => sprintf("Нужно заполнить %s адреса %s", count($empty_fileds) > 1 ? 'поля' : 'поле', Text::toList($empty_fileds, 'и'))];
        }

        $country = $this->getCountry();

        if ($country !== 'rus') {
            return [
                'code'    => self::ERR_VALIDATION_FATAL_RECOVERABLE,
                'message' => 'Расчёт доставки возможен только по РФ'
            ];
        }

        $region_code = $this->getRegionCode();
        if (!in_array($region_code, ['77', '50'])) {
            return [
                'code'    => self::ERR_VALIDATION_FATAL_RECOVERABLE,
                'message' => 'Расчёт доставки возможен только по Москве и Московской области'
            ];
        }

        return true;
    }

    /**
     * @param string $field_id
     * @return string
     */
    protected function getAddressFieldName($field_id)
    {
        if (!is_array($this->address_fileds)) {
            $this->address_fileds = [];
        }
        if (isset($this->address_fileds[$field_id])) {
            return $this->address_fileds[$field_id];
        }

        $field = waContactFields::get('address')->getFields($field_id);

        $name = $field instanceof waContactField ? $field->getName() : $field_id;

        $this->address_fileds[$field_id] = $name;

        return $name;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return implode(', ', array_filter([
            $this->getZip(),
            ($this->getSearchableCity() === 'москва' ? '' : $this->getRegionName()),
            $this->getCity(),
            $this->getStreet()
        ]));
    }

    /**
     * @param string $country Country ISO3 code
     * @return bool
     */
    public function isCountryHasRegions($country)
    {
        try {
            return (new waRegionModel)->countByField('country_iso3', $country) > 0;
        } catch (waException $e) {
            return false;
        }
    }

    /**
     * @param $str
     * @return string
     */
    public static function makeSearchable($str)
    {
        return WaShippingUtils::replaceYo(WaShippingUtils::mb_trim(mb_strtolower($str, 'UTF-8')));
    }
}