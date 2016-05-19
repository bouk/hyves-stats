<?php
require ROOT.'/constants.php';

$paths = array(
    array('/', 'FrontPage'),
    array('/hyver/nieuw', 'NewUser'),
    array('/hyver/([\da-zA-Z][\da-zA-Z-]{1,20}[\da-zA-Z])', 'UserPage'),
    array('/(top|stijgend)/(vrienden|bekeken)(?:/([\d]+))?', 'Ranking'),
    array('/(dalend)/(vrienden)(?:/([\d]+))?', 'Ranking'),
    array('/zoeken', 'Search'),
    array('/cron/updateHyvers', 'UpdateHyversCron')
);
Webapp::start($paths);
