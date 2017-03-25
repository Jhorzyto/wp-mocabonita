<?php

namespace MocaBonita\tools;

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
class Respostas extends Response
{

    /**
     * Váriavel que armazenda o request
     *
     * @var Requisicoes
     */
    protected $request;

    /**
     * @return Requisicoes
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param Requisicoes $request
     * @return Respostas
     */
    public function setRequest(Requisicoes $request)
    {
        $this->request = $request;
        return $this;
    }

    /**
     * Processar resposta para o navegador
     *
     * @param mixed $dados Resposta para enviar ao navegador
     *
     * @return Respostas
     */
    public function setContent($content){

        if($this->request->method() == "GET"){
            $this->statusCode = 200;
        } elseif($this->request->method() == "POST" || $this->request->method() == "PUT") {
            $this->statusCode = 201;
        } elseif ($content instanceof \Exception){
            $this->statusCode = $content->getCode();
        } else {
            $this->statusCode = 204;
        }

        //Verificar se a página atual é ajax
        if ($this->request->isAjax()) {

            //Se os dados for um array, é convertido para JSON na estrutura do Moca Bonita
            if (is_array($content)) {
                $content = $this->respostaJson($content);
            } //Se os dados for uma string, é adicionado ao atributo content do Moça Bonita
            elseif (is_string($content)) {
                $content = $this->respostaJson(['content' => $content]);
            } //Se não for array ou string, então retorna vázio
            elseif ($content instanceof \Exception) {
                $content = $this->respostaJson($content);
            } else {
                $content = $this->respostaJson(new \Exception("Nenhum conteúdo foi enviado!"));
            }
            //Caso a requisição não seja ajax
        } else {
            //Caso a resposta seja uma exception
            if($content instanceof \Exception){
                MBException::adminNotice($content);
                //Caso seja uma view
            } elseif ($content instanceof View){
                $content = $content->render();
                //Caso seja algum valor diferente de string
            } elseif (!is_string($content)){
                ob_start();
                var_dump($content);
                $content = ob_get_contents();
                ob_end_clean();
            }
        }

        //Tratar resposta
        parent::setContent($content);

        return $this;
    }

    /**
     * Redirecionar uma página
     *
     * @param string $url
     */
    public function redirect($url){
        header("Location: {$url}");
        exit();
    }

    /**
     * Transformar o array em JSON e formatar o retorno
     *
     * @param array|\Exception $dados Os dados para resposta do Moça Bonita
     */
    private function respostaJson($dados)
    {
        //Callback de resposta de sucesso do Moça Bonita
        $respostaSucesso = function ($codigo) use (&$dados) {
            return [
                'meta' => ['code' => $codigo],
                'data' => $dados,
            ];
        };

        //Callback de resposta de erro do Moça Bonita
        $respostaErro = function ($codigo) use (&$dados) {
            return [
                'meta' => [
                    'code' => (int) $codigo,
                    'error_message' => $dados->getMessage(),
                ],
            ];
        };

        return $dados instanceof \Exception ? $respostaErro($this->statusCode) : $respostaSucesso($this->statusCode);
    }

    public function processarHeaders(){
        $headers = $this->headers->all();

        WPAction::adicionarCallbackAction('send_headers', function () use ($headers) {
            error_log("chamou");
            foreach ($headers as $key => &$header){
                header("{$key}: {$header[0]}");
            }
        });
    }
}