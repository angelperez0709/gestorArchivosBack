<?php
header('Access-Control-Allow-Origin: *');
header('Content-type: application/json');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
require($_SERVER['DOCUMENT_ROOT'] . "/api/classes/DatabaseImpl.php");
$data = json_decode(file_get_contents('php://input'), true);
$token = $data["token"];
$parentId = $data['parentDirectory'];
$name = $data['name'];
$response = new stdClass();
$databseDao = new DatabaseImpl();

$idUser = $databseDao->checkToken($token);
try {
    if ($idUser) {
        $namePath = sha1($data['name'] . $parentId . $idUser);
        $result = $databseDao->insert("directories", ["name" => $name, "namePath" => $namePath, "id_user" => $idUser, "id_directory" => $parentId]);
        if ($result) {
            $response->data = [
                "status" => 201,
                "id" => $result
            ];
        } else {
            $response->error = [
                "status" => 500,
                "message" => "Was not possible to create the directory"
            ];
        }
    } else {
        $response->error = [
            "status" => 401,
            "message" => "Invalid token"
        ];
    }
} catch (Exception $e) {
    $response->error = [
        "status" => 500,
        "message" => "Was not possible to create the directory"
    ];
} finally {
    echo json_encode($response);
}