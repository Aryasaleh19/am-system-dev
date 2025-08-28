<?php

if (!function_exists('set_active')) {
    function set_active($uris, $class = 'active open')
    {
        $current = uri_string();

        if (is_array($uris)) {
            return in_array($current, $uris) ? $class : '';
        }

        return $current === $uris ? $class : '';
    }
}