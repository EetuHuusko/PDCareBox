<?php
$uid = "NA";
//in order of preference: cookie, prolific_pid (prolific.ac id), custom uid, random uid -- finally set cookie
$firstVisit = false;
if(isset($_COOKIE["uid"])){
    $uid = $_COOKIE["uid"];
} else if(isset($_GET["prolific_pid"])){
    $uid = $_GET["prolific_pid"];
} else if(isset($_GET["uid"])){
    $uid = $_GET["uid"];
} else {
    $uid = generateRandomUID();
    $firstVisit = true;
}
setcookie("uid", $uid, time() + (10 * 365 * 24 * 60 * 60), "/", "", 0);

function generateRandomUID ($length = 15) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        $secs = time();
        return "rand-" . $secs . $randomString;
}