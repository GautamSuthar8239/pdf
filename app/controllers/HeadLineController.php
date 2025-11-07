<?php

class HeadLineController
{
    use Controller;

    public function index()
    {
        $data = [
            'title' => 'Headline',
            'breadcrumb' => ['Home / Headline'],
        ];

        $headlineModel = new HeadLine();

        $headline = $headlineModel->findAll();

        $data['headline'] = $headline ?? [];

        $this->view("headline/index", $data);
    }
}
