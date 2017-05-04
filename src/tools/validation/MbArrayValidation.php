<?php

namespace MocaBonita\tools\validation;

use Exception;


/**
 * Array validation class
 *
 * @author Jhordan Lima <jhorlima@icloud.com>
 * @category WordPress
 * @package \MocaBonita\tools\validation
 * @copyright Jhordan Lima 2017
 * @copyright Divisão de Projetos e Desenvolvimento - DPD
 * @copyright Núcleo de Tecnologia da Informação - NTI
 * @copyright Universidade Estadual do Maranhão - UEMA
 * @version 3.1.0
 *
 * List of possible arguments for this class:
 *
 * @uses $arguments['json'] (boolean) : Validate array as string json
 * @uses $arguments['min'] (float) : Lower numbers of array elements
 * @uses $arguments['max'] (float) : Larger numbers of array elements (it requires the $arguments['min'])
 * @uses $arguments['callable'] (string | callable($value)) : Function to validate array elements
 */
class MbArrayValidation extends MbValidationBase
{
    /**
     * Implement validation
     *
     * @param mixed $value
     *
     * @param array $arguments
     *
     * @throws \Exception
     *
     * @return array $value
     */
    public function validate($value, array $arguments = [])
    {
        $min = isset($arguments['min']) ? $arguments['min'] : false;
        $max = isset($arguments['max']) ? $arguments['max'] : false;
        $callable = isset($arguments['callable']) ? $arguments['callable'] : false;
        $json = isset($arguments['json']) ? (bool)$arguments['json'] : false;

        if ($json && $this->isJson($value)) {
            $value = json_decode($value);
        }

        $isArray = is_array($value);

        if (!$isArray) {
            throw new Exception("O atributo '{$this->getAttribute()}' não é um array!");
        }

        $count = count($value);

        if ($min && is_numeric($min)) {
            $min = intval($min);
        } else {
            $min = false;
        }

        if ($max && is_numeric($max)) {
            $max = intval($max);
        } else {
            $max = false;
        }

        if ($min) {

            if ($count < $min) {
                throw new Exception("O atributo '{$this->getAttribute()}' deve ter no minimo '{$min}' elemento(s)!");
            } elseif ($max && $count > $max) {
                throw new Exception("O atributo '{$this->getAttribute()}' deve ter no máximo '{$max}' elemento(s)!");
            }
        }

        if ($callable) {
            $false = 0;

            if (is_string($callable) && function_exists($callable)) {
                foreach ($value as $item) {
                    if (!call_user_func_array($callable, $item)) {
                        $false++;
                    }
                }
            } elseif (is_callable($callable)) {
                foreach ($value as $item) {
                    if (!$callable($item)) {
                        $false++;
                    }
                }
            } else {
                throw new Exception("Não foi possivel aplicar a validação no atributo '{$this->getAttribute()}'!");
            }

            if ($false > 0) {
                throw new Exception("{$false} elemento(s) do atributo'{$this->getAttribute()}' não passou(passaram) na validação!");
            }
        }

        return $value;
    }

    /**
     * Check if string is a json
     *
     * @param $string
     *
     * @return bool
     *
     * @throws Exception
     */
    protected function isJson($string)
    {
        if (!is_string($string)) {
            throw new Exception("O atributo '{$this->getAttribute()}' não é um JSON válido!");
        }
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}