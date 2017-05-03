<?php

namespace MocaBonita\tools;

use MocaBonita\model\MbSessionModel;
use Symfony\Component\HttpFoundation\Session\Session as Base;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;

/**
 * Main class of the MocaBonita Session
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
class MbSession extends Base
{
    /**
     * Class Instance
     *
     * @var MbSession
     */
    protected static $instance;

    /**
     * Get instance.
     *
     * @return MbSession
     */
    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            $model   = new MbSessionModel();
            $storage = new NativeSessionStorage();

            MbMigration::enablePdoConnection();

            $pdoHandle = new PdoSessionHandler(
                MbMigration::connection()->getPdo(),
                [
                    'db_table'  => $model->getTable(),
                    'db_id_col' => $model->getPrimaryKey(),
                ]
            );

            if(!MbMigration::schema()->hasTable($model->getTable())){
                $pdoHandle->createTable();
            }

            $storage->setSaveHandler($pdoHandle);

            static::$instance = new static($storage);
        }

        return static::$instance;
    }
}