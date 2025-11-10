<?php
class SetDemoToast
{
    use Controller;

    public function index()
    {
        $referrer = parse_url($_SERVER['HTTP_REFERER'] ?? '/', PHP_URL_PATH);
        Flash::set('toast', 'This is Index option. <br />This is ' . $referrer . ' page.', 'info');
        $referrerPath = ltrim($referrer, '/');
        redirect($referrerPath);

        // $data = [
        //     'title' => 'Motivation Lines Manager',
        //     'breadcrumb' =>  ['Home / Motivation Lines Manager'],
        // ];

        // $this->view('setDemoToast/index', $data);
    }
    public function notify()
    {
        $referrer = parse_url($_SERVER['HTTP_REFERER'] ?? '/', PHP_URL_PATH);
        Flash::set('toast', 'This is Notification option. <br />This is ' . $referrer . ' page.', 'info');
        $referrerPath = ltrim($referrer, '/');
        redirect($referrerPath);
    }

    public function setting()
    {
        $referrer = parse_url($_SERVER['HTTP_REFERER'] ?? '/', PHP_URL_PATH);
        Flash::set('toast', 'This is Setting option. <br /> This is ' . $referrer . ' page.', 'info');
        $referrerPath = ltrim($referrer, '/');
        redirect($referrerPath);
    }
    public function hello()
    {
        $referrer = parse_url($_SERVER['HTTP_REFERER'] ?? '/', PHP_URL_PATH);
        Flash::set('toast', 'This is News option. <br /> This is ' . $referrer . ' page.', 'info');
        $referrerPath = ltrim($referrer, '/');
        redirect($referrerPath);
    }

    public function help()
    {
        $referrer = parse_url($_SERVER['HTTP_REFERER'] ?? '/', PHP_URL_PATH);
        Flash::set('toast', 'This is Help option. <br /> You can Contact us on: <a href="https://probidconsultant.com/" target="_blank"> https://probidconsultant.com/ </a> <br /> This is ' . $referrer . ' page.', 'info');
        $referrerPath = ltrim($referrer, '/');
        redirect($referrerPath);
    }

    public function about()
    {
        $referrer = parse_url($_SERVER['HTTP_REFERER'] ?? '/', PHP_URL_PATH);
        Flash::set('toast', 'This is About option. <br/> We offer end-to-end tender consultancy solutions tailored to your specific industry and requirements.<br /> You can Contact us on: <a href="https://probidconsultant.com/" target="_blank"> https://probidconsultant.com/ </a> <br /> This is ' . $referrer . ' page.', 'info');
        $referrerPath = ltrim($referrer, '/');
        redirect($referrerPath);
    }

    public function logout()
    {
        // session destry
        $referrer = parse_url($_SERVER['HTTP_REFERER'] ?? '/', PHP_URL_PATH);
        // Flash::set('toast', 'This is Logout option. <br/> You have been logged out. <br /> This is ' . $referrer . ' page.', 'info');
        $referrerPath = ltrim($referrer, '/');
        if (isset($_SESSION) && !empty($_SESSION))
            session_destroy();
        redirect($referrerPath);
    }
}
