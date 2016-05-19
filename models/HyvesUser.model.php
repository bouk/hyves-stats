<?php
class HyvesUser {
    private $_ID;
    private $_name;
    private $_username;
    private $_pictureLink;
    private $_created;
    private $_firstLog;
    private $_lastUpdate;

    private $_views;
    private $_friends;

    private $_viewsChange;
    private $_friendsChange;

    public function getID() {
        return $this->_ID;
    }

    public function getName() {
        return $this->_name;
    }

    public function getUsername() {
        return $this->_username;
    }

    public function getPictureLink($size) {
        /*
        class HyvesPictureSize {
        const IconSmall = 1;
        const IconMedium = 2;
        const IconLarge = 3;
        const IconExtraLarge = 4;
        const Image = 5;
        const ImageFullscreen = 6;
        const SquareLarge = 14;
        const SquareExtraLarge = 16;
        }*/

        return sprintf($this->_pictureLink, $size);
    }

    public function getStatsURL() {
        return APP_URI.'/hyver/'.$this->_username;
    }

    public function getHyvesURL() {
        return 'http://'.$this->_username.'.hyves.nl';
    }

    public function getCreated() {
        return $this->_created;
    }

    public function getFirstLog() {
        return $this->_firstLog;
    }

    public function getLastUpdate() {
        return $this->_lastUpdate;
    }

    public function getViews() {
        return $this->_views;
    }

    public function getFriends() {
        return $this->_friends;
    }

    public function getFriendsChange() {
        return $this->_friendsChange;
    }

    public function getViewsChange() {
        return $this->_viewsChange;
    }

    public function getStats($number = 10) {
        $format = "SELECT views, friends, date_inserted, views_change, friends_change FROM `stats` WHERE `user_id` = '%s' ORDER BY `date_inserted` DESC LIMIT %d";
        $query = sprintf($format, DB::escape($this->_ID), $number);
        return DB::set($query);
    }

    /**
     * Gets the rising hyvers
     *
     * $orderby string friends or views
     */
    public static function getRising($number, $orderby, $page) {
        $format = "SELECT
                    users.id,
                    users.username,
                    users.name,
                    users.picturelink,
                    users.created,
                    users.firstlog,
                    users.lastupdate,
                    stats.friends,
                    stats.views,
                    stats.friends_change,
                    stats.views_change
                    FROM  stats
                    INNER JOIN users ON (stats.user_id = users.id)
                    WHERE stats.date_inserted > NOW( ) - INTERVAL 1 DAY AND stats.%s_change > 0
                    ORDER BY stats.%s_change DESC, %s DESC
                    LIMIT %d,%d";
        $query = sprintf($format, $orderby, $orderby, $orderby, $number*($page-1), $number);
        $result = DB::set($query);


        $users = array();
        foreach($result as $userArray) {
            $users[] = self::fromArray($userArray);
        }
        return $users;
    }

    /**
     * Returns the total number of Users of who lost friends in the last 36 hours
     */
    public static function getTotalRising($type) {
        $format = "SELECT count(*) FROM  stats
                    INNER JOIN users ON (stats.user_id = users.id)
                    WHERE stats.date_inserted > NOW( ) - INTERVAL 1 DAY AND stats.%s_change > 0";
        return DB::field(sprintf($format, $type));
    }


    /**
     * Gets the Hyves Users who are losing the most friends
     */
    public static function getFalling($number, $page) {
        $format = "SELECT
                    users.id,
                    users.username,
                    users.name,
                    users.picturelink,
                    users.created,
                    users.firstlog,
                    users.lastupdate,
                    stats.friends,
                    stats.views,
                    stats.friends_change,
                    stats.views_change
                    FROM  stats
                    INNER JOIN users ON (stats.user_id = users.id)
                    WHERE stats.date_inserted > NOW( ) - INTERVAL 1 DAY AND stats.friends_change < 0
                    ORDER BY stats.friends_change ASC
                    LIMIT %d,%d";
        $query = sprintf($format, $number*($page-1), $number);
        $result = DB::set($query);

        $users = array();
        foreach($result as $userArray) {
            $users[] = self::fromArray($userArray);
        }
        return $users;
    }

    /**
     * Returns the total number of Users of who lost friends in the last 36 hours
     */
    public static function getTotalFalling() {
        $query = "SELECT count(*) FROM  stats
                    INNER JOIN users ON (stats.user_id = users.id)
                    WHERE stats.date_inserted > NOW( ) - INTERVAL 1 DAY AND stats.friends_change < 0";
        return DB::field($query);
    }

    public static function getTop($number, $orderby, $page) {
        $format = "SELECT * FROM users ORDER BY `%s` DESC LIMIT %d,%d";
        $query = sprintf($format, $orderby, $number*($page-1), $number);

        $result = DB::set($query);

        $users = array();
        foreach($result as $userArray) {
            $users[] = self::fromArray($userArray);
        }
        return $users;
    }

    public static function getTotalRegistered() {
        return DB::field("SELECT count(*) FROM users");
    }

    public static function fromUsername($username) {
        if(!Tools::validHyvesUsername($username)) {
            return false;
        }

        $format = "SELECT
                    users.id,
                    users.username,
                    users.name,
                    users.picturelink,
                    users.created,
                    users.firstlog,
                    users.lastupdate,
                    stats.friends,
                    stats.views,
                    stats.friends_change,
                    stats.views_change
                    FROM stats
                    INNER JOIN users ON (stats.user_id = users.id)
                    WHERE users.username = '%s'
                    ORDER BY stats.date_inserted DESC
                    LIMIT 1";
        $query = sprintf($format, DB::escape($username));
        if($row = DB::row($query)) {
            return self::fromArray($row);
        } else {
            return false;
        }
    }

    public static function fromID($id) {
        if(!Tools::validHyvesID($id)) {
            return false;
        }

        $format = "SELECT
                    users.id,
                    users.username,
                    users.name,
                    users.picturelink,
                    users.created,
                    users.firstlog,
                    users.lastupdate,
                    stats.friends,
                    stats.views,
                    stats.friends_change,
                    stats.views_change
                    FROM stats
                    INNER JOIN users ON (stats.user_id = users.id)
                    WHERE users.id = '%s'
                    ORDER BY stats.date_inserted DESC
                    LIMIT 1";
        $query = sprintf($format, DB::escape($id));
        if($row = DB::row($query)) {
            return self::fromArray($row);
        } else {
            return false;
        }
    }

    public static function fromArray($array) {
        $hyvesUser = new self();
        $hyvesUser->_ID = $array['id'];
        $hyvesUser->_name = $array['name'];
        $hyvesUser->_username = $array['username'];
        $hyvesUser->_pictureLink = $array['picturelink'];
        $hyvesUser->_created = strtotime($array['created']);
        $hyvesUser->_firstLog = strtotime($array['firstlog']);
        $hyvesUser->_lastUpdate = strtotime($array['lastupdate']);
        $hyvesUser->_views = $array['views'];
        $hyvesUser->_friends = $array['friends'];
        if(isset($array['friends_change'])) {
            $hyvesUser->_friendsChange = $array['friends_change'];
        }
        if(isset($array['views_change'])) {
            $hyvesUser->_viewsChange = $array['views_change'];
        }

        return $hyvesUser;
    }

    public static function fromXMLObject($user) {
        $hyvesUser = new self();
        $hyvesUser->_ID = (string)$user->userid;
        if((string)$user->lastname) {
            $hyvesUser->_name = (string)$user->firstname." ".(string)$user->lastname;
        } else {
            $hyvesUser->_name = (string)$user->firstname;
        }
        $hyvesUser->_username  = self::profileURLToUsername((string)$user->url);
        $hyvesUser->_pictureLink = self::processPictureLink((string)$user->profilepicture->icon_small->src);
        $hyvesUser->_created = (int)$user->created;

        $hyvesUser->_views = (int)$user->viewscount;
        $hyvesUser->_friends = (int)$user->friendscount;

        $hyvesUser->insertStat((int)$user->viewscount, (int)$user->friendscount);
        return $hyvesUser;
    }

    public function updateFromXMLObject($user) {
        if((string)$user->lastname) {
            $this->_name = (string)$user->firstname." ".(string)$user->lastname;
        } else {
            $this->_name = (string)$user->firstname;
        }
        $this->_username  = self::profileURLToUsername((string)$user->url);
        $this->_pictureLink = self::processPictureLink((string)$user->profilepicture->icon_small->src);
        $this->_views = (int)$user->viewscount;
        $this->_friends = (int)$user->friendscount;


        $this->insertStat((int)$user->viewscount, (int)$user->friendscount);
    }

    public static function fetchNewUserByUsername($username) {
        $result = Tools::getHyvesUsersByUsername($username);
        if(!isset($result->user)) {
            return false;
        }
        $user = $result->user;

        $hyvesUser = self::fromXMLObject($user);
        $hyvesUser->put();
        return $hyvesUser;
    }

    public static function fetchNewUserByID($id) {
        $result = Tools::getHyvesUsersByUserID($id);
        if(!isset($result->user)) {
            return false;
        }
        $user = $result->user;

        $hyvesUser = self::fromXMLObject($user);
        $hyvesUser->put();
        return $hyvesUser;
    }

    public function update() {
        $hyves = Tools::getHyves();
        $result = $hyves->doMethod('users.get', array('userid' => $this->_ID, 'ha_responsefields' => 'viewscount,profilepicture'));
        if(isset($result->user)) {
            $this->updateFromXMLObject($result->user);
        }
    }

    public function put() {
        $format = "INSERT INTO users (id, username, name, created, firstlog, picturelink, views, friends)
        VALUES ('%s', '%s', '%s', '%s', NOW(), '%s', %d, %d)
        ON DUPLICATE KEY UPDATE username = VALUES(username), name = VALUES(name), picturelink = VALUES(picturelink), lastupdate = NOW(), views = VALUES(views), friends = VALUES(friends)";
        $query = sprintf($format,
            DB::escape($this->_ID),
            DB::escape($this->_username),
            DB::escape($this->_name),
            date('Y-m-d H:i:s', $this->_created),
            DB::escape($this->_pictureLink),
            $this->_views,
            $this->_friends);
        DB::query($query);
    }

    private static function processPictureLink($link) {
        $count = 1;
        return str_replace_once('_1', '_%d', $link, $count);
    }

    public static function profileURLToUsername($url) {
        preg_match('/^http:\/\/([\da-zA-Z][\da-zA-Z-]{1,20}[\da-zA-Z]).hyves.nl\/$/', $url, $match);
        return $match[1];
    }

    private function insertStat($views, $friends) {
        $viewsChange = 0;
        $friendsChange = 0;

        if($lastStat = DB::row(sprintf("SELECT views, friends FROM stats WHERE user_id = '%s' ORDER BY date_inserted DESC LIMIT 1", $this->_ID))) {
            $viewsChange = $views - $lastStat['views'];
            $friendsChange = $friends - $lastStat['friends'];
        }

        $format = "INSERT INTO stats (user_id, views, friends, views_change, friends_change, date_inserted) VALUES ('%s', %d, %d, %d, %d, NOW())";
        $query = sprintf($format, DB::escape($this->_ID), $views, $friends, $viewsChange, $friendsChange);
        DB::query($query);
    }
}
