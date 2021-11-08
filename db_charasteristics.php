<?php

    $db = new Db();

function db_charasteristics($pid) {
    
    global $db;
    $characs = array();
    $techniques = array();
    $ratings = array();
    $pd_pat = 0;
    $pd_care = 0;

    $t = $db -> select("SELECT option_id FROM options WHERE problem_id = $pid");

    file_put_contents('php://stderr', print_r($t, TRUE));

    foreach($t as &$item) {
        $techniques[] = $item['option_id'];
    }

    $r = $db -> select("SELECT DISTINCT user_id, option_id FROM ratings WHERE problem_id = $pid");

    foreach($r as &$item) {
        $ratings[] = $item['user_id'];
    }
    
    $users = $db -> select("SELECT DISTINCT log_user, log_data FROM misc_logdata");

    /*foreach($users as $key => &$value) {
        $decode = $value['log_data'];
        if ($decode !== "") {
            if ($decode['havePD'] == "PD_yes") {
                $pd_pat++;
            } if ($decode['havePD'] == "PD_caretaker") {
                $pd_care++;
            }
        }
    }*/

    $characs['techniques'] = count($techniques);
    $characs['ratings'] = count($ratings);
    $characs['pd_pat'] = $pd_pat;
    $characs['pd_care'] = $pd_care;

    return $characs;
}

?>