<?php
class Webapp {
    /**
     * Entry point of the webapp, find the correct requesthandler and execute it
     */
    public static function start($paths) {
    	$request_url = str_replace(APP_URN, "", $_SERVER['REQUEST_URI']);
    	if(strpos($request_url, '?') !== false) {
    		$request_url = explode('?', $request_url);
    	    $request_url = $request_url[0];
    	}
    	
    	$foundpage = false;
    	foreach($paths as $path) {
    	    if(preg_match('/^'.str_replace('/', '\/', $path[0]).'$/m', $request_url, $matches)) {
    	        $handlerName = $path[1];
    			require HANDLER_PATH.'/'.$handlerName.'.php';
                $handler = new $handlerName();
    	        $function = array($handler, strtolower($_SERVER['REQUEST_METHOD']));
    	        if(method_exists($function[0], $function[1])) {
    	        	call_user_func_array($function, $matches);
    	        	$foundpage = true;
    	        }
                
    	        break;
    	    }
    	}
    	
    	if(!$foundpage) {
    		header("HTTP/1.0 404 Not Found");
    	}
    }

    
    /**
     * Autoloader for user-defined classes
     */
    public static function autoload($className) {
        $paths = array(
            CLASS_PATH.'/%s.class.php',
            MODEL_PATH.'/%s.model.php'
        );
        foreach($paths as $path) {
            $fileName = sprintf($path, $className);
            if(file_exists($fileName)) {
            	require_once $fileName;
                return true;
            }
        }
        return false;
    }
}
