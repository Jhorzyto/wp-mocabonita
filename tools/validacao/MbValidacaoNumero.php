<?php

namespace MocaBonita\tools\validacao;

use Exception;


/**
 * Validação de Números Números, valor minimo e maximo podendo ser definido como argumentos.
 *
 * float (bool) : Converter para float
 * min (float): Menor valor possível
 * max (float): Maior valor possivel (precisa definir o min)
 *
 */
class MbValidacaoNumero extends MbModeloValidacao
{
    /**
     * @param mixed $valor valor para validar
     * @param array $argumentos argumentos para validar
     * @throws \Exception caso ocorra algum erro
     *
     * @return integer|float $valor valor com ou sem mascara
     */
    public function validar($valor, array $argumentos = [])
    {
        $isNumero = is_numeric($valor);
        $min = isset($argumentos['min']) ? $argumentos['min'] : false;
        $max = isset($argumentos['max']) ? $argumentos['max'] : false;
        $float = isset($argumentos['float']) ? (bool)$argumentos['float'] : false;

        if (!$isNumero) {
            throw new Exception("O atributo '{$this->getAtributo()}' não é um número!");
        }

        if ($float) {
            $valor = $valor + 0;
            $valor = (float)$valor;
        } else {
            $valor = (int)$valor;
        }

        if ($min && is_numeric($min)) {
            $min = $min + 0;
        } else {
            $min = false;
        }

        if ($max && is_numeric($max)) {
            $max = $max + 0;
        } else {
            $max = false;
        }

        if ($min) {

            if ($valor < $min) {
                throw new Exception("O atributo '{$this->getAtributo()}' deve ser maior ou igual a '{$min}'!");
            } elseif ($max && $valor > $max) {
                throw new Exception("O atributo '{$this->getAtributo()}' deve ser menor ou igual a '{$max}'!");
            }
        }

        return $valor;
    }
}