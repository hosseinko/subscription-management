<?php


namespace App\Libs;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Throwable;

/**
 * Class Logger
 * @method static LogEmergency($filename, $message, $context = null)
 * @method static logAlert($filename, $message, $context = null)
 * @method static logCritical($filename, $message, $context = null)
 * @method static logError($filename, $message, $context = null)
 * @method static logWarning($filename, $message, $context = null)
 * @method static logNotice($filename, $message, $context = null)
 * @method static logInfo($filename, $message, $context = null)
 * @method static logDebug($filename, $message, $context = null)
 *
 * @package Application
 */
class Logger extends BaseLib implements LoggerInterface
{
    private $filename = 'app.log';

    /**
     * @param $filename
     * @param $logLevel
     * @param $message
     * @param null $context
     */
    public static function createLog($filename, $logLevel, $message, $context = null)
    {
        $logger           = new static();
        $logger->filename = $filename;
        $logger->{$logLevel}($message, $context);
    }

    /**
     * @param $name
     * @param $arguments
     */
    public static function __callStatic($name, $arguments)
    {
        $logLevel = strtolower(str_replace('log', '', $name));
        $filename = $arguments[0] ?? 'logger.log';
        $message  = $arguments[1] ?? 'Operation failed';
        $context  = $arguments[2] ?? [];

        self::createLog($filename, $logLevel, $message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function emergency($message, array $context = array())
    {
        $this->log(LogLevel::EMERGENCY, $message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function alert($message, array $context = array())
    {
        $this->log(LogLevel::ALERT, $message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function critical($message, array $context = array())
    {
        $this->log(LogLevel::CRITICAL, $message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function error($message, array $context = array())
    {
        $this->log(LogLevel::ERROR, $message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function warning($message, array $context = array())
    {
        $this->log(LogLevel::WARNING, $message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function notice($message, array $context = array())
    {
        $this->log(LogLevel::NOTICE, $message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function info($message, array $context = array())
    {
        $this->log(LogLevel::INFO, $message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function debug($message, array $context = array())
    {
        $this->log(LogLevel::DEBUG, $message, $context);
    }

    /**
     * @param mixed $level
     * @param string $message
     * @param array $context
     */
    public function log($level, $message, array $context = array())
    {
        try {
            $logDir = storage_path('logs/logger');
            if (!is_dir($logDir)) {
                mkdir($logDir, 0755, true);
            }

            $logFile = $logDir . '/' . $this->filename;

            $ip = request()->ip();

            $logData = '[' . strtoupper($level) . '] - ' . $message . chr(10);
            $logData .= 'Date: ' . now()->toDateTimeString() . ' UTC' . chr(10);
            $logData .= "IP: " . $ip . chr(10);
            if (is_array($context) && count($context) > 0) {
                $logData .= "[Details]" . chr(10);
                foreach ($context as $key => $value) {
                    $logData .= str_repeat(" ", 4) . "$key: ";
                    if (is_array($value)) {
                        $logData .= json_encode($value);
                    } else {
                        $logData .= (string)$value;
                    }
                    $logData .= chr(10);
                }
                $logData .= str_repeat('#', 50) . chr(10);
            }

            file_put_contents($logFile, $logData, FILE_APPEND);
        } catch (Throwable $exception) {
            abort(500, 'Unable to create log file:' . $exception->getMessage());
        }
    }
}
