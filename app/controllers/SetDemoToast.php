<?php
class SetDemoToast
{
    use Controller;

    public function index()
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        $pcId = md5($ip . $userAgent); // a simple unique identifier
        echo $pcId;

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
}
