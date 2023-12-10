<?php
include_once("mysql_connect.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orgno = $_POST['orgno'];

    $org_id = checkOrgNo($orgno);
    if (empty($org_id)) {

        if (empty($_POST['title'])) {
            echo "Title is empty.";
            exit;
        }
        
        $params = [
            ["s", $_POST['title']],
            ["s", $_POST['orgno']],
        ];
       
        $org_id = createOrg($params);
        if (!$org_id) {
            echo "create Org fail.";
            exit;
        }
    }

    $name = $_POST['name'];
    $birthday = isset($_POST['birthday']) ? $_POST['birthday'] : "0000-00-00";
    $email = $_POST['email'];
    $account = $_POST['account'];
    $password = $_POST['password'];
    $qualification_file = $_FILES['qualification_file'];

    check_post ($name, $birthday, $email, $account, $password, $qualification_file);

    
    
    $upload_dir = "uploads/"; 
    $file_path = $upload_dir . basename($qualification_file['name']);

    //if (move_uploaded_file($qualification_file['tmp_name'], $file_path)) {
        $params = [
            ["i", $org_id],
            ["s", $name],
            ["s", $email],
            ["s", $birthday],
            ["s", $account],
            ["s", $password],
        ];

        $user_id = createUser($params);
        if (!$user_id) {
            echo "create User fail.";
            exit;
        }

        $params = [
            ["i", $user_id],
            ["s", $file_path],
        ];

        $fild_id = createAppleFile($params);
        if (!$fild_id) {
            echo "create Apply File fail.";
            exit;
        }

        echo "Create User Success, ";
        echo "Upload File Success, file path:$file_path";
        
    //} else {
        //echo "Save file fail.";
    //}
    

}

function check_post ($name, $birthday, $email, $account, $password, $qualification_file) {

    if (empty($name)) {
        echo "Name is empty.";
        exit;
    }

    if (!empty($birthday)) {
        $date = DateTime::createFromFormat('Y-m-d', $birthday);
        if ($date instanceof DateTime) {
            $birthday = $date->format('Y-m-d');
        } else {
            echo "Date Format is wrong.";
            exit;
        }
    }

    if (!preg_match('/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/', $email)) {
        echo "Email Format is wrong.";
        exit;
    }

    if (empty($account)) {
        echo "Account is empty.";
        exit;
    }

    if (empty($password)) {
        echo "password is empty.";
        exit;
    }


    if ($qualification_file['error'] !== UPLOAD_ERR_OK) {
        echo "upload file fail";
        exit;
    }
}

function checkOrgNo ($orgno) {
    $org_id = "";
    $conn = connent();

    $sql = "SELECT * FROM orgs where org_no = ? LIMIT 1";
    $params = [
        ['s', $orgno],
    ];
    $row = executeQuery($conn, $sql, $params);

    close_connect($conn);

    if (count($row) > 0) {
        $org_id = $row["id"];
    } 

    return $org_id;
}

function createOrg ($params) {
    $conn = connent();
    $sql = "INSERT INTO orgs (`title`, `org_no`, `created_dt`, `updated_dt`) VALUES (?, ?, now(), now())";
    $id = insertQuery($conn, $sql, $params);
    close_connect($conn);

    return $id;
}

function createUser ($params) {
    $conn = connent();
    $sql = "INSERT INTO users (`org_id`, `name`, `email`, `birthday`, `account`, `password`, `status`, `created_dt`, `updated_dt`)
    VALUES (?, ?, ?, ?, ?, ?, 'pending', now(), now())";
    $id = insertQuery($conn, $sql, $params);
    close_connect($conn);

    return $id;
}

function createAppleFile ($params) {
    $conn = connent();
    $sql = "INSERT INTO apply_file (`user_id`, `file_path`, `created_dt`, `updated_dt`)
    VALUES (?, ?, now(), now())";
    $id = insertQuery($conn, $sql, $params);
    close_connect($conn);

    return $id;
}

?>
