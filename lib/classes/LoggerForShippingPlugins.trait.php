<?php
/**
 * @author Serge Rodovnichenko <serge@syrnik.com>
 * @copyright Serge Rodovnichenko, 2019
 * @license
 */

namespace Syrnik\dostavistaShipping;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use SergeR\CakeUtility\Inflector;
use SergeR\ProcessLogger;
use waLog;
use waSystemConfig;

trait LoggerForShippingPlugins
{
    /** @var ProcessLogger|LoggerInterface|null */
    protected $logger;

    /** @var array */
    protected $log_timers = [];

    /**
     * @return ProcessLogger|LoggerInterface|null
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @param LoggerInterface $logger
     * @return $this
     */
    public function setLogger(LoggerInterface $logger = null)
    {
        $this->logger = $logger;
        return $this;
    }

    /**
     * Стартует логгер, если он еще не запущен и если включен режим отладки
     * @param string $loglevel
     */
    protected function startLogger($loglevel = LogLevel::DEBUG)
    {
        if (waSystemConfig::isDebug() && !($this->getLogger() instanceof LoggerInterface)) {
            $this->setLogger(new ProcessLogger($loglevel));
        }
    }

    protected function logProcess($action = 'custom', $options = [])
    {
        $logger = $this->getLogger();
        if (!($logger instanceof LoggerInterface)) {
            return null;
        }

        $default_options = ['message' => '', 'data' => [], 'loglevel' => LogLevel::NOTICE];

        $options = array_merge($default_options, $options);

        switch ($action) {
            case 'start_timer':
                $this->log_timers[$options['data']['name']] = microtime(true);
                return null;
                break;
            case 'end_timer':
                $name = $options['data']['name'];
                if (isset($this->log_timers[$name])) {
                    $total = microtime(true) - $this->log_timers[$name];
                    $options['data']['total'] = number_format($total, 2, '.', '');
                } else {
                    return null;
                }
                break;
        }

        $action = Inflector::camelize($action);
        $options = LoggerActionFormatter::$action($options);

        $logger->log($options['loglevel'], $options['message'], $options['data']);

        if ((strtolower($action) === 'flush') && ($logger instanceof ProcessLogger)) {
            $process_log = $logger->flush();
            waLog::log($process_log, 'shipping/dostavista.log');
            return $process_log;
        }

        return null;
    }
}