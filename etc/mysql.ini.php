<?php
/**
 * mysql.ini.php
 */
return $mysql_config_list = array(
//	'example' => array(
//        'master'    => 'localhost',
//        'slave'     => 'localhost',
//        'standby'   => 'localhost',
//        'user'      => 'root',
//        'pass'      => '',
//        // 'ro_user'   => 'gree-ro',
//        //'ro_pass'   => 'gree',
//        'db'        => 'test',
//    ),
    'sandgame' => array(
        /*
    	'master'    => '10.32.8.18',
		'slave'     => '10.32.8.18',
		'standby'   => '10.32.8.18',
        'user'      => 'gmdev',
        'pass'      => 'gmdev',
        */
		'master'    => 'localhost',
		'slave'     => 'localhost',
		'standby'   => 'localhost',
        'user'      => 'root',
        'pass'      => 'jichao',
        // 'ro_user'   => 'gree-ro',
        //'ro_pass'   => 'gree',
        'db'        => 'sandgame',
    ),
);
// }}}
