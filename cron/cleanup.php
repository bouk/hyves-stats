<?php
set_time_limit(0);
define('ROOT', dirname(dirname(__FILE__)));
require ROOT.'/constants.php';
echo "Starting cleanup script\n";

// delete inactive users after 3 days
$query = "DELETE users, stats FROM users INNER JOIN stats ON (users.id = stats.user_id) WHERE users.lastupdate < NOW() - INTERVAL 72 HOUR";
DB::query($query);

// purge old statistics after 15 days
$query = "DELETE FROM stats WHERE date_inserted < NOW() - INTERVAL 15 DAY";
DB::query($query);
