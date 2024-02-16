<?php
header('Access-Control-Allow-Origin: *');
header('Content-type: application/json');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
require($_SERVER['DOCUMENT_ROOT'] . "/api/classes/DatabaseImpl.php");
try {
    $response = new stdClass();

    $data = json_decode(file_get_contents('php://input'), true);
    $token = apache_request_headers()["Authorization"] ?? "";
    $idDirectory = $data['id'];
    $con = new DatabaseImpl();
    $idUser = $con->checkToken($token);
    if ($idUser) {
        $result = $con->prepareQuery(
            "select",
            "directories dir",
            ["dir.name,dir.id,dir.namePath"],
            ["id" => $idDirectory, "idUser" => $idUser],
            ["where" => "dir.id_directory = :id AND dir.id_user = :idUser"]
        );
        $resultPath = $con->prepareQuery(
            "select",
            "directories dir",
            ["dir2.namePath"],
            [':id' => $idDirectory, ':idUser' => $idUser],
            [
                "where" => "dir.id = :id AND dir.id_user = :idUser",
                "joins" =>
                    ["INNER JOIN" => "directories dir2 ON dir2.id = dir.id_directory"]
            ]
        );
        if ($resultPath === false || count($resultPath) === 0) {
            $resultPath[0]['namePath'] = '';
        }
        $sql = "SELECT fi.name,fi.id from files as fi
    INNER JOIN directories as dir ON dir.id = fi.id_directory
    WHERE dir.id = :id";
        $resultFiles = $con->prepareQuery(
            "select",
            "files fi",
            ["fi.name,fi.id"],
            ["id" => $idDirectory],
            ["where" => "fi.id_directory = :id", "join" => ["INNER JOIN" => "directories dir ON dir.id = fi.id_directory"]]
        );
        if ($resultFiles === false || count($resultFiles) === 0) {
            $resultFiles = [];
        }
        $response->data = ["status" => 200, "directories" => $result, "files" => $resultFiles, "prevPath" => $resultPath[0]['namePath']];
        echo json_encode($response);
    }

} catch (Exception $e) {
    echo json_encode(['error' => 'An error occurred']);
}