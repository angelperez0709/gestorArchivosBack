<?php
header('Access-Control-Allow-Origin: *');
header('Content-type: application/json');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
require($_SERVER['DOCUMENT_ROOT'] . "/api/classes/DatabaseImpl.php");
require($_SERVER['DOCUMENT_ROOT'] . "/api/classes/SessionManager.php");

try {
    $response = new stdClass();
    $data = json_decode(file_get_contents('php://input'), true);
    $token = $data["token"];
    $directory = $data['parentDirectory'];
    $con = new DatabaseImpl();

    if ($con->checkToken($token)) {
        //create a sql to get the id of the directory from the user and path
        $rows = $con->prepareQuery(
            "select",
            "directories dir",
            ["dir.id"],
            ["token" => $token, "namePath" => $directory],
            ["joins" => ["INNER JOIN" => " users us on us.id = dir.id_user"], "where" => "us.token = :token and dir.namePath=:namePath"]
        );

        if ($rows !== false && count($rows) === 1) {
            $idDirectory = $rows[0]['id'];
            $path = $con->Preparequery("function", null, ["GetFullPath(:idDirectory) path"], ["idDirectory" => $idDirectory], [])[0]['path'];
            if ($path !== false) {
                $response->data = ['id' => $idDirectory, 'path' => $path];
            }
        } else {
            $response->error = "Invalid directory";
        }

    } else {
        $response->error = "Invalid token Authentication";
    }
} catch (Exception $e) {
    $response->error = "Internal server error";
} finally {
    echo json_encode($response);
}