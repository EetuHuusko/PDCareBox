<?php
//TODO: If the same user has posted for the same option and problem, overwrite
include ("helpers_pd.php");

    $rating_instance_id = $db -> quote(uniqid());
	
    $user_id = "'NA'";
	$meta = "'NA'";
    $client_info = array("ip" => $_SERVER['REMOTE_ADDR'], "useragent" => $_SERVER['HTTP_USER_AGENT'], "referer" => $_SERVER['HTTP_REFERER']);
    $client_info = $db -> quote(json_encode($client_info));

    if (isset($_POST['user_id'])) {
		$user_id = $db -> quote($_POST["user_id"]);
	}

	if (isset($_POST['meta'])) {
		$meta = $db -> quote($_POST["meta"]);
	}
    //return the option id so that the client knows to e.g. delete 
    $oid = -1;


	if (isset($_POST['json_ratings'])) {
        $json_ratings = $_POST["json_ratings"];
        $decoded = json_decode($json_ratings);
        //problem, option, criterion, rating
        $oid = $decoded[0][1];
        $problem_id = $db -> quote($decoded[0][0]);
        $option_id = $db -> quoteNum($decoded[0][1]);
        
        $vals = array();
        foreach($decoded as $rating) {
            $criterion_id = $db -> quoteNum($rating[2]);
            $rating_score = $db -> quoteNum($rating[3]);
            array_push($vals, "(" . $user_id . "," . $problem_id . "," . $option_id . "," . $criterion_id . "," . $rating_score . "," . $meta . "," . $client_info . "," . $rating_instance_id . ")");
        }
        $valsStr = implode(",", $vals);
        
        
        //if the problem is NOT evergreen, we always delete the past entries from the same user for the same exact _option_ NOT all responses.
        $evg = $db -> select("SELECT evergreen FROM problems WHERE problem_id = $problem_id AND status = 1");
        if($evg === false) {
            error_log($db -> error());
            die();
        } else {
            if(count($evg) > 0 && $evg[0]["evergreen"] == "0"){
                $evg = $db -> query("DELETE FROM ratings WHERE problem_id = $problem_id AND user_id = $user_id AND option_id = $option_id");
                if($evg === false) {
                    error_log($db -> error());
                    die();
                } 
            }
        }
        
        $result = $db -> query("INSERT INTO ratings (user_id, problem_id, option_id, criterion_id, rating, meta, client_info, rating_instance_id) VALUES $valsStr");
            if($result === false) {
                error_log($db -> error());
                die();
            }   
		echo $oid;  
	}
	
?>