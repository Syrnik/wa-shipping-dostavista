<?php
/**
 * @author Serge Rodovnichenko <serge@syrnik.com>
 * @copyright Serge Rodovnichenko, 2023
 * @license Webasyst
 */

$dir = wa()->getConfig()->getPath('plugins') . '/shipping/dostavista/';

$files = [
    'lib/classes/LoggerForShippingPlugins.trait.php',
    'lib/classes/LoggerActionFormatter.class.php',
    'lib/classes/Surcharge.class.php',
    'lib/classes/InstanceCache.trait.php',
    'lib/classes/Address.class.php'
];

foreach ($files as $file) {
    try {
        waFiles::delete($dir . $file);
    } catch (Exception $e) {
        waLog::log('Dostavista shipping plugin update (' . __FILE__ . "): Error deleting old file $file: " . $e->getMessage());
    }
}
