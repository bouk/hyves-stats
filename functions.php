<?php
function str_replace_once($str_pattern, $str_replacement, $string){    
    if (strpos($string, $str_pattern) !== false){ 
        $occurrence = strpos($string, $str_pattern); 
        return substr_replace($string, $str_replacement, strpos($string, $str_pattern), strlen($str_pattern)); 
    } 
    
    return $string; 
}