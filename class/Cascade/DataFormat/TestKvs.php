<?php

class Cascade_DataFormat_TestKvs extends Cascade_DB_KVS_DataFormat {

    protected $dsn = 'gree(memcache)://node/test';
	
	protected $driver_type = self::DRIVER_MEMCACHED;
	
	protected $namespace = 'test';
	
	protected $compressed = FALSE;
}


