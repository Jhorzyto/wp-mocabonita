<?php
namespace MocaBonita\model;

use \Exception;

/**
 * Classe Model do Moça Bonita.
 *
 *
 * @author Jhordan Lima
 * @category WordPress
 * @package \MocaBonita\model
 * @copyright Copyright (c) 2016
 * @copyright Divisão de Projetos e Desenvolvimento - DPD
 * @copyright Núcleo de Tecnologia da Informação - NTI
 * @copyright Universidade Estadual do Maranhão - UEMA
 */
abstract class Model
{
    /**
     * Objeto WPDB do Wordpress
     *
     * @var \wpdb
     */
    protected $wpdb;

    /**
     * Nome da tabela do banco
     *
     * @var string
     */
    protected $table;

    /**
     * @return \wpdb
     * @throws \Exception
     */
    public final function getWpdb()
    {
        if(is_null($this->wpdb))
            $this->__construct();

        return $this->wpdb;
    }

    /**
     * @param \wpdb $wpdb
     */
    public final function setWpdb(\wpdb $wpdb)
    {
        $this->wpdb = $wpdb;
    }

    /**
     * Construtor da Model
     *
     */
    public final function __construct()
    {
        global $wpdb;
        $this->setWpdb($wpdb);

        if(is_null($this->table)){
            $classname = get_class($this);
            $this->table = ($pos = strrpos($classname, '\\')) ? substr($classname, $this->table + 1) : $this->table;
            $this->table = strtolower($this->table);
            $this->table = $this->getWpdb()->{$this->table};
        }

        //Verificar se existe algum método inicializar no service para executa-lo
        if(method_exists($this, 'inicializar')){
            $this->inicializar();
        }
    }

    /**
     * Obter o resultado a partir de uma query
     *
     * @param string $query query SQL
     * @param string $outputType tipo de retorno
     * @return array[]|object[]|null $result
     * @throws \Exception caso ocorra algum problema durante o processo
     */
    public final function getDadosQuery($query, $outputType = OBJECT)
    {
        $result = $this->getWpdb()->get_results($query, $outputType);

        if ($result === false || $this->getWpdb()->last_error)
            throw new Exception($this->getWpdb()->last_error);

        return $result;
    }

    /**
     * Obter o resultado a partir de uma query
     *
     * @param string $query query SQL
     * @return array[] $result
     * @throws \Exception caso ocorra algum problema durante o processo
     */
    public final function getDadosQueryArray($query)
    {
        return $this->getDadosQuery($query, ARRAY_A);
    }

    /**
     * Retorna o valor de uma unica tupla da Query. Ex "SELECT COUNT(*) FROM wp_users"
     *
     * @param string $query query SQL
     * @return string|null $result
     * @throws \Exception caso ocorra algum problema durante o processo
     */
    public final function getValorQuery($query)
    {
        $result = $this->getWpdb()->get_var($query);

        if ($result === false || $this->getWpdb()->last_error)
            throw new Exception($this->getWpdb()->last_error);

        return $result;
    }

    /**
     * Receber uma linha da query
     *
     * @param string $query query SQL
     * @param string $outputType tipo de retorno
     * @param integer $rowOffSet offset da linha
     * @return array|object|null $result
     * @throws \Exception caso ocorra algum problema durante o processo
     */
    public final function getLinhaQuery($query, $outputType = OBJECT, $rowOffSet = 0)
    {
        $result = $this->getWpdb()->get_row($query, $outputType, $rowOffSet);

        if ($result === false || $this->getWpdb()->last_error)
            throw new Exception($this->getWpdb()->last_error);

        return $result;
    }

    /**
     * Retrieve an entire columns from a query
     *
     * @param string $query query SQL
     * @param integer $colOffSet offset da coluna
     * @return array $result
     * @throws \Exception caso ocorra algum problema durante o processo
     */
    public final function getColunaQuery($query, $colOffSet = 0)
    {
        $result = $this->getWpdb()->get_col($query, $colOffSet);

        if ($result === false || $this->getWpdb()->last_error)
            throw new Exception($this->getWpdb()->last_error);

        return $result;
    }

    /**
     * Inserir dados na tabela
     *
     * @param array $data dados para inserir
     * @param array|string $format formato de dados inserido no array
     * @return integer ID do registro
     * @throws \Exception caso ocorra algum problema durante o processo
     */
    public final function inserir(array $data, $format = null)    {

        $result = $this->getWpdb()->insert($this->table, $data, $format);

        if ($result === false || $this->getWpdb()->last_error)
            throw new Exception($this->getWpdb()->last_error);

        return $this->getWpdb()->insert_id;
    }

    /**
     * Substituir uma linha na tabela
     *
     * @param array $data dados para substituir
     * @param array $where Um array de regrar para a condição
     * @param array|string $format formato dos dados para atualizar
     * @param array|string $whereFormat formato das regras para atualizar
     * @return integer número de registros alterados
     * @throws \Exception caso ocorra algum problema durante o processo
     */
    public final function atualizar(array $data, array $where, $format = null, $whereFormat = null)
    {
        $result = $this->getWpdb()->update($this->table, $data, $where, $format, $whereFormat);

        if ($result === false || $this->getWpdb()->last_error)
            throw new Exception($this->getWpdb()->last_error);

        return $result;
    }

    /**
     * Apagar linhas na tabela
     *
     * @param array $where Um array de regrar para a condição
     * @param array|string $whereFormat formato das regras para atualizar
     * @return integer número de registros apagados
     * @throws \Exception caso ocorra algum problema durante o processo
     */
    public final function apagar(array $where, $whereFormat = null)
    {
        $result = $this->getWpdb()->delete($this->table, $where, $whereFormat);

        if ($result === false || $this->getWpdb()->last_error)
            throw new Exception($this->getWpdb()->last_error);

        return $result;
    }

    /**
     * Retorna o resultado de uma query
     *
     * @param string $query query SQL
     * @return bool|integer retorna inteiro para SELECT, INSERT, DELETE, UPDATE, etc. Para CREATE, ALTER,
     * TRUNCATE e DROP SQL vai retornar TRUE ou FALSE
     * @throws \Exception caso ocorra algum problema durante o processo
     */
    public final function query($query)
    {
        $result = $this->getWpdb()->query($query);

        if ($result === false || $this->getWpdb()->last_error)
            throw new Exception($this->getWpdb()->last_error);

        return $result;
    }

    /**
     * Iniciar uma transação do WPDB
     *
     * @throws \Exception caso ocorra algum problema durante o processo
     */
    public final function beginTransaction()
    {
        return $this->query("START TRANSACTION;");
    }

    /**
     * Confirmar uma transação
     *
     * @throws \Exception caso ocorra algum problema durante o processo
     */
    public final function commit()
    {
        return $this->query("COMMIT;");
    }

    /**
     * Desfazer uma transação
     *
     * @throws \Exception caso ocorra algum problema durante o processo
     */
    public final function rollBack()
    {
        return $this->query("ROLLBACK;");
    }

    /**
     * Preparar uma query com bindValues
     *
     * @param string $query query SQL
     * @param array $arguments dados para substituir na query
     * @return string $result
     */
    public final function preparar($query, array $arguments)
    {
        return $this->getWpdb()->prepare($query, $arguments);
    }

    /**
     * Escape like query
     *
     * @param string $query query SQL
     * @return string $result
     */
    public final function escaparLike($query)
    {
        return $this->getWpdb()->esc_like($query);
    }

    /**
     * Listar os registros da tabela
     *
     * @param array $columns lista de colunas para listar
     * @param array $where condições para listar
     * @param array $orderByAsc ordenação para listar
     * @return array[] $result
     */
    public function listarTudo(array $columns = [], array $where = [], array $orderByAsc = [])
    {
        $_columns = empty($columns) ? '*' : implode(', ', $columns);
        $_where = empty($where) ? '' : ' WHERE ' . implode(' = ', $where);
        $_orderByAsc = empty($orderByAsc) ? '' : ' ORDER BY ' . implode(', ', $orderByAsc);
        $query = "SELECT {$_columns} FROM {$this->table}{$_where}{$_orderByAsc}";
        return $this->getDadosQuery($query, ARRAY_A);
    }
}
