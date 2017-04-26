<?php

namespace MocaBonita\tools\validacao;

use \Exception;
use Illuminate\Contracts\Support\Arrayable;
use MocaBonita\tools\MbException;

/**
 * Classe de Validação do Moça Bonita.
 *
 *
 * @author Jhordan Lima
 * @category WordPress
 * @package \MocaBonita\Tools
 * @copyright Copyright (c) 2016
 * @copyright Divisão de Projetos e Desenvolvimento - DPD
 * @copyright Núcleo de Tecnologia da Informação - NTI
 * @copyright Universidade Estadual do Maranhão - UEMA
 */
class MbValidacao implements Arrayable
{
    /**
     * Verificar se ocorreu erro
     *
     * @var bool
     */
    protected $erro = false;

    /**
     * @return boolean
     */
    public function isErro()
    {
        return (bool)$this->erro;
    }

    /**
     * @param boolean $erro
     */
    private function setErro($erro = true)
    {
        $this->erro = (bool)$erro;
    }

    /**
     * Dados para serem validados
     *
     * @var array[]
     */
    protected $dados;

    /**
     * Atributos que podem ser nulos
     *
     * @var string[]
     */
    protected $permitirNulo;

    /**
     * Validacoes
     *
     * @var array[]
     */
    protected $validacoes;

    /**
     * Validacoes
     *
     * @var bool
     */
    protected $removerNaoUsados = false;

    /**
     * Mensagens de erros da validação
     *
     * @var array[]
     */
    protected $mensagens = [];

    /**
     * @param null $chave
     * @return \array[]|mixed|null
     */
    public function getDados($chave = null)
    {
        if(is_null($chave)){
            return $this->dados;
        } elseif(isset($this->dados[$chave])){
            return $this->dados[$chave];
        } else {
            return null;
        }
    }

    /**
     * @param \array[] $dados
     * @return MbValidacao
     */
    public function setDados(array $dados)
    {
        $this->dados = $dados;
        return $this;
    }

    /**
     * @return \string[]
     */
    public function getNulos()
    {
        return $this->permitirNulo;
    }

    /**
     * @param \string[] $permitirNulo
     * @return MbValidacao
     */
    public function setNulos(array $permitirNulo)
    {
        $this->permitirNulo = $permitirNulo;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isRemoverNaoUsados()
    {
        return $this->removerNaoUsados;
    }

    /**
     * @param boolean $removerNaoUsados
     * @return MbValidacao
     */
    public function setRemoverNaoUsados($removerNaoUsados = true)
    {
        $this->removerNaoUsados = (bool)$removerNaoUsados;
        return $this;
    }

    /**
     * Criar uma nova instancia de MbValidacoes
     *
     * @param array $dados
     * @return MbValidacao
     */
    public static function validar(array $dados)
    {
        $validacao = new self();
        return $validacao->setDados($dados);
    }

    /**
     * @param string $atributo
     *
     * @return array[]
     */
    public function getValidacoes($atributo = null)
    {
        if (is_null($atributo)) {
            return $this->validacoes;
        } elseif (isset($this->validacoes[$atributo])) {
            return $this->validacoes[$atributo];
        } else {
            return [];
        }
    }

    /**
     * @param string $atributo
     * @param MbModeloValidacao $validacao
     * @param array $argumentos
     *
     * @return $this
     */
    public function setValidacoes($atributo, MbModeloValidacao $validacao, array $argumentos = [])
    {
        if (is_string($atributo) && is_array($argumentos)) {
            if (!isset($this->validacoes[$atributo])) {
                $this->validacoes[$atributo] = [];
            }

            $this->validacoes[$atributo][] = [
                'atributo' => $atributo,
                'class' => $validacao,
                'argumentos' => $argumentos,
            ];
        }

        return $this;
    }

    /**
     * @param string|null $atributo
     * @return array|\array[]
     */
    public function getMensagens($atributo = null)
    {
        return is_null($atributo) ? $this->mensagens : $this->mensagens[$atributo];
    }

    /**
     * Atribuir todas as mensagens
     *
     * @param array $mensagens
     */
    protected function setMensagens(array $mensagens)
    {
        $this->mensagens = $mensagens;
    }

    /**
     * Atribuir mensagem individual para um atributo
     *
     * @param string $atributo
     * @param string $mensagem
     */
    protected function setMensagem($atributo, $mensagem)
    {
        if (!isset($this->mensagens[$atributo])){
            $this->mensagens[$atributo] = [];
        }

        $this->mensagens[$atributo][] = $mensagem;
    }

    /**
     * Verificar se a validação deu certa
     *
     * @param bool $mbException
     *
     * @return bool
     * @throws MbException
     */
    public function verificar($mbException = false)
    {
        //Obter atributos das regras
        $atributos = array_keys($this->validacoes);

        $this->setMensagens([]);

        foreach ($atributos as $atributo) {

            $existeAtributo = array_key_exists($atributo, $this->dados);
            $atributoNulo = $existeAtributo ? is_null($this->dados[$atributo]) : true;
            $permitirNulo = in_array($atributo, $this->permitirNulo);
            $regrasAtributo = $this->getValidacoes($atributo);

            if (!$atributoNulo && !empty($regrasAtributo)) {
                foreach ($regrasAtributo as $regra) {
                    $regra['class']::getInstance()->setAtributo($atributo);
                    try {
                        $this->dados[$atributo] = $regra['class']::getInstance()->validar(
                            $this->dados[$atributo],
                            $regra['argumentos']
                        );
                    } catch (Exception $e) {
                        $this->setMensagem($atributo, $e->getMessage());
                    }
                }
            } elseif ($atributoNulo && $permitirNulo) {
                $this->dados[$atributo] = null;
            } else {
                $this->setMensagem($atributo, "O atributo '{$atributo}' não pode ser nulo!");
            }
        }

        if ($this->isRemoverNaoUsados()) {
            $atributosEnviados = array_keys($this->dados);

            foreach ($atributosEnviados as &$atributo) {
                if (!in_array($atributo, $atributos)) {
                    unset($this->dados[$atributo]);
                }
            }
        }

        $this->setErro(!empty($this->mensagens) ? true : false);

        if ($mbException && $this->isErro()){
            throw new MbException("Seus dados não passaram na validação!", 400, $this);
        }

        return $this->isErro();
    }

    /**
     * O método mágico __clone() é declarado como private para impedir a clonagem de uma instância da classe através
     * do operador clone.
     *
     */
    final private function __clone()
    {
        //
    }/** @noinspection PhpUnusedPrivateMethodInspection */

    /**
     * O método mágico __wakeup() é declarado como private para evitar unserializing de uma instância da classe via
     * a função global unserialize ().
     *
     */
    final private function __wakeup()
    {
        //
    }

    /**
     * O construtor __construct() é declarado como private para evitar a criação de uma nova instância fora da classe
     * através do operador new.
     *
     */
    protected function __construct()
    {
        //
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'error' => $this->isErro(),
            'messages' => $this->getMensagens(),
            'data' => $this->getDados(),
        ];
    }
}