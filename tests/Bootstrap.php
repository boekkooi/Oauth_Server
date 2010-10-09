<?php
// phpunit --bootstrap tests/Bootstrap.php --coverage-html ./report --verbose tests/
function oauth_autoload($class)
{
    if (strpos(ltrim($class, '\\'), 'OAuth\\Server\\') !== 0) {
        return false;
    }
    $class = str_replace(array('OAuth\\', '_', '\\'), '/', $class);

	$path = realpath(__DIR__ . '/../OAuth/' . $class . '.php');
	if (!$path) {
        return false;
	}
    include $path;
}
spl_autoload_register("oauth_autoload");

function zend_autoload($class)
{
    if (strpos($class, 'Zend_') !== 0) {
        return false;
    }
    $class = str_replace(array('_', '\\'), '/', $class);

	$path = realpath(__DIR__ . '/../' . $class . '.php');
	if (!$path) {
        return false;
	}
    include $path;
}
spl_autoload_register("zend_autoload");
