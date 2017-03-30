<?php

namespace MocaBonita\tools;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Response;
use MocaBonita\view\View;

/**
 * Gerenciamento de respostas do moça bonita
 *
 * @author Jhordan Lima
 * @category WordPress
 * @package moca_bonita\tools
 * @copyright Divisão de Projetos e Desenvolvimento - DPD
 * @copyright Núcleo de Tecnologia da Informação - NTI
 * @copyright Universidade Estadual do Maranhão - UEMA
 */
class MbRespostas extends Response
{

    /**
     * Váriavel que armazenda o request
     *
     * @var MbRequisicoes
     */
    protected $request;

    /**
     * @var string
     */
    protected $content;

    /**
     * @return MbRequisicoes
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param MbRequisicoes $request
     * @return MbRespostas
     */
    public function setRequest(MbRequisicoes $request)
    {
        $this->request = $request;
        return $this;
    }

    /**
     * Processar resposta para o navegador
     *
     * @param mixed $dados Resposta para enviar ao navegador
     *
     * @return MbRespostas
     */
    public function setConteudo($content)
    {

        if ($this->request->method() == "GET") {
            $this->statusCode = 200;
        } elseif ($this->request->method() == "POST" || $this->request->method() == "PUT") {
            $this->statusCode = 201;
        } elseif ($content instanceof \Exception) {
            $this->statusCode = $content->getCode();
            $this->statusCode = $this->statusCode < 300 ? 400 : $this->statusCode;
        } else {
            $this->statusCode = 204;
        }

        //Verificar se a página atual é ajax
        if ($this->request->isAjax()) {
            $content = $this->respostaAjax($content);
            //Caso a requisição não seja ajax
        } else {
            $content = $this->respostaHtml($content);
        }

        //Tratar resposta
        $this->content = $content;
        return $this;
    }

    /**
     *
     */
    public function getContent()
    {
        if ($this->request->isAjax() && is_array($this->content)) {
            wp_send_json($this->content, $this->statusCode);
        } else {
            parent::setContent($this->content);
            echo parent::getContent();
        }
    }

    /**
     * Redirecionar uma página
     *
     * @param string $url
     */
    public function redirect($url)
    {
        header("Location: {$url}");
        exit();
    }

    /**
     * Transformar o array em JSON e formatar o retorno
     *
     * @param array|\Exception $dados Os dados para resposta do Moça Bonita
     */
    protected function respostaAjax($dados)
    {
        if ($dados instanceof Arrayable) {
            $dados = $dados->toArray();

        } //Se os dados for uma string, é adicionado ao atributo content do Moça Bonita
        elseif (is_string($dados)) {
            $dados = ['content' => $dados];

        } //Se não for array ou string, então retorna vázio
        elseif (!is_array($dados) && !$dados instanceof \Exception) {
            $dados = $this->respostaAjax(new \Exception("Nenhum conteúdo válido foi enviado!"));

        }

        //Callback de resposta de sucesso do Moça Bonita
        $respostaSucesso = function ($codigo) use (&$dados) {
            return [
                'meta' => ['code' => $codigo],
                'data' => $dados,
            ];
        };

        //Callback de resposta de erro do Moça Bonita
        $respostaErro = function ($codigo) use (&$dados) {

            if ($dados instanceof MbException) {
                $data = $dados->getDadosArray();
            } else {
                $data = null;
            }

            return [
                'meta' => [
                    'code' => (int)$codigo,
                    'error_message' => $dados->getMessage(),
                ],
                'data' => $data,
            ];
        };

        return $dados instanceof \Exception ? $respostaErro($this->statusCode) : $respostaSucesso($this->statusCode);
    }

    /**
     * Gerar resposta html
     *
     * @param $dados
     * @return string
     */
    protected function respostaHtml($dados){
        //Caso a resposta seja uma exception
        if ($dados instanceof MbException) {
            $dados = "<div class='notice notice-error'><p>{$dados->getDadosView($dados)}</p></div>";
        } elseif ($dados instanceof \Exception) {
            $dados = "<div class='notice notice-error'><p>{$dados->getMessage()}</p></div>";
            //Caso seja uma view
        } elseif ($dados instanceof View) {
            $dados = $dados->render();
            //Caso seja algum valor diferente de string
        } elseif (!is_string($dados)) {
            ob_start();
            var_dump($dados);
            $dados = ob_get_contents();
            ob_end_clean();
        }

        return $dados;
    }

    /**
     * Processar cabeçalhos
     *
     */
    public function processarHeaders()
    {
        foreach ($this->headers->all() as $key => &$header) {
            header("{$key}: {$header[0]}");
        }
    }
}