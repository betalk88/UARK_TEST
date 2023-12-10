<?php


function connent() {
    // database params
    $host = 'localhost';
    $user = 'root';
    $password = 'root';
    $database = 'test';

    // create connect
    $conn = new mysqli($host, $user, $password, $database);

    // check connect
    if ($conn->connect_error) {
        die("connect fail: " . $conn->connect_error);
    }

    return $conn;
}

// $sql = "SELECT * FROM your_table";
// $result = $conn->query($sql);

function executeQuery(mysqli $conn, $sql, array $params = []) {
    // sql
    $stmt = $conn->prepare($sql);

    // check sql
    if ($stmt === false) {
        die("check sql fail: " . $conn->error);
    }

    // bind
    if (!empty($params)) {
        $type = "";
        $bind = array();
        foreach ($params as $value) {
            $type .= $value[0];
            $bind[] = $value[1];
        }
        $stmt->bind_param($type, ...$bind);
    }

    // run
    $stmt->execute();

    // get result
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $rows = $result->fetch_array();
    } else {
        $rows = [];
    }
    
    // close
    $stmt->close();

    return $rows;
}

function insertQuery(mysqli $conn, $sql, array $params = []) {
    // sql
    $stmt = $conn->prepare($sql);

    // check sql
    if ($stmt === false) {
        die("check sql fail: " . $conn->error);
    }

    // bind
    if (!empty($params)) {
        $type = "";
        $bind = array();
        foreach ($params as $value) {
            $type .= $value[0];
            $bind[] = $value[1];
        }
        $stmt->bind_param($type, ...$bind);
    }

    // run
    $result = $stmt->execute();
    if ($result) {
        $id = $stmt->insert_id;
    } else {
        $id = false;
    }
    // close
    $stmt->close();

    return $id;
}


function close_connect($conn) {
    $conn->close();
    return;
}

?>
