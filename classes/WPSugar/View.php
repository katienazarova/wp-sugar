<?php

namespace WPSugar;

class View {
	public static function render($template, $vars = array(), $echo = false) {
		$path = __DIR__ . '/../../templates';
		
		foreach ($vars as $key => $value) {
			$$key = $value;
		}
		
		ob_start();

        if (file_exists(get_template_directory() . '/templates/' . $template . '.php')) {
            require get_template_directory() . '/templates/' . $template . '.php';
        } elseif (file_exists($path . '/' . $template . '.php')) {
            require $path . '/' . $template . '.php';
        }

		$content = ob_get_clean();

		if ($echo) {
            echo $content;
        }
        
        return $content;
	}
}