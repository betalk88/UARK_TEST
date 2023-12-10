<?php
include_once("mysql_connect.php");


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $account = $_POST['account'];
    $password = $_POST['password'];

    $return = Login($account, $password);
    echo $return;
} else {
    phpinfo();
}

function Login($account, $password) {

    $conn = connent();

    $sql = "SELECT * FROM users where account = ? and password = ? LIMIT 1";
    $params = [
        ['s', $account],
        ['s', $password],
    ];
    $row = executeQuery($conn, $sql, $params);

    close_connect($conn);

    if (count($row) > 0) {
        switch ($row["status"]) {
            case "Pending":
                $return = "此帳號待審核開通";
                break;
            case "Open":
                $return = "登入成功";
                break;
            case "Close":
                $return = "此帳號已關閉";
                break;
            default:
                $return = "未知狀態";
                break;
        }
    } else {
        $return = "帳號或密碼錯誤";
    }
    return $return;
}
?>