<?php
    include ("Db_pd.php");
//TODO: Figure out what to do when a option-criteria pair has no ratings given in the database...how does it deal with it
    $user_id = "'NA'";
	$problem_id = "5fb26bc3e762c";
	$criteria_importances = ""; // array, criterion_id => importance (1-100), N entries
	$num_of_options = 5; //how many best ones to give, default 5
    $meta = "''";

	$db = new Db();
	
	if (isset($_POST['user_id'])) {
		$user_id = $db -> quote($_POST["user_id"]);
	}

	if (isset($_POST['problem_id'])) {
		$problem_id = $db -> quote($_POST["problem_id"]);
	}

    $p = $db -> select("SELECT allow_decision_support FROM problems WHERE problem_id = $problem_id");
    if ($p[0]["allow_decision_support"] == "0") {
        die();
    };
	
	if (isset($_POST['criteria_importances'])) {
		$criteria_importances = $_POST["criteria_importances"];
	}
	
	if (isset($_POST['num_of_options'])) {
		$num_of_options = $_POST["num_of_options"];
	}

	if (isset($_POST['meta'])) {
		$meta = $db -> quote($_POST["meta"]);
	}

//euclidean(array(120, 255, 0), array(130, 255, 10));

	if ($problem_id == "" || $criteria_importances == "") {
		//No question set, return a simple error msg
		$error = "No problem_id OR criteria_importances array set, cannot retrieve recommendations";
		$array = array('success' => 'false','error' => $error);
		echo json_encode($array);
        die();
	}

function euclidean(array $a, array $b) {
    if (($n = count($a)) !== count($b)) return false;
    $sum = 0;
    for ($i = 0; $i < $n; $i++)
        $sum += pow($b[$i] - $a[$i], 2);
    return sqrt($sum);
}

function nnsort($a, $b)
{
    if ($a['avg_distance'] == $b['avg_distance']) {
        return 0;
    }
    return ($a['avg_distance'] < $b['avg_distance']) ? -1 : 1;
}



function computeMedian($problem_id, $optionId, $cid) {
    $medianSql = "SELECT x.criterion_id, x.rating from (select * from ratings where status=1 and problem_id = $problem_id and criterion_id=$cid and option_id = $optionId) x, (select * from ratings where status=1 and problem_id = $problem_id and criterion_id=$cid and option_id = $optionId) y GROUP BY x.rating HAVING SUM(SIGN(1-SIGN(y.rating-x.rating)))/COUNT(*) > .5 LIMIT 1";
    
    global $db;
    $median = $db -> select($medianSql)[0]['rating'];
    
    if (isset($median)){
        return $median;
    } else {
        return 0;    
    }
    
}

    $client_info = array("ip" => $_SERVER['REMOTE_ADDR'], "useragent" => $_SERVER['HTTP_USER_AGENT'], "referer" => $_SERVER['HTTP_REFERER'], "referer" => $_SERVER['HTTP_REFERER']);
    $insert_info = $db -> quote(json_encode($client_info));
    $insert_importances = $db -> quote($criteria_importances);
    $insert_importances = preg_replace('/\s+/', '', $insert_importances);

//log what was asked
	$result = $db -> query("INSERT INTO supportrequests (user_id, problem_id, req_criteria_importances_json, meta, client_info) VALUES (" . $user_id . "," . $problem_id . "," . $insert_importances . "," . $meta . "," . $insert_info . ")");

    if($result === false) {
        $error = $db -> error();
        $array = array('success' => 'false','error' => $error);
        echo json_encode($array);
        die();
    } 
    
    //get all options for the given question, process each one of them to find out their "match" to the given ratings of the criteria...
    $alloptions = $db -> select("SELECT primary_link, option_id, option_title, option_details FROM options WHERE problem_id=$problem_id and status = 1 order by option_id");
    if($alloptions === false) {
        $error = $db -> error();
        $array = array('success' => 'false','error' => $error);
        echo json_encode($array);
        die();
    }



    //an array of criterion id => importance
    $idealCriteriaValues = (array)json_decode($criteria_importances, true);

    $idealValArray = array();
    $criteriaStr = "(";
    foreach($idealCriteriaValues as $crit){
        $cid = $crit[0];
        $cval = $crit[1];
        $idealValArray[$cid] = $cval;
        $criteriaStr = $criteriaStr . "criterion_id =  " . $cid . " OR ";
        }
    $criteriaStr = substr($criteriaStr, 0, strlen($criteriaStr)-4) . ")";
    ksort($idealValArray); //sort, so we get stuff in same order in the next query...

    foreach($alloptions as $key => &$option){
        $optionId = $option['option_id'];
        //averages per each criterion
        $optionAverages = array();
        $sel = "select criterion_id, avg(rating) as avg from ratings where status=1 and problem_id = $problem_id and (option_id = $optionId) and $criteriaStr group by criterion_id order by criterion_id";
        $optionAverages = $db -> select($sel);
        $optionAveragesArray = array();
        foreach($optionAverages as $avg){
            $critId = $avg['criterion_id'];
            $critAvg = $avg['avg'];
            $optionAveragesArray[$critId] = $critAvg;
        }
        //if we don't have data in the knowledge base for all requested criteria ideal values, simply don't display the option
        if(count($idealValArray) != count($optionAveragesArray)) {
            unset($alloptions[$key]);
        }

        
        $dist = round(euclidean(array_values($idealValArray), array_values($optionAveragesArray)), 1);

        
        
        $option['avg_distance'] = $dist;

        //// to check if the option can be performed home
        //$home_sel = "select distinct meta from ratings group by meta, option_id having option_id= $optionId";
        //$optionHome = $db -> select($home_sel);
        //foreach($optionHome as $key => &$value) {
        //    foreach($value as $key => &$text){
        //        if (strpos($text, "practicedHome") !== false) {
        //            $option['practiced_home'] = true;
        //        }
        //    }
        //}

    }


    $alloptionsAvg = $alloptions;
    usort($alloptionsAvg, "nnsort"); //sort, based on distance
    echo json_encode(array_slice($alloptionsAvg, 0, intval($num_of_options)));



?>