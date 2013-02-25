<?php
/**
 *  DataFormat.php
 *
 *  @author   Yoshinobu Kinugasa <kinugasa@gree.co.jp>
 *  @package  Cascade_DB
 */

/**
 *  [抽象クラス] データ・フォーマット
 *
 *  データ・フォーマットインターフェースを使用した抽象クラス定義。
 *
 *  @package  Cascade_DB
 *  @author   Yoshinobu Kinugasa <kinugasa@gree.co.jp>
 */
abstract class Cascade_DB_DataFormat
    extends    Cascade_Object
{
    // ----[ Methods ]------------------------------------------------
    // {{{ getInstance
    /**
     *  インスタンスを取得する
     *
     *  @param   string                 スキーマ名
     *  @return  Cascade_DB_DataFormat  インスタンス
     */
    public static /* string */
        function getInstance(/* string */ $schema_name)
    {
        static $instances = array();

        if (isset($instances[$schema_name]) === FALSE) {
            $instances[$schema_name] = self::createInstance($schema_name);
        }
        return $instances[$schema_name];
    }
    // }}}
    // {{{ createInstance
    /**
     *  インスタンスを生成する
     *
     *  @param   string                 スキーマ名
     *  @return  Cascade_DB_DataFormat  インスタンス
     */
    public static /* string */
        function createInstance(/* string */ $schema_name)
    {
        // クラス名の取得
        $class_name = Cascade_System_Schema::getDataFormatClassName($schema_name);
        if (class_exists($class_name) === FALSE) {
            $ex_msg = 'Not found DataFormat {schema_name, class_name} %s %s';
            $ex_msg = sprintf($ex_msg, $schema_name, $class_name);
            throw new Cascade_Exception_Exception($ex_msg);
        }
        // インスタンス作成
        $instance = new $class_name;
        if (($instance instanceof Cascade_DB_DataFormat) === FALSE) {
            $ex_msg = 'Invalid a Instance of DataFormat {class} %s';
            $ex_msg = sprintf($ex_msg, $class_name);
            throw new Cascade_Exception_Exception($ex_msg);
        }
        // 作成したインスタンスを返す
        return $instance;
    }
    // }}}
    // {{{ getInterceptors
    /**
     *  インターセプターを取得する
     *
     *  データ種別に応じた割り込み処理実装クラスのインスタンスを取得する。<br/>
     *  割り込み処理はデータ・フォーマット毎に定義される。
     *
     *  @see     Cascade_DB_Interceptor
     *  @return  array  インターセプター・リスト
     */
    public final /* array */
        function getInterceptors(/* void */)
    {
        $icptrs = array();
        foreach ($this->interceptors as $class_name) {
            $icptr = new $class_name;
            $icptrs[] = $icptr;
        }
        return $icptrs;
    }
    // }}}
};