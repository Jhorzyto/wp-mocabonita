<?php

namespace MocaBonita\tools;

use MocaBonita\controller\Requisicoes;

/**
 * Gerenciamento de JSON do moça bonita
 *
 * @author Jhordan Lima
 * @category WordPress
 * @package \MocaBonita\Tools
 * @copyright Divisão de Projetos e Desenvolvimento - DPD
 * @copyright Núcleo de Tecnologia da Informação - NTI
 * @copyright Universidade Estadual do Maranhão - UEMA
 */
final class ServicosJSON
{

    /**
     * Transformar o array em JSON e formatar o retorno
     *
     * @param array $dados Os dados para resposta do Moça Bonita
     * @param Requisicoes $requisicoes
     */
    public static function respostaHTTP(array $dados, Requisicoes $requisicoes)
    {
        //Callback de resposta de sucesso do Moça Bonita
        $respostaSucesso = function ($codigo) use (&$dados) {
            return [
                'meta' => ['code' => $codigo],
                'data' => $dados,
            ];
        };

        //Callback de resposta de erro do Moça Bonita
        $respostaErro = function () use (&$dados) {
            return [
                'meta' => [
                    'code' => (int) $dados['http_method']['code'],
                    'error_message' => $dados['http_method']['error_message'],
                ],
            ];
        };

        if ($requisicoes->isGet())
            wp_send_json(isset($dados['http_method']) ? $respostaErro() : $respostaSucesso(200));

        elseif ($requisicoes->isPost() || $requisicoes->isPut())
            wp_send_json(isset($dados['http_method']) ? $respostaErro() : $respostaSucesso(201));

        elseif ($requisicoes->isDelete())
            wp_send_json(isset($dados['http_method']) ? $respostaErro() : $respostaSucesso(204));

        else
            wp_send_json($dados);
    }

    /**
     * Decodificar JSON em um array
     *
     * @param string $json JSON para decodificar
     * @return array
     */
    public static function decodificar($json)
    {
        return json_decode($json, true);
    }

    /**
     * Codificar um array em JSON
     *
     * @param array $dados Array para codificar
     * @return string
     */
    public static function codificar(array $dados)
    {
        return json_encode($dados);
    }

    /**
     * Verificar se a String é um JSON válido
     *
     * @param string $json String para verificar se o JSON é válido
     * @return true|false true se JSON for válido, false se JSON for inválido
     */
    public static function verificarJSON($json)
    {
        return is_string($json) && is_object(json_decode($json)) ? true : false;
    }
}
