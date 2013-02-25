<?php

if (!defined('IN_TINY')) {
    exit();
}

class Request {

    public static function get($string, $defaultParam = null) {

        if (!isset($_GET[$string])) {
            return is_null($defaultParam) ? '' : htmlspecialchars(trim($defaultParam));
        }

        if (!is_array($_GET[$string])) {
            $getParams = htmlspecialchars(trim($_GET[$string]));
            return !is_null($getParams) ? $getParams : (is_null($defaultParam) ? '' : htmlspecialchars(trim($defaultParam)));
        }

        foreach ($_GET[$string] as $key=>$value) {
            $getArray[$key] = htmlspecialchars(trim($value));
        }

        return $getArray;
    }

    public static function post($string, $defaultParam = null) {

        if (!isset($_POST[$string])) {
            return is_null($defaultParam) ? '' : htmlspecialchars(trim($defaultParam));
        }

        if (!is_array($_POST[$string])) {
            $postParams = htmlspecialchars(trim($_POST[$string]));
            return !is_null($postParams) ? $postParams : (is_null($defaultParam) ? '' : htmlspecialchars(trim($defaultParam)));
        }

        foreach ($_POST[$string] as $key=>$value) {
            $postArray[$key] = htmlspecialchars(trim($value));
        }

        return $postArray;
    }

    public static function requestVars($type = 'post') {

        $paramArray = array();

        switch ($type) {
            case 'post':
                if (isset($_POST)) {
                    $keyArray = array_keys($_POST);
                    foreach ((array)$keyArray as $name) {
                        $paramArray[$name] = self::post($name);
                    }
                }
                break;

            case 'get':
                if (isset($_GET)) {
                    $keyArray = array_keys($_GET);
                    foreach ((array)$keyArray as $name) {
                        $paramArray[$name] = self::get($name);
                    }
                }
                break;

            case 'request':
                if (isset($_REQUEST)) {
                    $keyArray = array_keys($_GET);
                    foreach ((array)$keyArray as $name) {
                        $paramArray[$name] = ($_REQUEST[$name]) ? htmlspecialchars(trim($_REQUEST[$name])) : '';
                    }
                }
                break;
        }

        return $paramArray;
    }

    public static function getParams($string, $defaultParam = null) {

        $paramValue = self::post($string, $defaultParam);

        return (!$paramValue) ? self::get($string, $defaultParam) : $paramValue;
    }

    public static function getCliParams($string , $defaultParam = null) {

        if (!isset($_SERVER['argv'][$string])) {
            return is_null($defaultParam) ? '' : htmlspecialchars(trim($defaultParam));
        }

        $cliParams = htmlspecialchars(trim($_SERVER['argv'][$string]));
        return ($cliParams) ? $cliParams : (is_null($defaultParam) ? '' : htmlspecialchars(trim($defaultParam)));
    }
}