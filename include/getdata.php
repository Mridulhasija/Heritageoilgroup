<?php

require_once __DIR__ . '/connection.php';

require_once __DIR__ . '/getdata/sanitization.php';
require_once __DIR__ . '/getdata/db.php';
require_once __DIR__ . '/getdata/auth.php';
require_once __DIR__ . '/getdata/currency.php';
require_once __DIR__ . '/getdata/product.php';
require_once __DIR__ . '/getdata/user.php';
require_once __DIR__ . '/getdata/seo.php';

class Getdata
{
    public $db;

    use GetdataSanitizationTrait;
    use GetdataDbTrait;
    use GetdataAuthTrait;
    use GetdataCurrencyTrait;
    use GetdataProductTrait;
    use GetdataUserTrait;
    use GetdataSeoTrait;
}
