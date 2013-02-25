<?php
   class Cascade_DataFormat_BuyStaff extends Cascade_DB_SQL_DataFormat
   {
   // ----[ Properties ]---------------------------------------------
    protected $table_name     = 'user_bag';
    protected $primary_key    = 'id';
    protected $auto_increment =  FALSE;
    protected $master_dsn     = 'gree://master/sandgame';
    protected $slave_dsn      = 'gree://slave/sandgame';
    protected $extra_dsn      = array(
        'standby' => 'gree://standby/sandgame',
    );

    protected $field_names = array(
    	'id',
        'user_name',
        'goods_id',
    	'goods_count',
    );

    protected $queries = array(
        'find_all' => array(
            'sql' => 'SELECT * FROM __TABLE_NAME__ ',
        ),
        
        /*'addNewUser'=>array(
              'sql'=>'INSERT INTO __TABLE_NAME__ (name,password,phone,email) VALUES (:name,:password,:phone,:email)',
        ),
        'login' => array(
        	   'sql'=>'SELECT * FROM __TABLE_NAME__ WHERE name = :name AND password = :password',
        ),
        'checkExistUser' => array(
        		'sql'=>'SELECT name FROM __TABLE_NAME__ WHERE name = :name',
        ),*/
        
        'addNewItem' => array(
        		'sql' => 'INSERT INTO __TABLE_NAME__ (user_name,goods_id,goods_count) VALUES(:user_name, :goods_id, 1)',
        ),
        
        'updateItem' => array(
        		//'sql' => 'UPDATE __TABLE_NAME__ SET sword = sword + 1 WHERE user_name = :user_name',
        		'sql' => 'UPDATE __TABLE_NAME__ SET :item = :item + 1 WHERE user_name = :user_name',
        ),
        
        'retrieveBag' => array(
        		'sql' => 'SELECT * FROM __TABLE_NAME__ WHERE user_name = :user_name',
        ),
    );

   }
?>
