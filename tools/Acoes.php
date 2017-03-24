<?php

namespace MocaBonita\tools;

/**
 * Classe de Ações do Wordpress
 *
 * @author Jhordan Lima
 * @category WordPress
 * @package \MocaBonita\Tools
 * @copyright Copyright (c) 2016
 * @copyright Divisão de Projetos e Desenvolvimento - DPD
 * @copyright Núcleo de Tecnologia da Informação - NTI
 * @copyright Universidade Estadual do Maranhão - UEMA
 */
class Acoes
{

    /**
     * Controller da página
     *
     * @var Paginas
     */
    private $pagina;

    /**
     * Nome da Action
     *
     * @var string
     */
    private $nome;

    /**
     * Verificar se a página precisa de login
     *
     * @var bool
     */
    private $login;

    /**
     * Verificar se a página é ajax
     *
     * @var bool
     */
    private $ajax;

    /**
     * Verificar método de requisição
     *
     * @var string
     */
    private $requisicao;

    /**
     * Metodo da Controller
     *
     * @var string
     */
    private $metodo;

    /**
     * Complemento do nome do método na controller
     *
     * @var string
     */
    private $complemento;

    /**
     * Verificar se a ação é um shortcode
     *
     * @var bool
     */
    private $shortcode;

    /**
     * Capacidade da ação
     *
     * @var string
     */
    private $capacidade;

    /**
     * @return Paginas
     * @throws MBException
     */
    public function getPagina()
    {
        if (is_null($this->pagina))
            throw new MBException("Nenhuma página foi definida para essa ação!");

        return $this->pagina;
    }

    /**
     * @param Paginas $pagina
     * @return Acoes
     */
    public function setPagina(Paginas $pagina)
    {
        $this->pagina = $pagina;
        return $this;
    }

    /**
     * @return string
     */
    public function getNome()
    {
        return $this->nome;
    }

    /**
     * @param string $nome
     * @return Acoes
     */
    public function setNome($nome)
    {
        $this->nome = $nome;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isLogin()
    {
        return $this->login;
    }

    /**
     * @param boolean $login
     * @return Acoes
     */
    public function setLogin($login = true)
    {
        $this->login = $login;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isAjax()
    {
        return $this->ajax;
    }

    /**
     * @param boolean $ajax
     * @return Acoes
     */
    public function setAjax($ajax = true)
    {
        $this->ajax = $ajax;
        return $this;
    }

    /**
     * @return string
     */
    public function getRequisicao()
    {
        return $this->requisicao;
    }

    /**
     * Método de requisição da ação, se null, permite todos os métodos
     *
     * @param string|null $requisicao
     * @return Acoes
     */
    public function setRequisicao($requisicao = "GET")
    {
        $this->requisicao = $requisicao;
        return $this;
    }

    /**
     * Nome do método na controller
     *
     * @return string
     */
    public function getMetodo()
    {
        return $this->metodo . $this->complemento;
    }

    /**
     * @param string $metodo
     * @return Acoes
     */
    public function setMetodo($metodo)
    {
        $this->metodo = $metodo;
        return $this;
    }

    /**
     * @return string
     */
    public function getComplemento()
    {
        return $this->complemento;
    }

    /**
     * @param string $complemento
     * @return Acoes
     */
    public function setComplemento($complemento)
    {
        $this->complemento = $complemento;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isShortcode()
    {
        return $this->shortcode;
    }

    /**
     * @param boolean $shortcode
     * @return Acoes
     */
    public function setShortcode($shortcode = true)
    {
        $this->shortcode = $shortcode;
        return $this;
    }

    /**
     * @return string
     */
    public function getCapacidade()
    {
        return $this->capacidade;
    }

    /**
     * @param string $capacidade
     * @return Acoes
     */
    public function setCapacidade($capacidade = "read")
    {
        $this->capacidade = $capacidade;
        return $this;
    }

    /**
     * Construtor da Classe Ações
     *
     * @param Paginas $pagina
     * @param string $nome
     * @param bool $login
     * @param bool $ajax
     * @param string $requisicao
     */
    public function __construct(Paginas $pagina, $nome, $login = true, $ajax = false, $requisicao = null)
    {
        $this->setPagina($pagina)
            ->setNome($nome)
            ->setLogin($login)
            ->setAjax($ajax)
            ->setRequisicao($requisicao)
            ->setMetodo($nome)
            ->setShortcode(false)
            ->setComplemento('Action')
            ->setCapacidade(null);
    }

    /**
     * Verificar se o método existe
     *
     * @return true|false
     */
    public function metodoValido()
    {
        return method_exists($this->pagina->getController(), $this->getMetodo());
    }
}