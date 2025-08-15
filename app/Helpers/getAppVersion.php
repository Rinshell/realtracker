<?php

// хелпер function_exists() гарантирует, что функция будет доступна только в том случае, если она не существует в экземпляре приложения.
if (!function_exists('getAppVersion')) {
    function getAppVersion() {
        $command = 'git describe --always --tags --dirty';
        $output = shell_exec($command);
        return $output;
    }
}
