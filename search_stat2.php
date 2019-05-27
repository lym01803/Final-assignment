<?php
    $field = $_POST["field"];
    $value = $_POST["value"];
    $str = urlencode(str_replace(' ','+',$field.":".$value));
    $ch = curl_init();
    $timeout = 10;
    $rows = 1048576;
    $url = "http://acemap.lifanz.cn:8983/solr/ee101_core_1/select?indent=on&q=".$str."&rows=".$rows."&wt=json&fl=ConferenceName,Year";
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    $result=json_decode(curl_exec($ch), true);
    curl_close($ch);
    //echo $url;
    $result = $result["response"]["docs"];
    $retval = array();
    $count = array(
        "AAAI" => 0,
        "IJCAI" => 0,
        "CVPR" => 0,
        "ECCV" => 0,
        "ICCV" => 0,
        "ICML" => 0,
        "SIGKDD" => 0,
        "NIPS" => 0,
        "ACL" => 0,
        "EMNLP" => 0,
        "NAACL" => 0,
        "SIGIR" => 0,
        "WWW" => 0,
    );
    foreach($result as $item){
        $conf = $item["ConferenceName"];
        if(array_key_exists($conf, $count)){
            $count[$conf] += 1;
        }else{
            $count[$conf] = (int)1;
        }
    }
    arsort($count);
    $retval["conf"] = array();
    $retval["num"] = array();
    $idx = 0;
    foreach($count as $key=>$val){
        $retval["conf"][] = $key;
        $retval["num"][] = $val;
    }
    echo json_encode($retval);
?>