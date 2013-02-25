<?php


if (!defined('IN_TINY')) {
    exit();
}

class Template {

    protected static $_instance;

    public $viewDir;

    public $compileDir;

    public $leftDelimiter = '<!--\s?{';

    public $rightDelimiter = '}\s?-->';

    protected $_options = array();

    public $layout;
    
    protected $cacheFile;
    
    protected $cacheStatus = false;

    public function __construct() {

        $this->viewDir    = VIEW_DIR;
        $this->compileDir = CACHE_DIR . 'views' . DIRECTORY_SEPARATOR;
    }

    protected function getViewFile($fileName) {

        return $this->viewDir . $fileName . '.html';
    }

    protected function getCompileFile($fileName) {

        return $this->compileDir . $fileName . '.cache.php';
    }

    protected function createCompileFile($compileFile, $content) {

        $compileDir = dirname($compileFile);
        $this->makeDir($compileDir);

        $content = "<?php if(!defined('IN_TINY')) exit(); ?>\r\n" . $content;

        return file_put_contents($compileFile, $content, LOCK_EX);
    }


    protected function isCompile($viewFile, $compileFile) {

        return (is_file($compileFile) && (filemtime($compileFile) >= filemtime($viewFile))) ? false : true;
    }

    protected function makeDir($dirName) {

        if (!$dirName) {
            return false;
        }

        if (!is_dir($dirName)) {
            mkdir($dirName, 0777, true);
        } else if (!is_writable($dirName)) {
            chmod($dirName, 0777);
        }
    }


    public function assign($key, $value = null) {

        if(!$key) {
            return false;
        }

        if(is_array($key)) {
            foreach ($key as $k=>$v) {
                $this->_options[$k] = $v;
            }
        } else {
            $this->_options[$key] = $value;
        }

        return true;
    }


    protected function parseFileName($fileName = null) {

        if (is_null($fileName)) {
            $controllerId     = Tiny::getControllerName();
            $actionId         = Tiny::getActionName();
            $fileName         = $controllerId . '/' . $actionId;

        } else {
            if (strpos($fileName, '/') !==  false) {
                $fileNameArray      = explode('/', $fileName);
                $fileName           = trim($fileNameArray[0]) . '/' . trim($fileNameArray[1]);
            } else {
                $controllerId       = Tiny::getControllerName();
                $fileName           = $controllerId . '/' . $fileName;
            }
        }

        return $fileName;
    }


    protected function loadViewFile($viewFile) {

        if (!is_file($viewFile)) {
            trigger_error('The view file: ' . $viewFile . ' is not exists!', E_USER_ERROR);
        }

        $viewContent = file_get_contents($viewFile);

        return $this->handleViewFile($viewContent);
    }

    public function setTheme($themeName = 'default') {

        return $this->viewDir = THEME_DIR . $themeName . DIRECTORY_SEPARATOR;
    }


    public function setLayout($layoutName = null) {

        return $this->layout = $layoutName;
    }


    protected function handleViewFile($viewContent) {

        if (!$viewContent) {
            return false;
        }

        $regexArray = array(
        '#'.$this->leftDelimiter.'\s*include\s+(.+?)\s*'.$this->rightDelimiter.'#is',
        '#'.$this->leftDelimiter.'php\s+(.+?)'.$this->rightDelimiter.'#is',
        '#'.$this->leftDelimiter.'\s?else\s?'.$this->rightDelimiter.'#i',
        '#'.$this->leftDelimiter.'\s?\/if\s?'.$this->rightDelimiter.'#i',
        '#'.$this->leftDelimiter.'\s?\/loop\s?'.$this->rightDelimiter.'#i',
        );

        $replaceArray = array(
        "<?php \$this->render('\\1'); ?>",
        "<?php \\1 ?>",
        "<?php } else { ?>",
        "<?php } ?>",
        "<?php } } ?>",
        );

        $viewContent = preg_replace($regexArray, $replaceArray, $viewContent);

        $patternArray = array(
        '#'.$this->leftDelimiter.'\s*(\$.+?)\s*'.$this->rightDelimiter.'#i',
        '#'.$this->leftDelimiter.'\s?(if\s.+?)\s?'.$this->rightDelimiter.'#i',
        '#'.$this->leftDelimiter.'\s?(elseif\s.+?)\s?'.$this->rightDelimiter.'#i',
        '#'.$this->leftDelimiter.'\s?(loop\s.+?)\s?'.$this->rightDelimiter.'#i',
        '#'.$this->leftDelimiter.'\s*(widget\s.+?)\s*'.$this->rightDelimiter.'#is',
        );
        $viewContent = preg_replace_callback($patternArray, array($this, 'parseTags'), $viewContent);

        $viewContent = preg_replace('#\?\>\s*\<\?php\s#s', '', $viewContent);
        $viewContent = str_replace(array("\r\n", "\n", "\t"), '', $viewContent);

        return $viewContent;
    }


    protected function parseTags($tag) {

        $tag = stripslashes(trim($tag[1]));

        if(empty($tag)) {
            return '';
        }

        if (substr($tag, 0, 1) == '$') {
            return '<?php echo ' . $this->getVal($tag) . '; ?>';
        } else {

        	$tag_sel = array_shift(explode(' ', $tag));
            switch ($tag_sel) {

                case 'if' :
                    return $this->_compileIfTag(substr($tag, 3));
                    break;

                case 'elseif' :
                    return $this->_compileIfTag(substr($tag, 7), true);
                    break;

                case 'loop' :
                    return $this->_compileForeachStart(substr($tag, 5));
                    break;

                case 'widget' :
                    return $this->_compileWidgetTag(substr($tag, 7));
                    break;

                default :
                    return $tag_sel;
                    break;
            }
        }
    }

    protected function _compileIfTag($tagArgs, $elseif = false) {

        preg_match_all('#\-?\d+[\.\d]+|\'[^\'|\s]*\'|"[^"|\s]*"|[\$\w\.]+|!==|===|==|!=|<>|<<|>>|<=|>=|&&|\|\||\(|\)|,|\!|\^|=|&|<|>|~|\||\%|\+|\-|\/|\*|\@|\S#i', $tagArgs, $match);
        $tokens = $match[0];

        $tokenArray = array();
        foreach ($match[0] as $vaule) {
            $tokenArray[] = $this->getVal($vaule);
        }
        $tokenStr = implode(' ', $tokenArray);
        unset($tokenArray);

        return ($elseif === false) ? '<?php if (' . $tokenStr . ') { ?>' : '<?php } else if (' . $tokenStr . ') { ?>';
    }

    protected function _compileForeachStart($tagArgs) {

        preg_match_all('#(\$.+?)\s+(.+)#i', $tagArgs, $match);
        $loopVar = $this->getVal($match[1][0]);

        return '<?php if (is_array(' . $loopVar . ')) { foreach (' . $loopVar . ' as ' . $match[2][0] . ') { ?>';
    }

    protected function _compileWidgetTag($tagArgs) {

        $pos = strpos($tagArgs, '$');

        if ($pos !== false) {
            $widgetId  = trim(substr($tagArgs, 0, $pos));
            $params    = $this->getVal(trim(substr($tagArgs, $pos)));

            return '<?php Controller::widget(\'' . $widgetId . '\', ' . $params . '); ?>';
        }

        return '<?php Controller::widget(\'' . $tagArgs . '\'); ?>';
    }

    protected function getVal($val) {

        if (strpos($val, '.') === false) {
            return $val;
        }

        $valArray = explode('.', $val);
        $_varName = array_shift($valArray);

        return $_varName . '[\'' . implode('\'][\'', $valArray) . '\']';
    }

    public function render($fileName, $_data = array(), $return = false) {

        if (!$fileName) {
            return false;
        }

        if ($_data && is_array($_data)) {
            extract($_data, EXTR_PREFIX_SAME, 'data');
            unset($_data);
        }

        $fileName       = $this->parseFileName($fileName);

        $viewFile       = $this->getViewFile($fileName);
        $compileFile    = $this->getCompileFile($fileName);

        if ($this->isCompile($viewFile, $compileFile)) {
            $viewContent = $this->loadViewFile($viewFile);
            $this->createCompileFile($compileFile, $viewContent);
        } else {
            ob_start();
            include $compileFile;
            $viewContent = ob_get_clean();
        }

        if (!$return) {
            echo $viewContent;
        } else {
            return $viewContent;
        }
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
        $this->makeDir($cacheDir);

        return file_put_contents($cacheFile, $content, LOCK_EX);
    }

    public function display($fileName = null) {

        if (!empty($this->_options)) {
            extract($this->_options, EXTR_PREFIX_SAME, 'data');
            $this->_options = array();
        }

        if ($this->layout) {
            $layoutFile      = $this->viewDir . 'layout/' . $this->layout . '.html';
            $layoutState    = is_file($layoutFile) ? true : false;
        } else {
            $layoutState    = false;
        }

        $fileName      = $this->parseFileName($fileName);

        $viewFile      = $this->getViewFile($fileName);
        $compileFile   = $this->getCompileFile($fileName);

        if ($this->isCompile($viewFile, $compileFile)) {
            $viewContent = $this->loadViewFile($viewFile);
            $this->createCompileFile($compileFile, $viewContent);
        }

        if (!$layoutState) {
            ob_start();
            include $compileFile;
            $htmlContent = ob_get_clean();
        } else {

            $layoutCompileFile = $this->getCompileFile('layout/' . $this->layout);
            if ($this->isCompile($layoutFile, $layoutCompileFile)) {
                $layoutContent = $this->loadViewFile($layoutFile);
                $this->createCompileFile($layoutCompileFile, $layoutContent);
            }

            ob_start();
            include $compileFile;
            $content = ob_get_clean();

            ob_start();
            include $layoutCompileFile;
            $htmlContent = ob_get_clean();
        }

        echo $htmlContent;

        if ($this->cacheStatus == true) {
            $this->createCacheFile($this->cacheFile, $htmlContent);
        }
    }

    public function __destruct() {
        $this->_options = array();
    }

     public static function getInstance(){

         if (!self::$_instance instanceof self) {
             self::$_instance = new self();
         }

        return self::$_instance;
    }
}