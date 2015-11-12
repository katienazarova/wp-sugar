<?php

/**
 * Plugin Name: WP Sugar
 * Version: 1.0.0
 * Plugin URI: https://github.com/katienazarova/wp-sugar
 * Description: WordPress Framework
 * Author: Ekaterina Nazarova
 * Author URI: https://github.com/katienazarova
 * Text Domain: wp-sugar
 * License: GPL v3
 */

/**
 * WP Sugar Plugin
 * Copyright (C) 2015, Ekaterina Nazarova - support@getinstance.info
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

if (!function_exists('add_filter')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

if (!defined('WPSUGAR_FILE')) {
    define('WPSUGAR_FILE', __FILE__);
}

if (!defined('WPSUGAR_PATH')) {
    define('WPSUGAR_PATH', plugin_dir_path(WPSUGAR_FILE));
}

require_once WPSUGAR_PATH . 'classes/WPSugar/CustomTypesManager.php';
require_once WPSUGAR_PATH . 'classes/WPSugar/OptionsManager.php';
require_once WPSUGAR_PATH . 'classes/WPSugar/FormsManager.php';
require_once WPSUGAR_PATH . 'classes/WPSugar/View.php';
require_once WPSUGAR_PATH . 'classes/WPSugar/Utils.php';
require_once WPSUGAR_PATH . 'classes/WPSugar/Localization.php';
require_once WPSUGAR_PATH . 'classes/WPSugar/Config.php';

require_once WPSUGAR_PATH . 'shortcodes/posts-list.php';
require_once WPSUGAR_PATH . 'shortcodes/text-block.php';
require_once WPSUGAR_PATH . 'shortcodes/form.php';

function wpsugar_load_config() {
    wpsugar_execute_config(__DIR__ . '/default-config.json');
    wpsugar_execute_config(get_template_directory() . '/wpsugar-config.json');
}

function wpsugar_execute_config($configFile) {
    $config = WPSugar\Config::load($configFile);
    if (!$config || !is_array($config)) {
        return;
    }

    if (isset($config['custom_types']) && is_array($config['custom_types'])) {
        foreach ($config['custom_types'] as $customTypeFields) {
            $manager = new WPSugar\CustomTypesManager($customTypeFields);
            try {
                $manager->register();
            } catch (Exception $e) {}
        }
    }
    
    if (isset($config['options']) && is_array($config['options'])) {
        foreach ($config['options'] as $optgroup) {
            $manager = new WPSugar\OptionsManager($optgroup);
            try {
                $manager->register();
            } catch (Exception $e) {}
        }
    }

    if (isset($config['forms']) && is_array($config['forms'])) {
        foreach ($config['forms'] as $form) {
            $manager = new WPSugar\FormsManager($form);
            try {
                $manager->register();
            } catch (Exception $e) {}
        }
    }
}

function wpsugar_setup() {
    wpsugar_execute_config(__DIR__ . '/default-config.json');
    wpsugar_execute_config(get_template_directory() . '/wpsugar-config.json');
}
add_action('after_setup_theme', 'wpsugar_setup');