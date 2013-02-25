<?php
/**
 *  Flare.php
 *
 *  @author      Yoshinobu Kinugasa <kinugasa@gree.co.jp>
 *  @package     Cascade_Driver
 *  @subpackage  KVS
 */
if (Cascade_Driver_KVS_Libmemcached::is_enable($notice = FALSE)) {
    /**
     *  Cascade_Driver_KVS_Flare
     *
     *  @author      Yoshinobu Kinugasa <kinugasa@gree.co.jp>
     *  @package     Cascade_Driver
     *  @subpackage  KVS
     */
    class Cascade_Driver_KVS_Flare
        extends Cascade_Driver_KVS_Libmemcached
    {
        // {{{ get_server_list
        /**
         *  サーバリストを取得
         *
         *  クライアントに登録するサーバリストを取得する。
         *
         *  @return  array  サーバーリスト
         */
        protected /* array */
            function get_server_list(/* void */)
        {
            $pos_list = array(
                getmypid() % count($this->dsn['extra']['hostspec']),
                time()     % count($this->dsn['extra']['hostspec']),
            );
            $server_list = array();
            foreach ($pos_list as $pos) {
                $hostspec = $this->dsn['extra']['hostspec'][$pos];
                list($host, $port) = (strpos($hostspec, ':') !== FALSE)
                    ? explode(':', $hostspec)
                    : array($hostspec, NULL);
                $server_list[] = array($host, $port);
            }
            return $server_list;
        }
        // }}}
    }
} else {
    /**
     *  Cascade_Driver_KVS_Flare
     *
     *  @author      Yoshinobu Kinugasa <kinugasa@gree.co.jp>
     *  @package     Cascade_Driver
     *  @subpackage  KVS
     */
    class Cascade_Driver_KVS_Flare
        extends Cascade_Driver_KVS_Memcached
    {
        // {{{ get_server_list
        /**
         *  サーバリストを取得
         *
         *  クライアントに登録するサーバリストを取得する。
         *
         *  @return  array  サーバーリスト
         */
        protected /* array */
            function get_server_list(/* void */)
        {
            $pos_list = array(
                getmypid() % count($this->dsn['extra']['hostspec']),
                time()     % count($this->dsn['extra']['hostspec']),
            );
            $server_list = array();
            foreach ($pos_list as $pos) {
                $hostspec = $this->dsn['extra']['hostspec'][$pos];
                list($host, $port) = (strpos($hostspec, ':') !== FALSE)
                    ? explode(':', $hostspec)
                    : array($hostspec, NULL);
                $server_list[] = array($host, $port);
            }
            return $server_list;
        }
        // }}}
    }
}

