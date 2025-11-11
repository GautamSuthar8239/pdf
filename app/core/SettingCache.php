<?php

class SettingCache
{
    public static function get($key, $default = null)
    {
        // If already cached in session → return
        if (isset($_SESSION['setting_cache'][$key])) {
            return $_SESSION['setting_cache'][$key];
        }

        // Load from DB
        $model = new Setting();
        $row = $model->first(['key' => $key]);

        // Cache it
        $_SESSION['setting_cache'][$key] = $row['value'] ?? $default;

        return $_SESSION['setting_cache'][$key];
    }

    public static function value($key, $default = null)
    {
        return self::get($key, 'value', $default);
    }

    public static function status($key, $default = null)
    {
        return self::getOr($key, 'status', $default);
    }


    public static function getOr($key, $column = 'value', $default = null)
    {
        // Unique cache key (key + column)
        $cacheKey = $key . '_' . $column;

        // Check session cache
        if (isset($_SESSION['setting_cache'][$cacheKey])) {
            return $_SESSION['setting_cache'][$cacheKey];
        }

        // Load from DB
        $model = new Setting();
        $row = $model->first(['key' => $key]);

        // Prevent undefined index
        $value = $row[$column] ?? $default;

        // Cache it
        $_SESSION['setting_cache'][$cacheKey] = $value;

        return $value;
    }


    public static function clear($key = null)
    {
        if ($key === null) {
            unset($_SESSION['setting_cache']);
        } else {
            unset($_SESSION['setting_cache'][$key]);
        }
    }

    public static function clearArray($key, $column = null)
    {
        if ($column === null) {
            unset($_SESSION[$key]);
        } else {
            unset($_SESSION[$key][$column]);
        }
    }
}
