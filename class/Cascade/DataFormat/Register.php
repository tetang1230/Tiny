<?php
   class Cascade_DataFormat_Register extends Cascade_DB_SQL_DataFormat
   {
   // ----[ Properties ]---------------------------------------------
    protected $table_name     = 'user';
    protected $primary_key    = 'id';
    protected $auto_increment =  FALSE;
    protected $master_dsn     = 'gree://master/sandgame';
    protected $slave_dsn      = 'gree://slave/sandgame';
    protected $extra_dsn      = array(
        'standby' => 'gree://standby/sandgame',
    );

    protected $field_names = array(
        'id',
        'name',
    	'password',
    	'phone',
    	'email',
		'coin',
    );

    protected $queries = array(
        'find_all' => array(
            'sql' => 'SELECT * FROM __TABLE_NAME__ ',
        ),
        'addNewUser'=>array(
              'sql'=>'INSERT INTO __TABLE_NAME__ (name,password,phone,email) VALUES (:name,:password,:phone,:email)',
        ),
        'login' => array(
        	   'sql'=>'SELECT * FROM __TABLE_NAME__ WHERE name = :name AND password = :password',
        ),
        'checkExistUser' => array(
        		'sql'=>'SELECT name FROM __TABLE_NAME__ WHERE name = :name',
        ),
        'minusCoin' => array(
        		'sql' => 'UPDATE __TABLE_NAME__ SET coin = coin - :price WHERE id = :id',
        ),

    );

   }
?>
