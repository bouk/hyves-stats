<?php
class Search extends RequestHandler {
    public function get($url) {
        $users = array();
        $username = '';
        if(isset($_GET['q']) and ($searchQuery = trim($_GET['q']))) {
            
            $userIds = array();
            $hyves = Tools::getHyves();
            $result = $hyves->doMethod('users.search', array('searchterms'=> $searchQuery, 'ha_returnfields' => '/user/userid,/user/url'));
            if(isset($result->user)) {
                foreach($result->user as $user) {
                    $userIds[] = '\''.DB::escape((string)$user->userid).'\'';
                }
                if(count($userIds) == 1) {
                    $username = HyvesUser::profileURLToUsername($result->user->url);
                }
            }
            
            $format = "SELECT * FROM users WHERE id IN (%s)";
            $query = sprintf($format, implode(', ', $userIds));
            if($result = DB::set($query)) {
                foreach($result as $row) {
                    $users[] = HyvesUser::fromArray($row);
                }
            }
            
            
        }
        $template = self::$Twig->loadTemplate('search.html');
        $template->display(array('query' => $searchQuery, 'users' => $users, 'username' => $username));
    }
}
