<?php
/**
 * Created by PhpStorm.
 * User: terskine
 * Date: 9/20/18
 * Time: 2:05 PM
 */

namespace Magento\JZI;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
//use Magento\JZI\JZILogger;
include_once ('JZILogger.php');

class LoggingUtil
{
    /**
     * Private Map of Logger instances, indexed by Class Name.
     *
     * @var array
     */
    private $loggers = [];

    /**
     * Singleton LoggingUtil Instance
     *
     * @var LoggingUtil
     */
    private static $INSTANCE;

    /**
     * Singleton accessor for instance variable
     *
     * @return LoggingUtil
     */
    public static function getInstance()
    {
        if (self::$INSTANCE == null) {
            self::$INSTANCE = new LoggingUtil();
        }

        return self::$INSTANCE;
    }

    /**
     * Constructor for Logging Util
     */
    private function __construct()
    {
        // private constructor
    }

    /**
     * Creates a new logger instances based on class name if it does not exist. If logger instance already exists, the
     * existing instance is simply returned.
     *
     * @param string $clazz
     * @return MftfLogger
     * @throws \Exception
     */
    public function getLogger($clazz)
    {
        if ($clazz == null) {
            throw new \Exception("You must pass a class to receive a logger");
        }

        if (!array_key_exists($clazz, $this->loggers)) {
            $logger = new JZILogger($clazz);
            $logger->pushHandler(new StreamHandler($this->getLoggingPath()));
            $this->loggers[$clazz] = $logger;
        }

        return $this->loggers[$clazz];
    }

    /**
     * Function which returns a static path to the the log file.
     *
     * @return string
     */
    private function getLoggingPath()
    {
        return "jzi.log";
    }
}