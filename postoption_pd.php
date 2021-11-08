<?php
include ("helpers_pd.php");

	$user_id = "";
	$problem_id = "";
	$option_title = "";
	$option_details = "'No details provided.'";
    $option_url = "''";
	$meta = "";
    $status = "1"; //default, yes, it's status.



	$db = new Db();


    

    $client_info = array("ip" => $_SERVER['REMOTE_ADDR'], "useragent" => $_SERVER['HTTP_USER_AGENT'], "referer" => $_SERVER['HTTP_REFERER']);
    $client_info = $db -> quote(json_encode($client_info));

	//if values set, escape and quote properly to be insterted to db
	if (isset($_POST['user_id'])) {
		$user_id = $db -> quote($_POST["user_id"]);
	}
    if (isset($_POST['problem_id'])) {
		$problem_id = $db -> quote($_POST["problem_id"]);
	}
	if (isset($_POST['title'])) {
		$option_title = $db -> quote($_POST["title"]);
	}
	if (isset($_POST['details'])) {
	   $option_details = $db -> quote($_POST["details"]);
	}
    if (isset($_POST['url'])) {
	   $option_url = $db -> quote($_POST["url"]);
	}
	if (isset($_POST['meta'])) {
		$meta = $db -> quote($_POST["meta"]);
	}

    if(strlen($option_title) < 5){
        $status = "0";
    }

    //NOTE: this is odd, but quite future-proof -- this way the person who created the problem will be allowed to add more, once we have an admin view
    $p = $db -> select("SELECT allow_add_options, user_id FROM problems WHERE problem_id = $problem_id");
    if ($p[0]["allow_add_options"] == "0" && $p[0]["user_id"] != $_POST["user_id"]) {
        die();
    };

    if (filter_var($_POST["url"], FILTER_VALIDATE_URL)) {
        // Insert the values into the database, with url
        $result = $db -> query("INSERT INTO options (user_id, problem_id, option_title, option_details, primary_link, meta, status, client_info) VALUES (" . $user_id . "," . $problem_id . "," . $option_title . "," . $option_details . "," . $option_url . "," . $meta . "," . $status . "," . $client_info . ")");

        if($result === false) {
            $error = $db -> error();
            $array = array('success' => 'false','error' => $error);
            echo json_encode($array);
            die();
        } 
    } else {
        // Insert the values into the database, without url
        $result = $db -> query("INSERT INTO options (user_id, problem_id, option_title, option_details, meta, status, client_info) VALUES (" . $user_id . "," . $problem_id . "," . $option_title . "," . $option_details . "," . $meta . "," . $status . "," . $client_info . ")");

        if($result === false) {
            $error = $db -> error();
            $array = array('success' => 'false','error' => $error);
            echo json_encode($array);
            die();
        } 
    }


    $last_id = $db->inserted_id();
	$array = array('success' => 'true', 'inserted_id' => $last_id);
	echo json_encode($array);
	
	
?>