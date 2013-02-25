<?php
   class Cascade_DataFormat_Goods extends Cascade_DB_SQL_DataFormat
   {
   // ----[ Properties ]---------------------------------------------
    protected $table_name     = 'goods';
    protected $primary_key    = 'goods_id';
    protected $auto_increment =  FALSE;
    protected $master_dsn     = 'gree://master/sandgame';
    protected $slave_dsn      = 'gree://slave/sandgame';
    protected $extra_dsn      = array(
        'standby' => 'gree://standby/sandgame',
    );

    protected $field_names = array(
    	'goods_id',
    	'goods_name',
    	'goods_price'
    );

    protected $queries = array(
        'find_all' => array(
            'sql' => 'SELECT * FROM __TABLE_NAME__ ',
        ),
    );

   }
?>
