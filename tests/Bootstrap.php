<?php
// Validate php version
function default_autoload($class)
{
    if (strpos(ltrim($class, '\\'), 'OAuth\\Server\\') === 0) {
        $class = str_replace(array('OAuth\\', '_', '\\', '/'), DIRECTORY_SEPARATOR, $class);

        $path = realpath(__DIR__ . '/../src/OAuth/' . $class . '.php');
        if ($path !== false) {
            include $path;
            return;
        }
    }
    if (strpos(ltrim($class, '\\'), 'Tests\\') === 0) {
        $class = str_replace(array('Tests\\', '_', '\\', '/'), DIRECTORY_SEPARATOR, $class);

        $path = realpath(__DIR__ . '/Mock/' . $class . '.php');
        if ($path !== false) {
            include $path;
            return;
        }
    }

    @include str_replace(array('\\', '_', '/'), DIRECTORY_SEPARATOR, $class) . '.php';
}
spl_autoload_register("default_autoload");