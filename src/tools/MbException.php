<?php

namespace MocaBonita\tools;

use Illuminate\Contracts\Support\Arrayable;
use Katzgrau\KLogger\Logger;
use MocaBonita\MocaBonita;
use MocaBonita\view\MbView;

/**
 * Main class of the MocaBonita Exception
 *
 * @author Jhordan Lima <jhorlima@icloud.com>
 * @category WordPress
 * @package \MocaBonita\tools
 * @copyright Jhordan Lima 2017
 * @copyright Divisão de Projetos e Desenvolvimento - DPD
 * @copyright Núcleo de Tecnologia da Informação - NTI
 * @copyright Universidade Estadual do Maranhão - UEMA
 * @version 3.1.0
 */
class MbException extends \Exception
{

    /**
     * Stored if exception log is required
     *
     * @var bool
     */
    protected static $registerExceptionLog;

    /**
     * Stored log path
     *
     * @var string
     */
    protected static $exceptionLogPath;

    /**
     * Stored exception data
     *
     * @var null|array|Arrayable
     */
    protected $exceptionData;

    /**
     * Get exception data
     *
     * @return array|string
     */
    public function getExceptionData()
    {
        return $this->exceptionData;
    }

    /**
     * Get exception data in array
     *
     * @return array|null
     */
    public function getExcepitonDataArray()
    {
        if ($this->exceptionData instanceof Arrayable) {
            $this->exceptionData = $this->exceptionData->toArray();
        }

        if (!is_array($this->exceptionData)) {
            $this->exceptionData = null;
        }

        return $this->exceptionData;
    }

    /**
     * Set exception data
     *
     * @param array|Arrayable $exceptionData
     * @return MbException
     */
    public function setExceptionData($exceptionData)
    {
        $this->exceptionData = $exceptionData;
        return $this;
    }

    /**
     * PHP 5 allows developers to declare constructor methods for classes.
     * Classes which have a constructor method call this method on each newly-created object,
     * so it is suitable for any initialization that the object may need before it is used.
     *
     * Note: Parent constructors are not called implicitly if the child class defines a constructor.
     * In order to run a parent constructor, a call to parent::__construct() within the child constructor is required.
     *
     * @param string $msg
     * @param int $code
     * @param null|array|MbView|Arrayable $dados
     *
     * @link http://php.net/manual/en/language.oop5.decon.php
     */
    public function __construct($msg, $code = 400, $dados = null)
    {
        parent::__construct($msg, $code);

        $this->setExceptionData($dados);
    }

    /**
     * Get exception log path
     *
     * @return string
     */
    public static function getExceptionLogPath()
    {
        if(is_null(self::$registerExceptionLog)){
            self::setExceptionLogPath(MbPath::pDir('/logs'));
        }

        return self::$registerExceptionLog;
    }

    /**
     * Set exception log path
     *
     * @param string $exceptionLogPath
     */
    public static function setExceptionLogPath($exceptionLogPath)
    {
        self::$registerExceptionLog = $exceptionLogPath;
    }

    /**
     * Is register exception log
     *
     * @return boolean
     */
    public static function isRegisterExceptionLog()
    {
        return (bool) self::$registerExceptionLog;
    }

    /**
     * Set register exception log
     *
     * @param boolean $registerExceptionLog
     */
    public static function setRegisterExceptionLog($registerExceptionLog = true)
    {
        self::$registerExceptionLog = (bool) $registerExceptionLog;
    }

    /**
     * Register exception log
     *
     * @param \Exception $e
     *
     * @return bool
     */
    protected static function registerExceptionLog(\Exception $e){
        if(!self::isRegisterExceptionLog()){
            return false;
        }

        $logger = new Logger(self::getExceptionLogPath());
        $logger->debug($e->getMessage());

        return true;
    }

    /**
     * Post an error notice on the dashboard and save
     *
     * @param \Exception $e
     */
    public static function registerError(\Exception $e){
        MocaBonita::getInstance()->getMbResponse()->adminNotice($e->getMessage(), 'error');
        self::registerExceptionLog($e);
    }
}