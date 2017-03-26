<?php

namespace MocaBonita\tools;

use MocaBonita\controller\Controller;
use MocaBonita\MocaBonita;
use MocaBonita\service\Service;

/**
 * Classe de páginas do Wordpress
 *
 * @author Jhordan Lima
 * @category WordPress
 * @package \MocaBonita\Tools
 * @copyright Copyright (c) 2016
 * @copyright Divisão de Projetos e Desenvolvimento - DPD
 * @copyright Núcleo de Tecnologia da Informação - NTI
 * @copyright Universidade Estadual do Maranhão - UEMA
 */
class Paginas
{
    /**
     * Nome da página
     *
     * @var string
     */
    private $nome;

    /**
     * Capacidade da página
     *
     * @var string
     */
    private $capacidade;

    /**
     * Slug da página
     *
     * @var string
     */
    private $slug;

    /**
     * Ícone da página
     *
     * @var string
     */
    private $icone;

    /**
     * Posição da página no menu
     *
     * @var int
     */
    private $posicao;

    /**
     * Página Parente
     *
     * @var Paginas
     */
    private $paginaParente;

    /**
     * Remover página do submenu quando houver
     *
     * @var bool
     */
    private $removerSubMenuPagina;

    /**
     * Lista de Páginas
     *
     * @var Paginas[]
     */
    private $subPaginas = [];

    /**
     * Verificar se é página do menu principal
     *
     * @var bool
     */
    private $menuPrincipal;

    /**
     * Verificar se é página do submenu do wordpress
     *
     * @var bool
     */
    private $submenu;

    /**
     * Objeto Moca Bonita
     *
     * @var MocaBonita
     */
    private $mocaBonita;

    /**
     * Controller da página
     *
     * @var Controller|string
     */
    private $controller;

    /**
     * Verificar se é necessário esconder o menu dessa página
     *
     * @var bool
     */
    private $esconderMenu;

    /**
     * Ações da página
     *
     * @var Acoes[]
     */
    private $acoes = [];

    /**
     * Serviços da página
     *
     * @var array
     */
    private $servicos = [];

    /**
     * Complementos da página
     *
     * @var Assets
     */
    private $assets;

    /**
     * Construir uma página a partir do parente
     *
     * @param Paginas $paginaParente
     * @param bool $menuPrincipal
     * @param int $posicao
     */
    public function __construct(Paginas $paginaParente = null, $menuPrincipal = true, $posicao = 100)
    {
        $this->setNome("Moça Bonita")
            ->setCapacidade("manage_options")
            ->setIcone("dashicons-editor-code")
            ->setEsconderMenu(false)
            ->setAssets(new Assets())
            ->setPaginaParente($paginaParente)
            ->setMenuPrincipal($menuPrincipal)
            ->setSubmenu(!$menuPrincipal)
            ->setPosicao($posicao)
            ->adicionarAcao('index');
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
     * @return Paginas
     */
    public function setNome($nome)
    {
        $this->nome = $nome;
        $this->setSlug($nome);
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
     * @return Paginas
     */
    public function setCapacidade($capacidade)
    {
        $this->capacidade = $capacidade;
        return $this;
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     * @return Paginas
     */
    public function setSlug($slug)
    {
        $this->slug = sanitize_title($slug);
        return $this;
    }

    /**
     * @return string
     */
    public function getIcone()
    {
        return $this->icone;
    }

    /**
     * @param string $icone
     * @return Paginas
     */
    public function setIcone($icone)
    {
        $this->icone = $icone;
        return $this;
    }

    /**
     * @return int
     */
    public function getPosicao()
    {
        return $this->posicao;
    }

    /**
     * @param int $posicao
     * @return Paginas
     */
    public function setPosicao($posicao)
    {
        $this->posicao = $posicao;
        return $this;
    }

    /**
     * @throws MBException
     * @return Paginas
     */
    public function getPaginaParente()
    {
        if (is_null($this->paginaParente))
            throw new MBException("Nenhuma página parente foi definida em {$this->getNome()}");

        return $this->paginaParente;
    }

    /**
     * @param Paginas $paginaParente
     * @return Paginas
     */
    public function setPaginaParente(Paginas $paginaParente = null)
    {
        $this->paginaParente = $paginaParente;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isRemoverSubMenuPagina()
    {
        return $this->removerSubMenuPagina;
    }

    /**
     * @param boolean $removerSubMenuPagina
     * @return Paginas
     */
    public function setRemoverSubMenuPagina($removerSubMenuPagina = true)
    {
        $this->removerSubMenuPagina = $removerSubMenuPagina;
        return $this;
    }

    /**
     * @return Paginas[]
     */
    public function getSubPaginas()
    {
        return $this->subPaginas;
    }

    /**
     * @param string $slug
     * @return Paginas|null
     */
    public function getSubPagina($slug)
    {
        if (!isset($this->subPaginas[$slug]))
            return null;

        return $this->subPaginas[$slug];
    }

    /**
     * @param Paginas $pagina
     * @return Paginas Retorna a SubPagina para melhor tratamento
     */
    public function setSubPagina(Paginas $pagina)
    {
        $this->subPaginas[$pagina->getSlug()] = $pagina;
        $pagina->setPaginaParente($this);
        return $pagina;
    }

    /**
     * @param string $slug
     * @return Paginas Retorna a SubPagina para melhor tratamento
     */
    public function adicionarSubPagina($slug)
    {
        $pagina = new self();
        $pagina->setSlug($slug)
            ->setPaginaParente($this);

        $this->subPaginas[$pagina->getSlug()] = $pagina;
        return $pagina;
    }

    /**
     * @return boolean
     */
    public function isMenuPrincipal()
    {
        return $this->menuPrincipal;
    }

    /**
     * @param boolean $menuPrincipal
     * @return Paginas
     */
    public function setMenuPrincipal($menuPrincipal = true)
    {
        $this->menuPrincipal = $menuPrincipal;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isSubmenu()
    {
        return $this->submenu;
    }

    /**
     * @param boolean $submenu
     * @return Paginas
     */
    public function setSubmenu($submenu = true)
    {
        $this->submenu = $submenu;
        return $this;
    }

    /**
     * @return MocaBonita
     */
    public function getMocaBonita()
    {
        return $this->mocaBonita;
    }

    /**
     * @param MocaBonita $mocaBonita
     * @return Paginas
     */
    public function setMocaBonita(MocaBonita $mocaBonita)
    {
        $this->mocaBonita = $mocaBonita;
        return $this;
    }

    /**
     * @throws MBException caso não exista controller definido
     * @return Controller
     */
    public function getController()
    {
        if (is_string($this->controller)) {
            $this->controller = Controller::create($this->controller);
        } elseif (is_null($this->controller) || !$this->controller instanceof Controller) {
            throw new MBException("Nenhum Controller foi definido para a página {$this->getNome()}.");
        }

        return $this->controller;
    }

    /**
     * @param Controller|string $controller
     * @return Paginas
     */
    public function setController($controller)
    {
        $this->controller = $controller;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isEsconderMenu()
    {
        return $this->esconderMenu;
    }

    /**
     * @param boolean $esconderMenu
     * @return Paginas
     */
    public function setEsconderMenu($esconderMenu = true)
    {
        $this->esconderMenu = $esconderMenu;
        return $this;
    }

    /**
     * @param string $acao
     * @return Acoes|null
     */
    public function getAcao($acao)
    {
        if (!isset($this->acoes[$acao]))
            return null;

        return $this->acoes[$acao];
    }

    /**
     * @param Acoes $acao
     * @return Acoes
     */
    public function setAcao(Acoes $acao)
    {
        $this->acoes[$acao->getNome()] = $acao;
        return $acao;
    }

    /**
     * @param string $nome
     * @return Acoes
     */
    public function adicionarAcao($nome)
    {
        $this->acoes[$nome] = new Acoes($this, $nome);
        return $this->acoes[$nome];
    }

    /**
     * @return array
     */
    public function getServicos()
    {
        return $this->servicos;
    }

    /**
     * @param string $servico
     * @param array $metodos
     * @return Paginas
     */
    public function setServicos($servico, array $metodos)
    {
        $this->servicos[] = Service::configuracoesServicos($servico, $metodos);
        return $this;
    }

    /**
     * @return Assets
     */
    public function getAssets()
    {
        return $this->assets;
    }

    /**
     * @param Assets $assets
     * @return Paginas
     */
    public function setAssets(Assets $assets)
    {
        $this->assets = $assets;
        return $this;
    }

    /**
     * Adicionar as páginas ao menu do wordpress
     *
     */
    public function adicionarMenuWordpress()
    {
        //Adicionar menu principal
        if ($this->isEsconderMenu()) {

            add_submenu_page(
                null,
                $this->getNome(),
                $this->getNome(),
                $this->getCapacidade(),
                $this->getSlug(),
                [$this->getMocaBonita(), 'getConteudo']
            );

        //Adicionar menu principal
        } elseif ($this->isMenuPrincipal()) {

                add_menu_page(
                    $this->getNome(),
                    $this->getNome(),
                    $this->getCapacidade(),
                    $this->getSlug(),
                    [$this->getMocaBonita(), 'getConteudo'],
                    $this->getIcone(),
                    $this->getPosicao()
                );

        //Adicionar submenu
        } elseif ($this->isSubmenu()) {

            add_submenu_page(
                $this->getPaginaParente()->getSlug(),
                $this->getNome(),
                $this->getNome(),
                $this->getCapacidade(),
                $this->getSlug(),
                [$this->getMocaBonita(), 'getConteudo']
            );

            //Remover submenu semelhante ao menu principal
            if ($this->getPaginaParente()->isRemoverSubMenuPagina()) {
                remove_submenu_page($this->getPaginaParente()->getSlug(), $this->getPaginaParente()->getSlug());
                $this->getPaginaParente()->setRemoverSubMenuPagina(false);
            }
        }

    }
}
