<?php
/**
 * 	@author: $rachow
 *	@copyright: XM \2023
 *
 *	Autoload external libraries
*/

$autoload = (is_file(__DIR__ . '/vendor/autoload.php')) ? __DIR__ . '/vendor/autoload.php' : 
	(is_file(__DIR__ . '/../vendor/autoload.php')) ? __DIR__ . '/../vendor/autoload.php' : '';

if ($autoload) {
	require $autoload;
	$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
	$dotenv->load();
}
