<?php
class Tools {
    private static $_hyves;
    const responsefields = 'viewscount,profilepicture';
    const returnfields = '/user/userid,/user/firstname,/user/lastname,/user/url,/user/profilepicture/icon_small/src,/user/viewscount,/user/friendscount';
    
    public static function getHyves() {
        if(!isset(self::$_hyves)) {
            self::$_hyves = new GenusApis(new OAuthConsumer('OTQ1OF8qgcxUyUbOoD3JsUV7aXRp', 'OTQ1OF8UBrBdzbaSIJiBJyiB0POw'), '2.0');
        }
        return self::$_hyves;
    }
    
    public static function getHyvesUsersByUserID($userIds) {
        return self::getHyves()->doMethod('users.get', array(
        'userid' => $userIds, 
        'ha_responsefields' => self::responsefields,
        'ha_returnfields' => self::returnfields
        ));
    }

    public static function getHyvesUsersByUsername($usernames) {
        return self::getHyves()->doMethod('users.getByUsername', array(
        'username' => $usernames, 
        'ha_responsefields' => self::responsefields,
        'ha_returnfields' => self::returnfields
        ));
    }
    
    public static function validHyvesUsername($username) {
        return preg_match('/^[\da-zA-Z][\da-zA-Z-]{1,20}[\da-zA-Z]$/', $username);
    }
    
    public static function validHyvesID($username) {
        return preg_match('/^[\da-zA-Z]{34}$/', $username);
    }
}
