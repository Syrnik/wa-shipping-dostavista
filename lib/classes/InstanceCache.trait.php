<?php
/**
 * @author Serge Rodovnichenko <serge@syrnik.com>
 * @copyright Serge Rodovnichenko, 2019
 * @license http://www.webasyst.com/terms/#eula Webasyst
 */

namespace Syrnik\dostavistaShipping;

use waCache;
use waFileCacheAdapter;

/**
 * Trait InstanceCache
 * @package Syrnik\dostavistaShipping
 * @property string $app_id
 * @property string $key
 * @deprecated
 */
trait InstanceCache
{
    /** @var waCache|null */
    protected $instance_cache;

    /**
     * Кэш только этой установки
     * @return waCache
     */
    protected function getInstanceCache()
    {
        if ($this->instance_cache instanceof waCache) {
            return $this->instance_cache;
        }

        $cache = wa()->getCache('default', $this->app_id);
        if (!($cache instanceof waCache)) {
            $cache = new waCache(new waFileCacheAdapter(array()), $this->app_id);
        }

        $this->instance_cache = $cache;

        return $this->instance_cache;
    }

    /**
     * @param string $name
     * @return string
     */
    protected function getInstanceCacheGroup($name = '')
    {
        return implode('/', array_filter(["shipping", ($this->key ?: '0'), trim($name)]));
    }
}
