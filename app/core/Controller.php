<?php

trait Controller
{
    public string $className;
    public string $title;

    public function __construct()
    {
        $fullClass = get_class($this);
        if (str_contains($fullClass, 'Controller')) {
            $fullClass = str_replace('Controller', '', $fullClass);
        }
        $this->className = $fullClass;

        $this->title = ucfirst($fullClass);
    }

    public function view($name, $data = [])
    {
        if (!isset($data['title'])) {
            $data['title'] = $this->title;
        }

        // ✅ Global settings via SettingCache
        $data['data_option_status']   = SettingCache::status('data_option');
        if ($data['data_option_status'] == 'active')
            $data['dataOptionEnabled'] = SettingCache::value('data_option');
        $data['headline_status']   = SettingCache::status('headline_status');
        if ($data['headline_status'] == 'active')
            $data['headlineEnabled']   = SettingCache::value('headline_status');
        $data['version_status']    = SettingCache::status('version');
        if ($data['version_status'] == 'active')
            $data['version']           = SettingCache::value('version');

        // ✅ Global headlines
        $data['global_headlines'] = GlobalHeadlines::load();

        if (!empty($data)) extract($data);

        // ✅ Load view
        $filename = "./app/views/" . $name . ".php";

        if (!file_exists($filename)) {
            $filename = "./app/views/" . $name . "Controller.php";
        }

        if (file_exists($filename)) {
            require './app/views/layouts/header.php';
            require $filename;
            require './app/views/layouts/footer.php';
        } else {
            require "./app/views/404.php";
        }
    }

    public function model($model)
    {
        require_once "./app/models/" . $model . ".php";
        return new $model();
    }
}
