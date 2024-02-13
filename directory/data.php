<?php
header('Access-Control-Allow-Origin: *');
header('Content-type: application/json');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
require($_SERVER['DOCUMENT_ROOT'] . "/api/classes/Database.php");
try {

    $data = json_decode(file_get_contents('php://input'), true);
    $token = apache_request_headers()["Authorization"] ?? "";
    $idDirectory = $data['id'];
    $con = new Database();
    $con->connect();
    $idUser = $con->checkToken($token);
    if ($idUser) {
        $sql = "SELECT dir.name,dir.id,dir.namePath from directories as dir
    INNER JOIN users as u ON u.id = dir.id_user
    WHERE dir.id_directory = :id AND dir.id_user = :idUser";
        $result = $con->query($sql, [':id' => $idDirectory, ':idUser' => $idUser]);

        $sql = "SELECT dir2.namePath from directories as dir
    inner join directories dir2 on dir2.id = dir.id_directory
    WHERE dir.id = :id AND dir.id_user = :idUser";
        $resultPath = $con->query($sql, [':id' => $idDirectory, ':idUser' => $idUser]);
        if($resultPath === false || count($resultPath) === 0){
            $resultPath[0]['namePath'] = '';
        }
        //the same with directories and files
        $sql = "SELECT fi.name,fi.id from files as fi
    INNER JOIN directories as dir ON dir.id = fi.id_directory
    WHERE dir.id = :id";
        $resultFiles = $con->query($sql, [':id' => $idDirectory]);
        echo json_encode(['data' => ['status' => 200, 'directories' => $result, 'files' => $resultFiles, 'prevPath' => $resultPath[0]['namePath']]]);
    }

} catch (Exception $e) {
    echo json_encode(['error' => 'An error occurred']);
}