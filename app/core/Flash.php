<?php
class Flash
{
    private static $iconMap = [
        'success' => 'check_circle',
        'danger'  => 'error',
        'warning' => 'warning',
        'info'    => 'info'
    ];

    public static function set($key, $message, $type = 'info', $duration = 3000)
    {
        $_SESSION['flash'][$key] = [
            'message' => $message,
            'type'    => $type,
            'icon'    => self::$iconMap[$type] ?? 'info',
            'duration' => $duration
        ];
    }

    public static function get($key)
    {
        if (isset($_SESSION['flash'][$key])) {
            $data = $_SESSION['flash'][$key];
            unset($_SESSION['flash'][$key]); // remove after read
            return $data;
        }
        return null;
    }

    public static function has($key)
    {
        return isset($_SESSION['flash'][$key]);
    }
}
