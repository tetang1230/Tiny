<?php
/**
 *  Cascade Configuration
 */
return $cascade_config = array(

	'system'=>array(
			 // {{{ log
			 /*
        'log' => array(
            'level' => 1,
            'dir'   => array(
                // デフォルト出力先
                'default' => 'D:/workspaces/php_workspace/farm/log',
                // エラー毎に出力先を変更する場合
                'emerg'   => NULL,
                'alert'   => NULL,
                'crit'    => NULL,
                'err'     => NULL,
                'warning' => NULL,
                'notice'  => NULL,
                'info'    => NULL,
                'debug'   => NULL,
            ),
        ),
        // }}}
        // {{{ log#sql
        'log#sql : log' => array(
            'dir' => array(
                'default' => 'D:/workspaces/php_workspace/farm/log/cascade/mysql',
            ),
        ),
        // }}}
        // {{{ log#kvs
        'log#kvs : log' => array(
            'dir' => array(
                'default' => 'D:/workspaces/php_workspace/farm/log/cascade/kvs',
            ),
        ),*/
	    'dsn-config-gree' => array(
	        // gree(mysql)
	        'mysql.var'     => 'mysql_config_list',
	        'mysql.path'    => dirname(__FILE__).'/mysql.ini.php',
        	// gree(memcache)
			
            'memcache.var'  => 'memcache_config_list',
            'memcache.path' => dirname(__FILE__).'/memcache.ini.php',
			
	    )
	 ),
	   
	'schema' => array(    
	  'default' => array(
	            'dataformat.prefix' => 'Cascade_DataFormat_',
	            'dataformat.suffix' => NULL,
	            'gateway.prefix'    => 'Cascade_Gateway_',
	            'gateway.suffix'    => NULL,
	 	),
	 ), 
);
	
//	return $ cascade_config = array (
//		CASCADE_CONFIG_INDEX_SCHEMA => array (
//			'sample: default' => array (
//				'dataformat.prefix' => 'Gree_Service_Sample_Cascade_DataFormat', 
//				'dataformat.suffix' => null, 
//				' gateway.prefix '=>' Gree_Service_Sample_Cascade_Gateway ',
//				' gateway.suffix '=> null
//				' load.path '=>' / home / gree / service / sample ',
//				' load.ignore_prefix '=>' Gree_Service_Sample ',
//				' load. file_ext '=>'. php ',), ... (omitted) ...));
