<?php
    $field = $_POST["field"];
    $value = $_POST["value"];
    $partial = $_POST["partial"];
    $str = urlencode(str_replace(' ','+',$field.":".$value));
    $ch = curl_init();
    $timeout = 10;
    $rows = 1048576;
    $url = "http://acemap.lifanz.cn:8983/solr/ee101_core_1/select?indent=on&q=".$str."&fl=".$partial."&rows=".$rows."&wt=json";
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    $result=json_decode(curl_exec($ch), true);
    curl_close($ch);
    //echo $url;
    $result = $result["response"];
    $retval = array();
    $retval["numFound"] = $result["numFound"];
    $result = $result["docs"];
    //var_dump($result);
    foreach($result as $confName){
        if(array_key_exists($confName[$partial], $retval)){
            $retval[$confName[$partial]] += 1;
        }else{
            $retval[$confName[$partial]] = (int)1;
        }
    }
    echo json_encode($retval);
?>