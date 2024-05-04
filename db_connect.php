<?php
// ***************************
// 接続
// ***************************
$mysqli = @ new mysqli($server, $user, $password, $dbname);
if ($mysqli->connect_error) {
    print "接続エラーです : ({$mysqli->connect_errno}) ({$mysqli->connect_error})";
    exit();
}

// ***************************
// クライアントの文字セット
// ***************************
$mysqli->set_charset("utf8"); 

