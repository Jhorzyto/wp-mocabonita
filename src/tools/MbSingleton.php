<?php

namespace MocaBonita\tools;

/**
 * Main class of the MocaBonita Singleton
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
abstract class MbSingleton
{
    /**
     * Stores instances of classes
     *
     * @var MbSingleton[]
     */
    protected static $instances = [];

    /**
     * Singleton construct
     *
     */
    final protected function __construct()
    {
        $this->init();
    }

    /**
     * Method to be started
     *
     */
    protected function init(){
        //
    }

    /**
     * The singleton pattern is useful when we need to make sure that we only have a single instance of a class for
     * the entire request lifecycle in a Web application. This usually occurs when we have global objects
     * (such as a configuration class) or a resource (Such as an event queue).
     *
     * @return static
     */
    public final static function getInstance()
    {
        $className = get_called_class();

        if (!isset(self::$instances[$className])){
            self::$instances[$className] = new $className();
        }

        return self::$instances[$className];
    }

    /**
     * The __clone () magic method is declared private to prevent cloning of an instance of the class through
     * the clone operator.
     *
     */
    final private function __clone()
    {
        //
    }

    /**
     * The magic method __wakeup () is declared as private to avoid unserializing an instance of the class via the
     * global unserialize () function.
     *
     */
    final private function __wakeup()
    {
        //
    }

    /**
     * var_dump debug
     *
     */
    public static function var_dump(){
        var_dump(self::$instances);
        exit();
    }

}