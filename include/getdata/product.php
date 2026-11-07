<?php

trait GetdataProductTrait
{
    //product
    public function FetchProductData($productID)
    {
        $conn = db();
        $stmt = $conn->prepare('select * FROM tbl_product where id=?');
        $stmt->bind_param('s', $productID);
        $stmt->execute();
        $recordObj = $stmt->get_result();
        $stmt->close();
        return $recordObj->fetch_assoc();
    }

    public function FetchProductCatName($id)
    {
        $conn = db();
        $sql = "SELECT title FROM tbl_product_category WHERE id=?";
        $type = 's';
        $param = [$id];

        $stmt = $conn->prepare($sql);
        $stmt->bind_param($type, ...$param);
        $stmt->execute();
        $title = '';
        $stmt->bind_result($title);
        $stmt->fetch();
        $stmt->close();

        return $title;
    }

    public function FetchProductCatId($id)
    {
        $conn = db();
        $sql = "SELECT id FROM tbl_product_category WHERE id=?";
        $type = 's';
        $param = [$id];

        $stmt = $conn->prepare($sql);
        $stmt->bind_param($type, ...$param);
        $stmt->execute();
        $title = '';

        $stmt->bind_result($title);
        $stmt->fetch();
        $stmt->close();

        return $title;
    }

    public function FetchProductName($id)
    {
        $conn = db();
        $sql = "SELECT meta_title FROM tbl_product WHERE id=?";
        $type = 's';
        $param = [$id];

        $stmt = $conn->prepare($sql);
        $stmt->bind_param($type, ...$param);
        $stmt->execute();
        $title = '';

        $stmt->bind_result($title);
        $stmt->fetch();
        $stmt->close();

        return $title;
    }
}

