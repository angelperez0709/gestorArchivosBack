<?php
header('Access-Control-Allow-Origin: *');
header('Content-type: application/json');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
require($_SERVER['DOCUMENT_ROOT'] . "/api/classes/DatabaseImpl.php");
$response = new stdClass();
$data = json_decode(file_get_contents('php://input'), true);
$token = $data["token"];
$idDirectory = $data['idDirectory'];
$files = [];
try {
    $con = new DatabaseImpl();
    $idUser = $con->checkToken($token);
    $con->beginTransaction();
    if (!$idUser) {
        throw new Exception("Invalid token", 401);
    }

    $files = $con->prepareQuery(
        "select",
        "files",
        ["id", "path", "extension"],
        ["id_directory" => $idDirectory],
        ["where" => "id_directory = :id_directory"]
    );
    $result = $con->delete("directories", ["id" => $idDirectory, "id_user" => $idUser]);
    if ($result == 0) {
        throw new Exception("Directory not found", 400);

    }


    foreach ($files as $file) {
        $path = $_SERVER['DOCUMENT_ROOT'] . "/api/filesData/" . $file['path'] . "." . $file['extension'];
        $fileNameTemp = uniqid() . '_' . $file['path'] . "." . $file['extension'];
        $pathTemp = $_SERVER['DOCUMENT_ROOT'] . "/api/temp/";
        $pathTemp = $pathTemp . $fileNameTemp;

        if (!file_exists($path)) {
            throw new Exception("File not found", 400);
        }

        if (!copy($path, $pathTemp)) {
            throw new Exception("Error copying the file", 400);
        }


        if (!unlink($path) && !unlink($pathTemp)) {
            throw new Exception("Error deleting the file", 400);
        }


    }
    $con->commit();
    $response->data = [
        "status" => 200,
        "message" => "Directory deleted successfully"
    ];

} catch (Exception $e) {

    foreach ($files as $file) {
        $path = $_SERVER['DOCUMENT_ROOT'] . "/api/filesData/" . $file['path'] . "." . $file['extension'];
        $fileNameTemp = uniqid() . '_' . $file[0]['path'] . "." . $file[0]['extension'];
        $pathTemp = $_SERVER['DOCUMENT_ROOT'] . "/api/temp/";
        $pathTemp = $pathTemp . $fileNameTemp;
        if (!file_exists($path) && file_exists($pathTemp)) {
            rename($pathTemp, $path);
        }

    }

    $con->rollback();
    $response->data = [
        "status" => $e->getCode(),
        "message" => $e->getMessage()
    ];
} finally {
    echo json_encode($response);
}