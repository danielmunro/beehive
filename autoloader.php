<?php

spl_autoload_register(function($class) {
	$pos = strpos($class, 'Beehive');
	if($pos !== false) {
		$class = substr($class, $pos+8);
		$class = str_replace('\\', DIRECTORY_SEPARATOR, $class);
		require_once(__DIR__.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.$class.'.php');
	}
});
