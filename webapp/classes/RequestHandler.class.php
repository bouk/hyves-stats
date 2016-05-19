<?php
abstract class RequestHandler {
    public static $Twig;
    public function redirect($url) {
        header('Location: '.$url);
        die;
    }
}