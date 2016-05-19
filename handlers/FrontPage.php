<?php
class FrontPage extends RequestHandler {
    public function get($url) {
        $perPage = 20;
        $page = 1;
        $total = HyvesUser::getTotalRegistered();
        if($total > $page*$perPage) {
            $nextlink = '/top/vrienden/'.($page+1);
        }
        
        $template = self::$Twig->loadTemplate('frontpage.html');
        $template->display(array(
         'topUsers' => HyvesUser::getTop($perPage, 'friends', $page),
         'page' => $page,
         'perPage' => $perPage,
         'nextlink' => $nextlink,
         'hideTopNav' => true,
         'total' => $total));
    }
}
