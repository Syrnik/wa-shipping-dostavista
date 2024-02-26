<?php
/**
 * @author Serge Rodovnichenko <serge@syrnik.com>
 * @copyright Serge Rodovnichenko, 2023
 * @license Webasyst
 */

$dir = wa()->getConfig()->getPath('plugins') . '/shipping/dostavista/';

$files = [
    'lib/vendors/syrnik/wa-sipping-utils/lib',
    'lib/vendors/syrnik/wa-sipping-utils/composer.lock',
];

foreach ($files as $file) {
    try {
        waFiles::delete($dir . $file);
    } catch (Exception $e) {
        waLog::log('Dostavista shipping plugin update (' . __FILE__ . "): Error deleting old file $file: " . $e->getMessage());
    }
}
