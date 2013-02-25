<?php
/**
 * 角色等级表
 * @author HuangU
 *
 */
class Cascade_DataFormat_Prototype_PlayerLevel extends Cascade_DB_Config_DataFormat
{
    // ----[ Properties ]---------------------------------------------
    // @var string config dir path
    protected $config_path  = PROTOTYPE_CONFIG_DIR_PATH;

    // @var string config file name
    protected $config_file  = 'playerlevel.ini.php';

    // @var int driver
    protected $driver_type  = self::DRIVER_PHPARRAY;

    // @var int assoc    
    //protected $fetch_mode   = self::FETCH_MODE_ASSOC; 
};
?>