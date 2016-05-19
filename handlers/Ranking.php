<?php
class Ranking extends RequestHandler {
    public function get($url, $rankType, $type, $page = 1) {
        $rankTypes = array('top', 'stijgend', 'dalend');
        $types = array('vrienden' => 'friends', 'bekeken' => 'views');
        
        if(!in_array($rankType, $rankTypes) or !isset($types[$type])) {
            die('Invalid type');
        }
        
        if($page <= 0) {
            $page = 1;
        }
        $perPage = 20;
        $friendslink = '/'.$rankType.'/vrienden';
        $viewslink = '/'.$rankType.'/bekeken';
        
        $topUsers = array();
        $h2 = '';
        $total = 0;
        if($rankType == 'top') {
            $topUsers = HyvesUser::getTop($perPage, $types[$type], $page);
            if($type == 'vrienden') {
                $h2 = "Hyvers met de meeste vrienden";
            } else if ($type == 'bekeken') {
                $h2 = "Vaakst bekeken Hyvers";
            }
            $total = HyvesUser::getTotalRegistered();
        } else if($rankType == 'stijgend') {
            $topUsers = HyvesUser::getRising($perPage, $types[$type], $page);
            $total = HyvesUser::getTotalRising($types[$type]);
            
            if($type == 'vrienden') {
                $h2 = "Meest bevriende Hyvers in de laatste 24 uur";
            } else if ($type == 'bekeken') {
                $h2 = "Vaakst bekeken Hyvers in de laatste 24 uur";
            }
        } else if($rankType == 'dalend') {
            $topUsers = HyvesUser::getFalling($perPage, $page);
            $total = HyvesUser::getTotalFalling();
            
            $h2 = "Meest ontvriende Hyvers";
            $friendslink = '';
            $viewslink = '';
        }
        
        if($page > 1) {
            $prevlink = '/'.$rankType.'/'.$type.'/'.($page-1);
        } else {
            $prevlink = '';
        }
        if($total > $page*$perPage) {
            $nextlink = '/'.$rankType.'/'.$type.'/'.($page+1);
        } else {
            $nextlink = '';
        }
        
        $template = self::$Twig->loadTemplate('ranking.html');
        $template->display(array(
         'topUsers' => $topUsers,
         'page' => $page,
         'perPage' => $perPage,
         'prevlink' => $prevlink,
         'nextlink' => $nextlink, 
         'friendslink' => $friendslink, 
         'viewslink' => $viewslink,
         'h2' => $h2,
         'total' => $total));
    }
}
