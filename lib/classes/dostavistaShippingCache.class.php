<?php
/**
 * @author Serge Rodovnichenko <serge@syrnik.com>
 * @copyright Serge Rodovnichenko, 2023
 * @license Webasyst
 */

declare(strict_types=1);

class dostavistaShippingCache
{
    protected const MAIN_GROUP = 'shipping/dostavista';
    protected const CALCULATION_GROUP = self::MAIN_GROUP . '/calculations';
    protected const CALCULATION_TTL = 600; // 10 min
    public const CACHE_CONFIG = 'dostavista_cache';

    protected waCache $cache;

    public function __construct(waCache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @param array $result
     * @param dostavistaShippingApiEntityOrder $order
     * @param string $token
     * @param bool $test_mode
     * @return void
     * @throws waException
     */
    public function saveCalculation(
        array $result,
        dostavistaShippingApiEntityOrder $order,
        string $token,
        bool $test_mode
    ): void {
        $cache_key = $this->createCacheKey($order, $token, $test_mode);
        $this->cache->set($cache_key, $result, self::CALCULATION_TTL, self::CALCULATION_GROUP);
    }

    /**
     * @param dostavistaShippingApiEntityOrder $order
     * @param string $token
     * @param bool $test_mode
     * @return array|null
     * @throws waException
     */
    public function getCalculation(dostavistaShippingApiEntityOrder $order, string $token, bool $test_mode): ?array
    {
        return $this->cache->get($this->createCacheKey($order, $token, $test_mode), self::CALCULATION_GROUP);
    }

    /**
     * @param mixed ...$parts
     * @return string
     * @throws waException
     */
    protected function createCacheKey(...$parts): string
    {
        if (!$parts) {
            throw new waException('Невозможно сгенерировать ключ кэша для пустого списка параметров');
        }
        array_walk($parts, function (&$part): void {
            if (is_scalar($part)) {
                $part = (string)$part;
            } elseif ($part instanceof JsonSerializable) {
                $part = waUtils::jsonEncode($part, JSON_UNESCAPED_UNICODE);
            } elseif (is_object($part) && method_exists($part, '__toString')) {
                $part = (string)$part;
            } elseif (is_array($part)) {
                $part = waUtils::jsonEncode($part, JSON_UNESCAPED_UNICODE);
            } else {
                $part = '';
            }
        });

        return md5(implode(':', $parts));
    }
}
