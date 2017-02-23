<?php
/**
 * Historico de Alterações
 *
 * @created_at 1.0.0 : 01/02/2017 Jhordan Lima - Emissão inicial
 *
 */

namespace MocaBonita\tools;

/**
 * Classe Singleton - Implementar o design pattern Singleton
 *
 * O padrão singleton é útil quando precisamos ter certeza de que só temos uma única instância de uma classe para
 * todo o ciclo de vida do pedido em um aplicativo da Web. Isso normalmente ocorre quando temos objetos
 * globais (como uma classe de configuração) ou um recurso compartilhado (como uma fila de eventos).
 *
 * @author Jhordan Lima - jhordanlima.uema.dpd@gmail.com
 *
 * @category PHP
 *
 * @package Documentacao\DesignPatterns\Singleton
 * @version 1.0.0
 * @copyright Copyright (c) 2017 NTI UEMA
 * @date 31/01/2017
 */
abstract class Singleton
{
    /**
     * Atributo que armazena as instancias das classes
     *
     * @var Singleton[]
     */
    protected static $instances = [];

    /**
     * O construtor __construct() é declarado como private para evitar a criação de uma nova instância fora da classe
     * através do operador new.
     *
     * Executa o método inicializar, caso exista algum método pra ser executado no construtor
     *
     */
    final protected function __construct()
    {
        if (method_exists($this, 'inicializar')) {
            $this->inicializar();
        }
    }

    /**
     * O padrão singleton é útil quando precisamos ter certeza de que só temos uma única instância de uma classe para
     * todo o ciclo de vida do pedido em um aplicativo da Web. Isso normalmente ocorre quando temos objetos
     * globais (como uma classe de configuração) ou um recurso compartilhado (como uma fila de eventos).
     *
     */
    public static function getInstance()
    {
        $nomeClasse = get_called_class();

        if (!isset(self::$instances[$nomeClasse]))
            self::$instances[$nomeClasse] = new $nomeClasse();

        $oInstance = self::$instances[$nomeClasse];
        return $oInstance;
    }

    /**
     * O método mágico __clone() é declarado como private para impedir a clonagem de uma instância da classe através
     * do operador clone.
     *
     */
    final private function __clone()
    {
        //
    }

    /**
     * O método mágico __wakeup() é declarado como private para evitar unserializing de uma instância da classe via
     * a função global unserialize ().
     *
     */
    final private function __wakeup()
    {
        //
    }

}