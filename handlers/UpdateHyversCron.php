<?php
class UpdateHyversCron {
    public static function get($url) {
        set_time_limit(0);
        $_SERVER['REMOTE_ADDR'] = "127.0.0.1";
        
        if(!isset($_GET['token']) or $_GET['token'] != 'a30b51ad366de9cbe53e666a44583809') {
            die;
        }
        
        $rows = DB::set('SELECT * FROM `users` WHERE `lastupdate` < NOW() - INTERVAL 1 DAY ORDER BY `lastupdate` ASC');
    	
        $users = array();
        foreach($rows as $row) {
    		$user = HyvesUser::fromArray($row);
        	$users[$user->getID()] = $user;
    	}

        if(count($users) > 0) {
            if(count($users) === 1) {
                $user->update();
                $user->put();
            } else {
                $userIds = array();
                foreach($users as $user) {
                    $userIds[] = $user->getID();
                }

                $userIds = implode(',', $userIds);
                $result = Tools::getHyves()->doMethod('users.get', array('userid' => $userIds, 'ha_responsefields' => 'viewscount,profilepicture'));
    			if(isset($result->user)) {
                    foreach($result->user as $userResult) {
                        $users[(string)$userResult->userid]->updateFromXMLObject($userResult);
                        $users[(string)$userResult->userid]->put();
                    }
                }
            }
        } else {
            echo "Nothing to update";
        }
    }
}
