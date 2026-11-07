<?php

trait GetdataSeoTrait
{
    public function FetchSeoMeta($pgurl)
    {
        $qry = "SELECT * FROM tbl_page WHERE url = '$pgurl'";
        $conn = db();
        $recordObj = $conn->query($qry);
        return $recordObj;
    }
}

