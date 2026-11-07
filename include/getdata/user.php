<?php

trait GetdataUserTrait
{
    //user
    public function FetchUsername($user_id)
    {
        $conn = db();
        $sql = "SELECT username FROM tbl_user WHERE user_id=?";
        $type = 's';
        $param = [$user_id];

        $stmt = $conn->prepare($sql);
        $stmt->bind_param($type, ...$param);
        $stmt->execute();
        $username = '';
        $stmt->bind_result($username);
        $stmt->fetch();
        $stmt->close();

        return $username;
    }
}

