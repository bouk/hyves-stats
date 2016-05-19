<?php
define('WEBAPP_ROOT', dirname(__FILE__));

require WEBAPP_ROOT.'/classes/DB.class.php';
require WEBAPP_ROOT.'/classes/Model.class.php';
require WEBAPP_ROOT.'/classes/RequestHandler.class.php';
require WEBAPP_ROOT.'/classes/Webapp.class.php';

spl_autoload_register(array('Webapp', 'autoload'));

require WEBAPP_ROOT.'/Twig/Autoloader.php';
Twig_Autoloader::register();
require WEBAPP_ROOT.'/Twig/Extensions/Autoloader.php';
Twig_Extensions_Autoloader::register();

RequestHandler::$Twig = new Twig_Environment(
    new Twig_Loader_Filesystem(VIEW_PATH),
    array('cache' => TEMPLATECACHE_PATH)
);
