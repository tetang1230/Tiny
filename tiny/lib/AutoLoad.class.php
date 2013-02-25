<?php

if (!defined('IN_TINY')) {
    exit();
}

class AutoLoad {

	// ----[ Class Constants ]----------------------------------------
	// location variables index
	const LOCATE_SRC_ROOT      = 0x01;
	const LOCATE_IGNORE_PREFIX = 0x02;
	const LOCATE_FILE_EXT      = 0x03;
	
	// ----[ Properties ]---------------------------------------------
	/**
	 *  Infomation map for source files's location
	 *  @var array
	 */
	 protected static $location_map = array(
	 		array(
	 				self::LOCATE_SRC_ROOT      => CASCADE_SRC_ROOT,
	 				//self::LOCATE_SRC_ROOT      => CLASS_SRC_ROOT,
	 				self::LOCATE_IGNORE_PREFIX => '',
	 				self::LOCATE_FILE_EXT      => '.php',
	 		),
	 );
	
	
	public static $coreClassArray = array(
			'Request'           => 'lib/Request.class.php',
			'Model'             => 'lib/Model.class.php',
			'db_mysqli'         => 'lib/db/db_mysqli.class.php',
			'db_mysql'          => 'lib/db/db_mysql.class.php',
			'db_pdo'            => 'lib/db/db_pdo.class.php',
			'Log'               => 'lib/Log.class.php',
			'Widget'            => 'lib/Widget.class.php',
			'View'              => 'lib/View.class.php',
			'Template'          => 'lib/Template.class.php',
			'WidgetTemplate'    => 'lib/WidgetTemplate.class.php',
			'Module'            => 'lib/Module.class.php',
			'db_sqlite'         => 'lib/db/db_sqlite.class.php',
			'db_redis'          => 'lib/db/db_redis.class.php',
			'db_oracle'         => 'lib/db/db_oracle.class.php',
			'db_postgres'       => 'lib/db/db_postgres.class.php',
			'db_mssql'          => 'lib/db/db_mssql.class.php',
			'db_mongo'          => 'lib/db/db_mongo.class.php'
	);

	public static function index($className) {

		if (isset(self::$coreClassArray[$className])) {
			//当$className在核心类引导数组中存在时, 加载核心类文件
			Tiny::loadFile(DOIT_ROOT . self::$coreClassArray[$className]);
		} elseif (substr($className, -10) == 'Controller') {
			//controller文件自动载分析
			if (is_file(CONTROLLER_DIR . $className . '.class.php')) {
				//当文件在controller根目录下存在时,直接加载.
				Tiny::loadFile(CONTROLLER_DIR . $className . '.class.php');
			} else {
				//从controller的名称里获取子目录名称,注:controller文件的命名中下划线'_'相当于目录的'/'.
				$pos = strpos($className, '_');
				if ($pos !== false) {
					//当$controller中含有'_'字符时
					$childDirName      = strtolower(substr($className, 0, $pos));
					$controllerFile     = CONTROLLER_DIR . $childDirName . '/' . $className . '.class.php';
					if (is_file($controllerFile)) {
						//当子目录中所要加载的文件存在时
						Tiny::loadFile($controllerFile);
					} else {
						//当文件在子目录里没有找到时
						Controller::halt('The File:' . $className .'.class.php is not exists!');
					}
				} else {
					//当controller名称中不含有'_'字符串时
					Controller::halt('The File:' . $className .'.class.php is not exists!');
				}
			}
		} else if (substr($className, -5) == 'Model') {
			//modlel文件自动加载分析
			if (is_file(MODEL_DIR . $className . '.class.php')) {
				//当所要加载的model文件存在时
				Tiny::loadFile(MODEL_DIR . $className . '.class.php');
			} else {
				//当所要加载的文件不存在时,显示错误提示信息
				Controller::halt('The Model file: ' . $className . ' is not exists!');
			}
		} else if(substr($className, -6) == 'Widget') {
			//加载所要运行的widget文件
			if (is_file(WIDGET_DIR . $className . '.class.php')) {
				//当所要加载的widget文件存在时
				Tiny::loadFile(WIDGET_DIR . $className . '.class.php');
			} else {
				Controller::halt('The Widget file: ' . $className . ' is not exists!');
			}
		} else {
			// find the file containing class definition
			foreach (self::$location_map as $location) {
			
				$file_path = self::getClassFilePath($className, $location);
				
				if ($file_path === NULL) {
					continue;
				}
			
				// load class, or interface definition
				include($file_path);
				// check defined class, or interface
				if (class_exists($className, $autoload = FALSE)
						|| interface_exists($className, $autoload = FALSE)) {
					return TRUE;
				}
			}
			return FALSE;
		}
	}
	
	
	// ----[ Methods ]------------------------------------------------
	// {{{ register
	/**
	 *  Register a function with the provided load stack
	 *
	 *  @param   $dir_src        The directory PATH sotred files
	 *  @param   $ignore_prefix  (optional) Ignore the prefix name of class
	 *  @param   $file_ext       (optional) THe extenstion of PHP's file
	 */
	public static /** void */
	function register(/** string */ $dir_src,
	/** string */ $ignore_prefix = '',
	/** string */ $file_ext      = CASCADE_AUTOLOAD_FILE_EXT)
	{
		 
		 
		// Checks whether a directory exists.
		if (FALSE === is_dir($dir_src)) {
			 
			$ex_msg = 'Not found directory {path} %s';
			$ex_msg = sprintf($ex_msg, $dir_src);
			throw new Cascade_Exception_SystemException($ex_msg);
		}
	
	
		// Checks whether file's location is registered in internal
		foreach (self::$location_map as $location) {
			if (   $location[self::LOCATE_SRC_ROOT]      == $dir_src
					&& $location[self::LOCATE_IGNORE_PREFIX] == $ignore_prefix
					&& $location[self::LOCATE_FILE_EXT]      == $file_ext) {
				return;
			}
		}
		// register to internal
		self::$location_map[] = array(
				self::LOCATE_SRC_ROOT      => $dir_src,
				self::LOCATE_IGNORE_PREFIX => $ignore_prefix,
				self::LOCATE_FILE_EXT      => $file_ext,
		);
	}
	
	// {{{ getClassFilePath
	/**
	 *  Gets the file contating class, or interface definition.
	 *
	 *  @param   $class_name  The class name
	 *  @param   $location    The file's location infomation
	 *  @return               The file PATH, or FALSE not exists
	 */
	protected static /** void */
	function getClassFilePath(/** string */ $class_name,
	/** array  */ $location)
	{
		$base_name = $class_name;
		if (0 < count($location[self::LOCATE_IGNORE_PREFIX])) {
			$rep_ptn   = sprintf('/^%s/', $location[self::LOCATE_IGNORE_PREFIX]);
			$base_name = preg_replace($rep_ptn, '', $class_name);
		}
		$sep_ns = strpos($base_name, "\\") === FALSE ? '_' : "\\";
		$file_path = $location[self::LOCATE_SRC_ROOT]
		. Cascade::SEPARATOR_DIRECTORY
		. str_replace($sep_ns, Cascade::SEPARATOR_DIRECTORY, $base_name)
		. $location[self::LOCATE_FILE_EXT];
		//echo $file_path;
		if (file_exists($file_path)) {
			return $file_path;
		}
		return NULL;
	}
}