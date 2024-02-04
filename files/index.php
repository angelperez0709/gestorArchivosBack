<?php
header('Access-Control-Allow-Origin: *');
header('Content-type: application/json');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
require($_SERVER['DOCUMENT_ROOT'] . "/api/classes/Database.php");
$data = json_decode(file_get_contents('php://input'), true);
$token = apache_request_headers()["Authorization"] ?? "";
$pathList = json_decode($data['path'], true);

 $con = new Database();
 $con->connect();
 if ($con->checkToken($token)) {
    //Create an sql to get all files from the user and path
    $sql = "SELECT fl.name
    FROM files fl
    INNER JOIN directories dir on fl.id_directory = dir.id
    WHERE dir.id_user = :id_user AND dir.name = :path";
    $result = $con->query($sql,[':id_user',$result[0]['id'],':path',$path]);
    echo json_encode($result);
} 

