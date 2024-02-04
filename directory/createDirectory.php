<?php
header('Access-Control-Allow-Origin: *');
header('Content-type: application/json');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
require($_SERVER['DOCUMENT_ROOT'] . "/api/classes/Database.php");
$data = json_decode(file_get_contents('php://input'), true);
$token = apache_request_headers()["Authorization"] ?? "";
$parentId = $data['parentDirectory'];
$name = $data['name'];
$con = new Database();
$con->connect();
$idUser = $con->checkToken($token);
if ($idUser) {
    $namePath = sha1($data['name'].$parentId.$idUser);
    $sql = "INSERT INTO directories (name,namePath,id_user,id_directory) VALUES (:name,:namePath,:idUser,:parentId)";
    $rows = $con->query($sql,["name"=>$name,"namePath"=>$namePath,"idUser"=>$idUser,"parentId"=>$parentId]);
    echo json_encode(['data' => ['id' => $rows]]);
    
}