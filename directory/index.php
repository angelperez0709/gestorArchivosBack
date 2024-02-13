<?php
header('Access-Control-Allow-Origin: *');
header('Content-type: application/json');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
require($_SERVER['DOCUMENT_ROOT'] . "/api/classes/DatabaseImpl.php");

try {
    session_start();
    $data = json_decode(file_get_contents('php://input'), true);
    $token = apache_request_headers()["Authorization"] ?? "";

    $directory = $data['parentDirectory'];
    $con = new DatabaseImpl();

    if ($token == $_SESSION['token']) {
        //create a sql to get the id of the directory from the user and path
        $sql = "SELECT dir.id
    FROM directories dir
    INNER JOIN users us on dir.id_user = us.id
    WHERE us.token = :token and dir.namePath=:namePath";
        $rows = $con->Preparequery($sql, ["token" => $token, "namePath" => $directory]);
        if ($rows !== false && count($rows) === 1) {
            $idDirectory = $rows[0]['id'];
            $sql = "SELECT GetFullPath(:idDirectory) as path";
            $path = $con->Preparequery($sql, ["idDirectory" => $idDirectory])[0]['path'];
            if ($path !== false) {
                if (isset($_SESSION['currentDir'])) {
                    $_SESSION['lastDir'] = $_SESSION['currentDir'];
                }
                $_SESSION['currentDir'] = $path;
                echo json_encode(['data' => ['id' => $idDirectory, 'path' => $path]]);
            }
        } else {
            echo json_encode(['error' => 'Invalid directory']);
        }

    } else {
        echo json_encode(['error' => 'Invalid token']);
    }
} catch (Exception $e) {
    echo json_encode(['error' => 'An error occurred']);
}