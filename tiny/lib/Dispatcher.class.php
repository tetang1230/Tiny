<?php

class Dispatcher {
	
	public static function parseUrl(){
		
		if (isset($_GET['c'])) {
			
			$controllerName      = ($_GET['c'] == true) ? htmlspecialchars(trim($_GET['c'])) : DEFAULT_CONTROLLER;
			$actionName          = (isset($_GET['a']) && $_GET['a'] == true) ? htmlspecialchars(trim($_GET['a'])) : DEFAULT_ACTION;
		
			return array('c' => ucfirst(strtolower($controllerName)), 'a' => strtolower($actionName));
		}
		
		if (isset($_SERVER['SCRIPT_NAME']) && isset($_SERVER['REQUEST_URI'])) {

			$pathUrlString = str_replace(str_replace('/' . ENTRY_SCRIPT_NAME, '', $_SERVER['SCRIPT_NAME']), '', $_SERVER['REQUEST_URI']);
			$pathUrlString = str_replace(URL_SUFFIX, '', $pathUrlString);

			$pos = strpos($pathUrlString, '?');
			if ($pos !== false) {
				$pathUrlString = substr($pathUrlString, 0, $pos);
			}
		
			$urlInfoArray = explode(URL_SEGEMENTATION, str_replace('/', URL_SEGEMENTATION, $pathUrlString));
		
			$controllerName  = (isset($urlInfoArray[1]) && $urlInfoArray[1] == true) ? $urlInfoArray[1] : DEFAULT_CONTROLLER;
			$actionName  = (isset($urlInfoArray[2]) && $urlInfoArray[2] == true) ? $urlInfoArray[2] : DEFAULT_ACTION;
		
			if (($totalNum = sizeof($urlInfoArray)) > 4) {
				for ($i = 3; $i < $totalNum; $i += 2) {
					if (!$urlInfoArray[$i]) {
						continue;
					}
					$_GET[$urlInfoArray[$i]] = $urlInfoArray[$i + 1];
				}
			}
			
			unset($urlInfoArray);
		
			return array('c' => ucfirst(strtolower($controllerName)), 'a' => strtolower($actionName));
		}
				
		return array('c' => DEFAULT_CONTROLLER, 'a' => DEFAULT_ACTION);
	}
	
	
	public static function createUrl($route, $params = null, $routingMode = true) {
	
		if (!$route) {
			return false;
		}
	
		$url      = self::getBaseUrl() . ((DOIT_REWRITE === false) ? ENTRY_SCRIPT_NAME . URL_SEGEMENTATION : '');
		if ($routingMode == true) {
			$url .= str_replace('/', URL_SEGEMENTATION, $route);
		} else {
			$route_array = explode('/', $route);
			$url .= '?controller=' . trim($route_array[0]) . '&action=' . trim($route_array[1]);
			unset($route_array);
		}
	
		if (!is_null($params) && is_array($params)) {
			$paramsUrl = array();
			if ($routingMode == true) {
				foreach ($params as $key=>$value) {
					$paramsUrl[] = trim($key) . URL_SEGEMENTATION . trim($value);
				}
				$url .= URL_SEGEMENTATION . implode(URL_SEGEMENTATION, $paramsUrl) . ((DOIT_REWRITE === false) ? '' : URL_SUFFIX);
			} else {
				$url  .= '&' . http_build_query($params);
			}
		}
	
		return str_replace('//', URL_SEGEMENTATION, $url);
	}
	
	public static function getBaseUrl() {
	
		$url = str_replace(array('\\', '//'), '/', dirname($_SERVER['SCRIPT_NAME']));
		return (substr($url, -1) == '/') ? $url : $url . '/';
	}
}

?>