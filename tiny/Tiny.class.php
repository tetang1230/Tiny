<?php

if (!defined('IN_TINY')) {
	exit();
}

if(!defined('DEFAULT_CONTROLLER')){
	define('DEFAULT_CONTROLLER', 'index');
}

if(!defined('DEFAULT_ACTION')){
	define('DEFAULT_ACTION', 'index');
}

if (!defined('CONTROLLER_DIR')) {
	define('CONTROLLER_DIR', APP_ROOT . 'app/controllers' . DIRECTORY_SEPARATOR);
}

if (!defined('VIEW_DIR')) {
	define('VIEW_DIR', APP_ROOT . 'app/views' . DIRECTORY_SEPARATOR);
}

if (!defined('CACHE_DIR')) {
	define('CACHE_DIR', APP_ROOT . 'cache' . DIRECTORY_SEPARATOR);
}

if (!defined('URL_SEGEMENTATION')) {
	define('URL_SEGEMENTATION', '/');
}

if (!defined('URL_SUFFIX')) {
	define('URL_SUFFIX', '.html');
}

if (!defined('ENTRY_SCRIPT_NAME')) {
	define('ENTRY_SCRIPT_NAME', 'index.php');
}

if (!defined('TINY_ROOT')) {
	define('TINY_ROOT', dirname(__FILE__) . DIRECTORY_SEPARATOR);
}

if (!defined('TINY_DEBUG')) {
	define('TINY_DEBUG', false);
}

if (!defined('TINY_TIMEZONE')) {
	define('TINY_TIMEZONE', 'Asia/ShangHai');
}

if (!defined('TINY_VIEW')) {
	define('TINY_VIEW', false);
}

require_once APP_ROOT . 'tiny/lib/Dispatcher.class.php';

class Tiny {
	
	public static $controller;
	
	public static $action;
	
	public static $_incFiles = array();

	private static $_objects = array();
	
	private static $APP = array();

    public static function execute() {
		
		
        $url_params  = Dispatcher::parseUrl();

        self::$controller = $url_params['c'];
        self::$action     = $url_params['a'];

        $appId = self::$controller . '_' . self::$action;

        if (!isset(self::$APP[$appId])) {

            $controller = self::$controller . 'Controller';
            $action     = self::$action . 'Action';

            self::loadFile(APP_ROOT . 'tiny/lib/Controller.class.php');
			
            if (is_file(CONTROLLER_DIR . $controller . '.class.php')) {
                self::loadFile(CONTROLLER_DIR . $controller . '.class.php');
            } else {
                $pos = strpos($controller, '_');
                if ($pos !== false) {
                    $childDirName     = strtolower(substr($controller, 0, $pos));
                    $controllerFile   = CONTROLLER_DIR . $childDirName . '/' . $controller . '.class.php';

                    if (is_file($controllerFile)) {
                        self::loadFile($controllerFile);
                    } else {
                        self::display404Error();
                    }
                } else {
                    self::display404Error();
                }
            }
            
            $appObject = new $controller();
            
            if (method_exists($controller, $action)){
                self::$APP[$appId] = $appObject->$action();
            } else {
            	
                self::display404Error();
            }
        }

        return self::$APP[$appId];
        
    }

    private static function display404Error() {

        is_file(VIEW_DIR . 'error/error404.html') ? self::loadFile(VIEW_DIR . 'error/error404.html') : self::loadFile(TINY_ROOT . 'views/html/error404.html');

        exit();
    }

    public static function getControllerName() {

        return strtolower(self::$controller);
    }

    public static function getActionName() {

        return self::$action;
    }

    public static function singleton($className) {

        if (!$className) {
            return false;
        }

        $key = trim($className);

        if (isset(self::$_objects[$key])) {
            return self::$_objects[$key];
        }

        return self::$_objects[$key] = new $className();
    }

    public static function loadFile($fileName) {

        if (!$fileName) {
            return false;
        }

        if (!isset(self::$_incFiles[$fileName])) {

            if (!is_file($fileName)) {
                Controller::halt('The file:' . $fileName . ' not found!');
            }

            include_once $fileName;
            self::$_incFiles[$fileName] = true;
        }

        return self::$_incFiles[$fileName];
    }
}

include_once APP_ROOT . 'tiny/lib/AutoLoad.class.php';
spl_autoload_register(array('AutoLoad', 'index'));
