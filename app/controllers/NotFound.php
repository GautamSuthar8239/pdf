<?php
class NotFound
{
    use Controller;
    public function index()
    {
        $data['showLayout'] = false;
        $data['showSidebar'] = false;
        $data['showFooter'] = false;

        $this->view('404', $data);
    }
}
