<?php
    $author_id = $_POST["author_id"];
    $author_name = $_POST["author_name"];
    $retval = array();
    $retval["name"] = ucwords($author_name);
    $retval["children"] = array();
    $link = mysqli_connect("db.lifanz.cn:3306", 'ee101_user', 'ee1012019', 'ee101');
    /*$result = mysqli_query($link, "SELECT a.PaperID as paperid from (papers a inner join paper_author_affiliation b on a.PaperID = b.PaperID) where AuthorID = '$author_id'");
    $idx = 1;
    while($row = mysqli_fetch_array($result)){
        $papernode = array();
        $papernode["name"] = "No.".$idx;
        $idx += 1;
        $papernode["children"] = array();
        $paper_id = $row["paperid"];
        //echo $paper_id;
        //echo "<br/>";
        $result_ = mysqli_query($link, "SELECT AuthorName , AuthorSequence from(authors a inner join paper_author_affiliation b on a.AuthorID = b.AuthorID) where PaperID = '$paper_id'");
        while($row_ = mysqli_fetch_array($result_)){
            if($row_["AuthorName"] != $author_name){
                $authornode = array();
                $authornode["name"] = "No.".$row_["AuthorSequence"]." ".ucwords($row_["AuthorName"]);
                $authornode["children"] = array();
                $papernode["children"][] = $authornode;
            }
        }
        $retval["children"][] = $papernode;
    }*/
    $result = mysqli_query($link, "SELECT d.PaperID as paperid, e.AuthorName as authorname, d.AuthorSequence as seq from (SELECT b.PaperID, c.AuthorID, c.AuthorSequence from((SELECT a.PaperID from paper_author_affiliation a where a.AuthorID = '$author_id') b inner join paper_author_affiliation c on b.PaperID = c.PaperID)) d inner join authors e on d.AuthorID = e.AuthorID");
    $tmp_p = "";
    $paper_node = array();
    $idx = 1;
    while($row = mysqli_fetch_array($result)){
        if($tmp_p != $row["paperid"]){
            if($tmp_p != ""){
                $retval["children"][] = $paper_node;
            }
            $paper_node = array();
            $paper_node["name"] = "No.".$idx;
            $idx += 1;
            $paper_node["children"] = array();
        }
        $tmp_p = $row["paperid"];
        if($row["authorname"] != $author_name){
            $authornode = array("name" => "No.".$row["seq"]." ".ucwords($row["authorname"]), "children" => array());
            $paper_node["children"][] = $authornode;
        }
    }
    if($tmp_p != ""){
        $retval["children"][] = $paper_node;
    }
    echo json_encode($retval);
?>