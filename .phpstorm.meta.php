<?php
/**
 * @author Serge Rodovnichenko <serge@syrnik.com>
 * @copyright Serge Rodovnichenko, 2022
 * @license Webasyst
 */

namespace PHPSTORM_META {
    override(\dostavistaShipping::getAddress(), map([
        null      => ['country' => '', 'region' => '', 'city' => '', 'street' => '', 'zip' => ''],
        'country' => '',
        'region'  => '',
        'city'    => '',
        'street'  => '',
        'zip'     => ''
    ]));
//    registerArgumentsSet('TextConfigs', 'regions');
//    expectedArguments(\dostavistaShipping::loadDataConfig(), 0, argumentsSet('TextConfigs'));
}
