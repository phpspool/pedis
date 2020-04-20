<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Spool\Pedis\Constants;

use Spool\Pedis\Exception\PedisException;
/**
 * @method static getMessage(int $errorCode)
 *
 * @author 陈浩波
 */
abstract class AbstractConstants
{
    public static function __callStatic(string $name, array $arguments)
    {
        if (!self::startsWith($name, 'get')) {
            throw new ConstantsException('The function is not defined!');
        }

        if (!isset($arguments) || count($arguments) === 0) {
            throw new ConstantsException('The Code is required');
        }
        $code = $arguments[0];
        $class = get_called_class();

        $message = self::getValue($class, $code);
        return $message;
    }
    
    /**
     * Determine if a given string starts with a given substring.
     *
     * @param array|string $needles
     */
    private static function startsWith(string $haystack, $needles): bool
    {
        foreach ((array) $needles as $needle) {
            if ($needle !== '' && substr($haystack, 0, strlen($needle)) === (string) $needle) {
                return true;
            }
        }
        return false;
    }
    
    private static function getValue($class, $code): string
    {
        $re = new \ReflectionClass(new $class);
        $consts = $re->getConstants();
        $flip = array_flip($consts);
        $name = $flip[$code];
        $doc = $re->getReflectionConstant($name)->getDocComment();
        $str = str_replace('/**', '', $doc);
        $str = str_replace(' * @', '', $str);
        $str = str_replace('*/', '', $str);
        $cmd = explode('(', trim($str));
        $methods = get_class_methods(self::class);
        if (!$cmd || !$cmd[0] || !in_array($cmd[0], $methods)) {
            return $code;
        } else {
            $method = $cmd[0];
        }
        return self::$method($str);
    }
    
    public static function Message(string $msg): string
    {
        $tmp = str_replace(__FUNCTION__, '', trim($msg));
        $result = substr($tmp, 2, -2);
        return $result;
    }
}
