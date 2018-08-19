<?php
if (!function_exists('config_path')) {
    /**
     * Get the configuration path.
     *
     * @param  string $path
     * @return string
     */
    function config_path($path = '')
    {
        return app()->basePath() . '/config' . ($path ? '/' . $path : $path);
    }
}

if (!function_exists('mountOrWhereRecursive')) {
    function mountOrWhereRecursive($query, $field, $filter, $isWhere = false)
    {
        if (!empty($filter)) {
            if ($isWhere) {
                $query->where($field, 'like', $filter);
            } else {
                $query->orWhere($field, 'like', $filter);
            }
        }
        return $query;
    }
}

if (!function_exists('app_path')) {
    /**
     * Get the configuration path.
     *
     * @param  string $path
     * @return string
     */
    function app_path($path = '')
    {
        return app()->basePath() . '/app' . ($path ? '/' . $path : $path);
    }
}