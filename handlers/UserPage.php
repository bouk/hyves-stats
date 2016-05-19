<?php
class UserPage extends RequestHandler {
    public static function get($url, $userName) {
        if ($hyvesUser = HyvesUser::fromUsername($userName)) {
            $template = self::$Twig->loadTemplate('user.html');
            $stats = $hyvesUser->getStats();
            
            $amount = min(count($stats), 7);
            $weekDays = array(1 => "Maandag", 2 => "Dinsdag", 3 => "Woensdag", 4 => "Donderdag", 5 => "Vrijdag", 6 => "Zaterdag", 7 => "Zondag");
            for($i = 0; $i < $amount; $i++) {
                $stat = $stats[$i];
                $graphStats[] = array($amount-$i, (int)$stat['friends']);
                $graphTicks[] = array($amount-$i, $weekDays[date('N', strtotime($stat['date_inserted']))]);
            }
            $template->display(array(
                'hyvesUser' => $hyvesUser, 
                'stats' => $stats,
                'graphStats' => json_encode($graphStats),
                'graphTicks' => json_encode($graphTicks)
            ));
        } else {
            $template = self::$Twig->loadTemplate('user_not_found.html');
            $template->display(array('username' => $userName));
        }
    }
}
