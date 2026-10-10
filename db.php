```php
<?php

$servername = "sql305.infinityfree.com";
$username   = "if0_43137831";
$password   = "Knhn2005";
$dbname     = "if0_43137831_college_resource";

$conn = mysqli_connect(
    $servername,
    $username,
    $password,
    $dbname
);

if (!$conn) {
    error_log("Database connection failed: " . mysqli_connect_error());
    die("Database connection failed. Please try again later.");
}

mysqli_set_charset($conn, "utf8mb4");

?>
```