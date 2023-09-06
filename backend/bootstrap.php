<?php
/*
 *	@author: $rachow
 *	@copyright: XM \2023
 *
 *
 *	Backend scripts bootstrap file
 *  Loads and overrides any configs, helpers, etc
 *
*/

require __DIR__ . '/autoload.php';

$configs = [];
$helpers = [];

$exclude_configs = [
	// add configs to exclude
];

$exclude_helpers = [
	// add helpers to exclude
];

$configs_dir = __DIR__ . '/config';
$helpers_dir = __DIR__ . '/helpers';

if (file_exists(__DIR__ . '/.configs.cache')) {
    
    // load configs from cache file
	$configs = json_decode(file_get_contents(__DIR__ . '/.configs.cache'), true);

} elseif (is_dir($configs_dir)) {
	$dh = opendir($configs_dir);
	if (is_resource($dh)) {
		while (false !== ($file = readdir($dh))) {
			if ($file == '.' || $file == '..' || in_array($file, $exclude_configs)) {
				continue;
			}
			$file_path = $configs_dir . '/' . $file;
			$cfg_index = substr($file, 0, strrpos($file, "."));
			$cfg_array = include_once($file_path);
			$configs[$cfg_index] = $cfg_array;
		}

        closedir($dh);
		
		// cache the configs.
		if (!empty($configs)) {
			file_put_contents(__DIR__ . '/.configs.cache', json_encode($configs));
		}
	}
}

if (is_dir($helpers_dir)) {
	$pattern = '/*.php';
	$helpers = glob($helpers_dir . $pattern, GLOB_BRACE);

    if (!empty($helpers)) {
		foreach ($helpers as $helper_file) {
			$file_name = basename($helper_file);
			if (!in_array($file_name, $exclude_helpers)) {
				include_once $helper_file;
			}
		}
	}
}

/* jolly good, carry on. */
