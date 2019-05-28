<?php
    $field = $_POST["field"];
    $value = $_POST["value"];
    if(array_key_exists("start", $_POST)){
        $start = $_POST["start"];
    }else {
        $start = 0;
    }
    if(array_key_exists("rows", $_POST)){
        $rows = $_POST["rows"];
    }else {
        $rows = 10;
    }
    $str = urlencode(str_replace(' ','+',$field.":".$value));
    $ch = curl_init();
    $timeout = 10;
    $url = "http://acemap.lifanz.cn:8983/solr/ee101_core_1/select?indent=on&q=".$str."&rows=".$rows."&start=".$start."&wt=json";
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    $result=json_decode(curl_exec($ch), true);
    curl_close($ch);
    //echo $url;
    echo json_encode($result);
?>