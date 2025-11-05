<?php

class App
{
    private $controller = "Pdf";
    private $method = "index";
    private $params = [];

    private function splitUrl()
    {
        $url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $url = trim($url, '/');

        if ($url === '') return [];

        return explode('/', $url);
    }

    public function loadController()
    {
        $url = $this->splitUrl();

        $controllerName = !empty($url[0]) ? ucfirst($url[0]) : $this->controller;

        if (strtolower($controllerName) === 'pdf') {
            $controllerName = 'Pdf';
        }
        $filename = "../app/controllers/" . $controllerName . ".php";
        if (!file_exists($filename)) {
            $filename = "../app/controllers/" . $controllerName . "Controller.php";
            $controllerName .= "Controller";
        }

        if (file_exists($filename)) {
            require $filename;
            $this->controller = $controllerName;
        } else {
            $filename = "../app/controllers/NotFound.php";
            require $filename;
            $this->controller = "NotFound";
        }

        $controller = new $this->controller;

        $this->method = !empty($url[1]) ? $url[1] : $this->method;
        if (!method_exists($controller, $this->method)) {
            $this->method = "index";
        }

        $this->params = $url ? array_slice($url, 2) : [];

        call_user_func_array([$controller, $this->method], $this->params);
    }
}
