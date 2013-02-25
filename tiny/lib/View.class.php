<?php

if (!defined('IN_TINY')) {
    exit();
}

class View{


    protected static $_instance = null;

    protected $layout;

    protected $theme;

    protected $_options;

    protected $cacheFile;

    protected $cacheStatus;

    public function __construct() {

        $this->_options     = array();
        $this->cacheStatus = false;

        return true;
    }
    
    public function setTheme($themeName = 'default') {

        return $this->theme = $themeName;
    }

    public function setLayout($layoutName = null) {

        return $this->layout = $layoutName;
    }

    public function getViewFile($fileName = null) {

        if (is_null($fileName)) {
            $fileName = Tiny::getControllerName() . '/' . Tiny::getActionName();
        } else {
            $fileName = (strpos($fileName, '/') !== false) ? $fileName : Tiny::getControllerName() . '/' . $fileName;
        }

        $viewFile  = (!empty($this->theme)) ? THEME_DIR . $this->theme . '/' . $fileName : VIEW_DIR . $fileName;
        $viewFile .= '.php';

        if (!is_file($viewFile)) {
            Controller::halt('The view file:' . $viewFile . ' is not exists!');
        }

        return $viewFile;
    }

    protected function parseCacheFile($cacheId) {

        return CACHE_DIR . 'html/' . Tiny::getControllerName() . '/' . md5($cacheId) . '.action.html';
    }

    public function cache($cacheId = null, $lifetime = null) {

        if (is_null($cacheId)) {
            $cacheId = Tiny::getActionName();
        }
        if (is_null($lifetime)) {
            $lifetime = 31536000;
        }

        $cacheFile = $this->parseCacheFile($cacheId);
        if (is_file($cacheFile) && (filemtime($cacheFile) + $lifetime >= time())) {
            include $cacheFile;
            exit();
        }

        $this->cacheStatus = true;
        $this->cacheFile   = $cacheFile;

        return true;
    }

    protected function createCacheFile($cacheFile, $content = null) {

        if (!$cacheFile) {
            return false;
        }
        if (is_null($content)) {
            $content = '';
        }

        $cacheDir = dirname($cacheFile);
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0777, true);
        } else if (!is_writable($cacheDir)) {
            chmod($cacheDir, 0777);
        }

        return file_put_contents($cacheFile, $content, LOCK_EX);
    }

    public function assign($keys, $value = null) {

        //参数分析
        if (!$keys) {
            return false;
        }

        //当$keys为数组时
        if (!is_array($keys)) {
            $this->_options[$keys] = $value;
        } else {
            foreach ($keys as $handle=>$lines) {
                $this->_options[$handle] = $lines;
            }
        }

        return true;
    }

    /**
     * 显示当前页面的视图内容
     *
     * 包括视图页面中所含有的挂件(widget), 视图布局结构(layout), 及render()所加载的视图片段等
     * @access public
     * @param string $fileName 视图名称
     * @return void
     */
    public function display($fileName = null) {

        //分析视图文件路径
        $viewFile = $this->getViewFile($fileName);

        //模板变量赋值
        if (!empty($this->_options)) {
            extract($this->_options, EXTR_PREFIX_SAME, 'data');
            //清空不必要的内存占用
            $this->_options = array();
        }

        //获取当前视图($fileName)的内容
        ob_start();
        include $viewFile;
        $content = ob_get_clean();

        //分析,加载,显示layout视图内容
        $layoutFile = (!empty($this->theme)) ? THEME_DIR . $this->theme . '/layout/' . $this->layout . '.php' : VIEW_DIR . 'layout/' . $this->layout . '.php';

        //分析layout文件是否存在.
        if (is_file($layoutFile)) {
            ob_start();
            include $layoutFile;
            $content = ob_get_clean();
        }

        //显示视图文件内容
        echo $content;

        //当缓存重写开关开启时,创建缓存文件
        if ($this->cacheStatus == true) {
            $this->createCacheFile($this->cacheFile, $content);
        }
    }

    /**
     * 加载并显示视图片段文件内容
     *
     * 相当于include 代码片段，当$return为:true时返回代码代码片段内容,反之则显示代码片段内容
     * @access public
     * @param string  $fileName 视图片段文件名称
     * @param array   $_data     视图模板变量，注：数组型
     * @param boolean $return    视图内容是否为返回，当为true时为返回，为false时则为显示. 默认为:false
     * @return mixed
     */
    public function render($fileName, $_data = array(), $return = false) {

        //参数分析
        if (!$fileName) {
            return false;
        }

        //分析视图文件的路径
        $viewFile = $this->getViewFile($fileName);

        //模板变量赋值
        if (!empty($_data) && is_array($_data)) {
            extract($_data, EXTR_PREFIX_SAME, 'data');
            unset($_data);
        }

        //获取$fileName所对应的视图片段内容
        ob_start();
        include $viewFile;
        $content = ob_get_clean();

        if (!$return) {
            echo $content;
        } else {
            return $content;
        }
    }

    /**
     * 网址(URL)组装操作
     *
     * 组装绝对路径的URL
     * @access public
     * @param string     $route             controller与action
     * @param array     $params         URL路由其它字段
     * @param boolean     $routingMode    网址是否启用路由模式
     * @return string    URL
     */
    public static function createUrl($route, $params = null, $routingMode = true) {

        return Controller::createUrl($route, $params, $routingMode);
    }

    /**
     * 获取当前项目的根目录的URL
     *
     * 用于网页的CSS, JavaScript，图片等文件的调用.
     * @access public
     * @return string     根目录的URL. 注:URL以反斜杠("/")结尾
     */
    public static function getBaseUrl() {

        return Controller::getBaseUrl();
    }

    /**
     * 获取当前运行的Action的URL
     *
     * 获取当前Action的URL. 注:该网址由当前的控制器(Controller)及动作(Action)组成,不含有其它参数信息
     * 如:/index.php/index/list，而非/index.php/index/list/page/5 或 /index.php/index/list/?page=5
     * @access public
     * @return string    URL
     */
    public static function getSelfUrl() {

        return Controller::getSelfUrl();
    }

    /**
     * 获取当前Controller内的某Action的URL
     *
     * 获取当前控制器(Controller)内的动作(Action)的URL. 注:该网址仅由项目入口文件和控制器(Controller)组成。
     * @access public
     * @param string $actionName 所要获取URL的action的名称
     * @return string    URL
     */
    public static function getActionUrl($actionName) {

       return Controller::getActionUrl($actionName);
    }

    /**
     * 获取当前项目asset目录的URL
     *
     * @access public
     * @param string $dirName 子目录名
     * @return string    URL
     */
    public static function getAssetUrl($dirName = null) {

        return Controller::getAssetUrl($dirName);
    }

    /**
     * 获取当前项目themes目录的URL
     *
     * @access public
     * @param string $themeName 所要获取URL的主题名称
     * @return string    URL
     */
    public static function getThemeUrl($themeName = null){

        return Controller::getThemeUrl($themeName);
    }

    /**
     * 加载视图文件的挂件(widget)
     *
     * 加载挂件内容，一般用在视图内容中(view)
     * @access public
     * @param string  $widgetName 所要加载的widget名称,注没有后缀名
     * @param array   $params 参数. 如array('id'=>23)
     * @return boolean
     */
    public static function widget($widgetName, $params = null) {

        return Controller::widget($widgetName, $params);
    }

    /**
     * 析构函数
     *
     * @access public
     * @return void
     */
    public function __destruct() {

        //释放$_options数组
        if ($this->_options) {
            $this->_options = array();
        }

        $this->cacheStatus = false;
    }

    /**
     * 单例模式实例化本类
     *
     * @access public
     * @return object
     */
    public static function getInstance() {

        if (self::$_instance === null) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
}