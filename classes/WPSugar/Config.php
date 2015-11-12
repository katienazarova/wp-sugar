<?php
namespace WPSugar;

class Config {
    private static $_config = false;

	public static function load($configFile) {
		if (!file_exists($configFile) || !is_readable($configFile)) {
            return;
        }

        $config = file_get_contents($configFile);
        try {
            static::$_config = json_decode($config, true);
        } catch(Exception $e) {}
        return static::$_config;
	}
    
    public static function get() {
        return static::$_config;
    }
}