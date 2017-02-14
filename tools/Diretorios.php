<?php

namespace MocaBonita\tools;

define('mb_plg_name'  , explode('/',  plugin_basename(__FILE__))[0]);
define('mb_plg_path'  , WP_PLUGIN_DIR . "/" . mb_plg_name);
define('mb_plg_url'   , WP_PLUGIN_URL . "/" . mb_plg_name);
define('mb_plg_view'  , mb_plg_path . '/view/');
define('mb_plg_js'    , mb_plg_url  . '/public/js/');
define('mb_plg_css'   , mb_plg_url  . '/public/css/');
define('mb_plg_images', mb_plg_url  . '/public/images/');
define('mb_plg_fonts' , mb_plg_url  . '/public/fonts/');
define('mb_plg_bower' , mb_plg_url  . '/public/bower_components/');


/**
 * Gerenciamento de requisições do moça bonita
 *
 * @author Jhordan Lima
 * @category WordPress
 * @package \MocaBonita\Tools
 * @copyright Divisão de Projetos e Desenvolvimento - DPD
 * @copyright Núcleo de Tecnologia da Informação - NTI
 * @copyright Universidade Estadual do Maranhão - UEMA
 */
class Diretorios {

	/**
    * Constante que define o nome do plugin
    *
    * @var string
    */
	const PLUGIN_NOME = mb_plg_name;

	/**
	 * Constante que define o diretório do plugin
	 *
	 * @var string
	 */
	const PLUGIN_DIRETORIO = mb_plg_path;

	/**
	 * Constante que define o diretório view do plugin
	 *
	 * @var string
	 */
	const PLUGIN_VIEW_DIR = mb_plg_view;

	/**
    * Constante que define o diretório javascript do plugin
    *
    * @var string
    */
	const PLUGIN_JS_DIR = mb_plg_js;

	/**
    * Constante que define o diretório css do plugin
    *
    * @var string
    */
	const PLUGIN_CSS_DIR = mb_plg_css;

	/**
    * Constante que define o diretório imagens do plugin
    *
    * @var string
    */
	const PLUGIN_IMAGENS_DIR = mb_plg_images;

	/**
	 * Constante que define o diretório bower_components do plugin
	 *
	 * @var string
	 */
	const PLUGIN_BOWER_DIR = mb_plg_bower;
}
