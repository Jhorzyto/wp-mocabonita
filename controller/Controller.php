<?php

namespace MocaBonita\controller;

use MocaBonita\tools\MBException;
use MocaBonita\view\View;


/**
 * Classe de gerenciamento de controller do moçabonita.
 *
 *
 * @author Jhordan Lima
 * @category WordPress
 * @package \MocaBonita\controller
 * @copyright Copyright (c) 2016
 * @copyright Divisão de Projetos e Desenvolvimento - DPD
 * @copyright Núcleo de Tecnologia da Informação - NTI
 * @copyright Universidade Estadual do Maranhão - UEMA
 */
abstract class Controller
{

    /**
     * Contém a view atual da ação da controller.
     *
     * @var View
     */
    protected $view;

    /**
     * Contém o método de request utilizando para acessar a página. Geralmente 'GET', 'POST', 'PUT' ou 'DELETE'.
     *
     * @var string
     */
    protected $metodoRequisicao;

    /**
     * Um array associativo de variáveis passados para o script atual via método HTTP POST, PUT ou DELETE
     * quando utilizado application/x-www-form-urlencoded ou multipart/form-data como valor do cabeçalho
     * HTTP Content-Type na requisição ou RAW Data enviando um JSON
     *
     * @var string[]
     */
    protected $conteudo;

    /**
     * Um array associativo de variáveis passadas para o script atual via o método HTTP GET
     *
     * @var string[]
     */
    protected $httpGet = [];

    /**
     * Contém a página atual do wordpress obtida atráves do método httpGet['page']
     *
     * @var string
     */
    protected $page;

    /**
     * Contém a ação atual da página do wordpress obtida atráves do método httpGet['action']
     *
     * @var string
     */
    protected $action;

    /**
     * Contém a informação se está em uma página administrativa do Wordpress
     *
     * @var boolean
     */
    protected $admin;

    /**
     * Contém a informação se está em uma página ajax do Wordpress
     *
     * @var boolean
     */
    protected $ajax;

    /**
     * Contém a informação se está em uma página shortcode do Wordpress
     *
     * @var boolean
     */
    protected $shortcode;

    /**
     * Construtor da Controller.
     */
    protected final function __construct()
    {
        $this->view = new View();
        $this->view->setTemplate('index');
        $this->metodoRequisicao = 'GET';
        $this->conteudo = [];
        $this->httpGet = [];
        $this->page = 'no_page';
        $this->action = 'no_action';
        $this->admin = false;
        $this->ajax = false;
        $this->shortcode = false;

        //Verificar se existe algum método inicializar no service para executa-lo
        if (method_exists($this, 'inicializar')) {
            $this->inicializar();
        }
    }

    /**
     * Ação principal da controller
     *
     * Se o retorno for null, ele irá chamar a view desta controller e redenrizar
     * Se o retorno for string, ele irá imprimir a string na tela
     * Se o retorno for View, ele irá redenrizar a view desta controller
     * Se o retorno for qualquer outro tipo, ele irá fazer um var_dump do retorno
     *
     * @return null|string|View|void
     */
    public function indexAction()
    {

    }

    /**
     * @return View
     */
    public final function getView()
    {
        return $this->view;
    }

    /**
     * @param View $view
     */
    public final function setView(View $view)
    {
        $this->view = $view;
    }

    /**
     * @return string
     */
    public final function getMetodoRequisicao()
    {
        return $this->metodoRequisicao;
    }

    /**
     * @param string $metodoRequisicao
     */
    public final function setMetodoRequisicao($metodoRequisicao)
    {
        $this->metodoRequisicao = $metodoRequisicao;
    }

    /**
     * Receber conteudo enviado no corpo da requisição
     *
     * @param string|null $key
     * @return array|string|null
     */
    public final function getConteudo($key = null)
    {
        if (is_null($key))
            return $this->conteudo;
        elseif (isset($this->conteudo[$key]))
            return $this->conteudo[$key];
        else
            return null;
    }

    /**
     * @param array $conteudo
     */
    public final function setConteudo(array $conteudo)
    {
        $this->conteudo = $conteudo;
    }

    /**
     * @param string|null $key
     * @return array|string|null
     */
    public final function getHttpGet($key = null)
    {
        if (is_null($key))
            return $this->httpGet;
        elseif (isset($this->httpGet[$key]))
            return $this->httpGet[$key];
        else
            return null;
    }

    /**
     * @param array $httpGet
     */
    public final function setHttpGet(array $httpGet)
    {
        $this->httpGet = $httpGet;
    }

    /**
     * @return string
     */
    public final function getPage()
    {
        return $this->page;
    }

    /**
     * @param string $page
     */
    public final function setPage($page)
    {
        $this->page = $page;
    }

    /**
     * @return string
     */
    public final function getAction()
    {
        return $this->action;
    }

    /**
     * @param string $action
     */
    public final function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * @return bool
     */
    public final function isAdmin()
    {
        return $this->admin;
    }

    /**
     * @param bool $isAdmin
     */
    public final function setAdmin($isAdmin)
    {
        $this->admin = $isAdmin;
    }

    /**
     * @return bool
     */
    public final function isAjax()
    {
        return $this->ajax;
    }

    /**
     * @param bool $isAjax
     */
    public final function setAjax($isAjax)
    {
        $this->ajax = $isAjax;
    }

    /**
     * @return bool
     */
    public final function isShortcode()
    {
        return $this->shortcode;
    }

    /**
     * @param bool $isShortcode
     */
    public final function setShortcode($isShortcode)
    {
        $this->shortcode = $isShortcode;
    }

    /**
     * Metodo para carregamento pelo MocaBonita
     *
     * @param array $data
     */
    public final function mocabonita(array $data)
    {
        foreach ($data as $method => $value)
            $this->{$method} = $value;
    }

    /**
     * @param string $url
     * @param array $params
     */
    protected final function redirect($url, array $params = [])
    {
        if (is_string($url)) {
            $url .= !empty($params) ? "?" . http_build_query($params) : "";
            header("Location: {$url}");
            exit();
        }
    }

    /**
     * Construtor de Controller
     *
     * @param string|null $controllerNome
     * @throws MBException
     * @return Controller
     */
    public static function factory($controllerNome = null)
    {
        if (is_null($controllerNome)) {
            $controllerNome = get_called_class();
            return new $controllerNome();
        }

        $controller = new $controllerNome();

        if (!$controller instanceof Controller)
            throw new MBException("O Controller {$controllerNome} definido não extendeu a controller do MocaBonita!");

        return $controller;
    }

    /**
     * Método clone do tipo privado previne a clonagem dessa instância
     * da classe
     *
     * @return void
     */
    private function __clone()
    {
    }

    /**
     * Método unserialize do tipo privado para prevenir a desserialização
     * da instância dessa classe.
     *
     * @return void
     */
    private function __wakeup()
    {
    }
}
