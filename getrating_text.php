<?php
    include ("Db_pd.php");

    $problem_id = "5fb26bc3e762c";
    $option_id = "";
    $rating_texts = array();
    $user_sel = "";
    $user_i = "";
    $user_id = "";
    $sel_year = 0;
    $sel_diag = 0;
    $cur_year = (int)date("Y");
    $i = 0;

    $db = new Db();

    if (isset($_POST['option_id'])) {
        $option_id = $db -> quote($_POST['option_id']);
    }

    $sel = "select distinct meta, user_id from ratings group by meta, option_id, user_id having option_id=$option_id";

    $r = $db -> select($sel);

    foreach($r as $key => &$meta) {

        if (strpos($meta['meta'], "practicedHome") == false) {
            $parsed = substr($meta['meta'], 18, -3);
            $parsed = str_replace(['\r\n', '\r', '\n'], ". ", $parsed);
            $parsed = str_replace('\"', '"', $parsed);
            if ($parsed !== "" && $parsed !== "?") {
                $rating_texts[$i]['text'] = $parsed;

                if ($meta['user_id']) {
                    $user_id = $db -> quote($meta['user_id']);
                }

                $user_sel = "select log_data from misc_logdata group by log_data, log_user having log_user=$user_id";
                $user_i = $db -> select($user_sel);

                if($user_i == false) {
                    $rating_texts[$i]['info'] = false;
                    $i++;
                    continue;
                } else {
                    $rating_texts[$i]['info'] = true;
                }

                foreach($user_i as $key => &$data) {
                    $json = $data['log_data'];
                    if(empty($json)) {
                        continue;
                    }

                    $parsed_json = json_decode($json, true);

                    $sel_year = (int)$parsed_json['BirthYear'];
                    $sel_diag = (int)$parsed_json['yearsSinceDiagnosis'];

                    if(abs($sel_year) > $cur_year) {
                        $sel_year = substr($sel_year, -4);
                    }

                    if(abs($sel_year) < 1900){
                        $rating_texts[$i]['birthyear'] = (($cur_year - abs($sel_year)) - 1900);
                    } else {
                        $rating_texts[$i]['birthyear'] = ($cur_year - abs($sel_year));
                    }

                    if(abs($sel_diag) > 1900) {
                        $rating_texts[$i]['yearsSinceDiagnosis'] = ($cur_year - abs($sel_diag));
                    } else {
                        $rating_texts[$i]['yearsSinceDiagnosis'] = abs($sel_diag);
                    }
                    
                    $rating_texts[$i]['havePD'] = $parsed_json['havePD'];
                }
            }
        } else {
            $parsed = substr($meta['meta'], 18, -29);
            $parsed = str_replace(['\r\n', '\r', '\n'], ". ", $parsed);
            $parsed = str_replace('\"', '"', $parsed);
            if ($parsed !== "" && $parsed !== "?") {
                $rating_texts[$i]['text'] = $parsed;

                if ($meta['user_id']) {
                    $user_id = $db -> quote($meta['user_id']);
                }

                $user_sel = "select log_data from misc_logdata group by log_data, log_user having log_user=$user_id";
                $user_i = $db -> select($user_sel);

                if($user_i == false) {
                    $rating_texts[$i]['info'] = false;
                    $i++;
                    continue;
                } else {
                    $rating_texts[$i]['info'] = true;
                }

                foreach($user_i as $key => &$data) {
                    $json = $data['log_data'];
                    if(empty($json)) {
                        continue;
                    }

                    $parsed_json = json_decode($json, true);

                    $sel_year = (int)$parsed_json['BirthYear'];
                    $sel_diag = (int)$parsed_json['yearsSinceDiagnosis'];

                    if(abs($sel_year) < 1900){
                        $rating_texts[$i]['birthyear'] = (($cur_year - abs($sel_year)) - 1900);
                    } else {
                        $rating_texts[$i]['birthyear'] = ($cur_year - abs($sel_year));
                    }

                    if(abs($sel_diag) > 1900) {
                        $rating_texts[$i]['yearsSinceDiagnosis'] = ($cur_year - abs($sel_diag));
                    } else {
                        $rating_texts[$i]['yearsSinceDiagnosis'] = abs($sel_diag);
                    }
                    
                    $rating_texts[$i]['havePD'] = $parsed_json['havePD'];

                }

            }
        }

        $i++;
    }

    echo json_encode($rating_texts);

?>