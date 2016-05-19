<?php
class NewUser extends RequestHandler {
    public function post($url) {
        if(isset($_POST['username'])) {
            $username = $_POST['username'];
            if(!$user = HyvesUser::fromUsername($username)) {
                if($user = HyvesUser::fetchNewUserByUsername($username)) {
                    $this->redirect($user->getStatsURL());
                } else {
                    $template = self::$Twig->loadTemplate('user_not_exist.html');
                    $template->display(array('username' => $username));
                }
            } else {
                $this->redirect($user->getStatsURL());
            }
        }
    }
}
