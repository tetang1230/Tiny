<?php

if (!defined('IN_TINY')) {
    exit();
}

abstract class Controller {

    protected static $_view;

    public static $_config = array();

    public static $_moduleNameArray = array();

    public function __construct() {

        date_default_timezone_set(TINY_TIMEZONE);

        $sessionDir = CACHE_DIR . 'temp';
        if (is_dir($sessionDir) && is_writable($sessionDir)) {
            session_save_path($sessionDir);
        }

        if (get_magic_quotes_runtime()) {
            @set_magic_quotes_runtime(0);
        }
		

        if (!get_magic_quotes_gpc()) {
            $_POST    = $this->addSlashes($_POST);
            $_GET     = $this->addSlashes($_GET);
           	// $_SESSION = $this->addSlashes($_SESSION);
            $_COOKIE  = $this->addSlashes($_COOKIE);
        }

        $this->initView();

        $this->init();
    }

    public static function get($string, $defaultParam = null) {

        return Request::get($string, $defaultParam);
    }

    public static function post($string, $defaultParam = null) {

       return Request::post($string, $defaultParam);
    }


    public static function requestVars($optionName = 'post') {

        return Request::requestVars($optionName);
    }


    public static function getParams($string, $defaultParam = null) {

       return Request::getParams($string, $defaultParam);
    }


    public static function getCliParams($string , $defaultParam = null) {

       return Request::getCliParams($string, $defaultParam);
    }

    public static function halt($message, $level = 'Error') {

        if (empty($message)) {
            return false;
        }

        $trace            = debug_backtrace();
        $sourceFile       = $trace[0]['file'] . '(' . $trace[0]['line'] . ')';

        $traceString      = '';
        foreach ($trace as $key=>$t) {
            $traceString .= '#'. $key . ' ' . $t['file'] . '('. $t['line'] . ')' . $t['class'] . $t['type'] . $t['function'] . '(' . implode('.',  $t['args']) . ')<br/>';
        }

        include_once TINY_ROOT . 'views/html/exception.php';

        if (TINY_DEBUG === false) {
            Log::write($message, $level);
        }

        exit();
    }

    public static function showMessage($message, $gotoUrl = null, $limitTime = 5) {

        if (!$message) {
            return false;
        }

        if (!is_null($gotoUrl)) {
            $limitTime    = 1000 * $limitTime;
            if ($gotoUrl == -1) {
                $gotoUrl  = 'javascript:history.go(-1);';
                $message .= '<br/><a href="javascript:history.go(-1);" target="_self">如果你的浏览器没反应,请点击这里...</a>';
            } else{
                $gotoUrl  = str_replace(array("\n","\r"), '', $gotoUrl);
                $message .= '<br/><a href="' . $gotoUrl . '" target="_self">如果你的浏览器没反应,请点击这里...</a>';
            }
            $message .= '<script type="text/javascript">function doit_redirect_url(url){location.href=url;}setTimeout("doit_redirect_url(\'' . $gotoUrl . '\')", ' . $limitTime . ');</script>';
        }

        $messageTemplateFile = VIEW_DIR . 'error/message.php';

        is_file($messageTemplateFile) ? include_once $messageTemplateFile : include_once TINY_ROOT . 'views/html/message.php';

        exit();
    }

    public static function dump($data, $option = false) {

        if(!$option){
            echo '<pre>';
            print_r($data);
            echo '</pre>';
        } else {
            ob_start();
            var_dump($data);
            $output = ob_get_clean();

            $output = str_replace('"', '', $output);
            $output = preg_replace('/\]\=\>\n(\s+)/m', '] => ', $output);

            echo '<pre>', $output, '</pre>';
        }

        exit;
    }


    public static function getServerName() {

        $serverName = !empty($_SERVER['HTTP_HOST']) ? strtolower($_SERVER['HTTP_HOST']) : $_SERVER['SERVER_NAME'];
        $serverPort = ($_SERVER['SERVER_PORT'] == '80') ? '' : ':' . (int)$_SERVER['SERVER_PORT'];

        $secure = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 1 : 0;

        return ($secure ? 'https://' : 'http://') . $serverName . $serverPort;
    }


    public static function getBaseUrl() {

        return Dispatcher::getBaseUrl();
    }


    public static function getSelfUrl() {

        return self::createUrl(Tiny::getControllerName() . URL_SEGEMENTATION . Tiny::getActionName());
    }


    public static function getActionUrl($actionName) {

        if (empty($actionName)) {
            return false;
        }

        return self::createUrl(Tiny::getControllerName() . URL_SEGEMENTATION . $actionName);
    }


    public static function getAssetUrl($dirName = null) {

        $assetUrl = self::getBaseUrl() . 'assets/';

        if (!is_null($dirName)) {
            $assetUrl .= $dirName . '/';
        }

        return $assetUrl;
    }

    public static function getThemeUrl($themeName = null){

        $themeDirName = is_null($themeName) ? 'default' : $themeName;

        return self::getBaseUrl() . 'themes/' . $themeDirName . '/';
    }


    public function redirect($url){

        if (!$url) {
            return false;
        }

        if (!headers_sent()) {
            header("Location:" . $url);
        }else {
            echo '<script type="text/javascript">location.href="' . $url . '";</script>';
        }

        exit();
    }


    public static function createUrl($route, $params = null, $routingMode = true) {

        return Dispatcher::createUrl($route, $params, $routingMode);
    }



    public static function instance($className) {

        //参数判断
        if (!$className) {
            return false;
        }

        return Tiny::singleton($className);
    }


    public static function model($modelName) {

        if (!$modelName) {
            return false;
        }

        $modelName = ucfirst(trim($modelName)).'Model';

        return Tiny::singleton($modelName);
    }

   
    public static function module($moduleName) {

        if (!$moduleName) {
            return false;
        }

        if (!isset(self::$_moduleNameArray[$moduleName])) {
            $module_file  = MODULE_DIR . $moduleName . DIRECTORY_SEPARATOR;
            $_module_name = ucfirst(strtolower($moduleName));
            $module_file .= $_module_name . 'Module.class.php';

            self::import($module_file);
            self::$_moduleNameArray[$moduleName] = self::instance($_module_name . 'Module');
        }

        return self::$_moduleNameArray[$moduleName];
    }



    public static function import($fileName) {

        if (!$fileName) {
            return false;
        }

        $fileUrl = ((strpos($fileName, '/') !== false) || (strpos($fileName, '\\') !== false)) ? realpath($fileName) : realpath(EXTENSION_DIR . $fileName . '.class.php');

        return Tiny::loadFile($fileUrl);
    }


    public static function getConfig($fileName) {

        if (!$fileName) {
            return false;
        }

        if (!isset(self::$_config[$fileName])) {
            $filePath = CONFIG_DIR . $fileName . '.ini.php';
            if (!is_file($filePath)) {
                self::halt('The config file:' . $fileName . '.ini.class is not exists!');
            }
            self::$_config[$fileName] = include_once $filePath;
        }

        return self::$_config[$fileName];
    }

    public function setTheme($themeName = 'default') {

        return self::$_view->setTheme($themeName);
    }

    public function setLayout($layoutName = null) {

        return self::$_view->setLayout($layoutName);
    }


    public function cache($cacheId = null, $lifetime = null) {

        return self::$_view->cache($cacheId, $lifetime);
    }

    public function assign($keys, $value = null) {

        return self::$_view->assign($keys, $value);
    }


    public function display($fileName = null) {

        return self::$_view->display($fileName);
    }


    public static function widget($widgetName, $params = null) {

        if (!$widgetName) {
            return false;
        }

        $widgetName = ucfirst(trim($widgetName)) . 'Widget';
        Tiny::singleton($widgetName)->renderContent($params);

        return true;
    }


    public function render($fileName, $_data = array(), $return = false) {

        return self::$_view->render($fileName, $_data, $return);
    }

    public function ajax($status = true, $info = null, $data = array()) {

        $result             = array();
        $result['status']   = $status;
        $result['info']     = !is_null($info) ? $info : '';
        $result['data']     = $data;

        header("Content-Type:text/html; charset=utf-8");
        exit(json_encode($result));
    }

    protected static function stripSlashes($string) {

        if (!$string) {
            return false;
        }

        if (!is_array($string)) {
            return stripslashes($string);
        }

        foreach ($string as $key=>$value) {
            $string[$key] = self::stripSlashes($value);
        }

        return $string;
    }
    
    protected static function addSlashes($mix){
    	
    	if (!$mix) {
    		return false;
    	}
    	
    	if (!is_array($mix)) {
    		return addslashes($mix);
    	}
    	
    	foreach ($mix as $key=>$value) {
    		$string[$key] = self::addslashes($value);
    	}
    	
    	return $string;
    }

   
    protected function initView() {

        $viewFile     = TINY_ROOT . 'lib/' . ((TINY_VIEW === false) ? 'View.class.php' : 'Template.class.php');

        Tiny::loadFile($viewFile);

        self::$_view   = (TINY_VIEW === false) ? View::getInstance() : Template::getInstance();

        return true;
    }

 
    protected function init() {

        return true;
    }
}