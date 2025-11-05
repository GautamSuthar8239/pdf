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
