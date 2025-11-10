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
        // ✅ Inject global settings before extracting $data
        $settingsModel = new Setting();
        $headlineStatus = $settingsModel->first(['key' => 'headline_status']);

        $settingsStatus = $settingsModel->first(['key' => 'data_option']);
        $data['dataOptionEnabled'] = $settingsStatus['value'] ?? 'on';

        // Default = on
        $data['headlineEnabled'] = $headlineStatus['value'] ?? 'on';

        if (!empty($data)) {
            extract($data);
        }
        $filename = "../app/views/" . $name . ".php";

        if (!file_exists($filename)) {
            $filename = "../app/views/" . $name . "Controller.php";
        }
        if (file_exists($filename)) {
            require '../app/views/layouts/header.php';
            require $filename;
            require '../app/views/layouts/footer.php';
        } else {
            $filename = "../app/views/404.php";
            require $filename;
        }
    }

    public function model($model)
    {
        require_once "../app/models/" . $model . ".php";
        return new $model();
    }
}
