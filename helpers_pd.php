<?php
	include ("Db_pd.php");
	$db = new Db();

function parseMetaData(){
    //adds fields from get parameters starting with meta-<field>
    $array_meta = array_filter($_GET, function ($v) {
        return substr($v, 0, 5) == 'meta-';
    }, ARRAY_FILTER_USE_KEY);
    $ret_array = array();
    foreach ($array_meta as $key => $value){
        $ret_array[substr($key, 5)] = $value;
    }
    return $ret_array;
}
    
function db_charasteristics($pid) {
    //reads and returns the charasteristics of the database
    global $db;
    $characs = array();
    $techniques = array();
    $ratings = array();
    $pd_pat = 0;
    $pd_care = 0;

    $qpid = $db -> quote($pid);

    $t = $db -> select("SELECT option_id FROM options HAVING problem_id = $qpid");

    file_put_contents('php://stderr', print_r($t, TRUE));

    foreach($t as $key => &$item) {
        $techniques[] = $item['option_id'];
    }

    $r = $db -> select("SELECT DISTINCT user_id, option_id FROM ratings HAVING problem_id = $qpid");

    foreach($r as $key => &$item) {
        $ratings[] = $item['user_id'];
    }
    
    /*$users = $db -> select("SELECT DISTINCT log_user, log_data FROM misc_logdata");

    foreach($users as $key => &$value) {
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


function printCriteriaSliders($pid){
    $htmlstr = "";
    global $db;
    $qpid = $db -> quote($pid);
    $result = $db -> select("SELECT criterion_id, criterion_title, criterion_details, low_title, high_title, low_val, high_val FROM criteria WHERE problem_id = $qpid AND status = 1");
    if($result === false) {
        error_log($db -> error());
        die();
    } else {
        foreach ($result as $r) {
            $title = $r["criterion_title"];
            $details = $r["criterion_details"];
            $id = $r["criterion_id"];
            $lt = $r["low_title"];
            $ht = $r["high_title"];
            $low_val=$r["low_val"];
            $high_val=$r["high_val"];

            $htmlstr = $htmlstr . "<div class='row my-4'><div class='col-12'><h5>$title: <span data-id='$id' class='badge badge-pill badge-warning criterionvalue'>-</span><br/><small>$details</small></h5><input type='range' min='$low_val' max='$high_val' width='100%' value='-1' class='sliders my-0' data-id='$id'></input><div class='mt-n1'><span class='float-left text-muted'>$lt</span><span class='float-right text-muted'>$ht</span></div></div></div>";  
            
            
        }
    }
    if ($htmlstr == "") {
        echo "<p class='lead'>¯\_(ツ)_/¯ this problem has no criteria defined.</p>";
    } else {
        echo $htmlstr;
    }
    
}
    
?>