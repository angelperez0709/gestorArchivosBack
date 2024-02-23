<?php
header('Access-Control-Allow-Origin: *');
header('Content-type: application/json');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization,Access-Control-Allow-Methods");
header("Access-Control-Allow-Methods: DELETE");
require($_SERVER['DOCUMENT_ROOT'] . "/api/classes/DatabaseImpl.php");
$token = apache_request_headers()["Authorization"] ?? "";
$response = new stdClass();
$data = json_decode(file_get_contents('php://input'), true);
$idFile = $data['idFile'];
$path = "";
$pathTemp = "";
$fileNameTemp = "";
try {
    $con = new DatabaseImpl();
    $idUser = $con->checkToken($token);
    //$con->beginTransaction();
    if ($idUser) {
        $file = $con->prepareQuery(
            "select",
            "files",
            ["path", "extension"],
            ["id" => $idFile],
            ["where" => "id = :id"]
        );
        if (count($file) < 1) {
            throw new Exception("Directory not found", 400);
        }

        $path = $_SERVER['DOCUMENT_ROOT'] . "/api/filesData/" . $file[0]['path'] . "." . $file[0]['extension'];

        if (!file_exists($path)) {
            throw new Exception("File not found", 400);
        }

        $pathTemp = $_SERVER['DOCUMENT_ROOT'] . "/api/temp/";

        // Copiar el archivo a la ruta temporal
        $fileNameTemp = uniqid() . '_' . $file[0]['path'] . "." . $file[0]['extension'];
        $pathTemp = $pathTemp . $fileNameTemp;

        if (!copy($path, $pathTemp)) {
            throw new Exception("Error copying the file", 400);
        }

        if (!unlink($path)) {
            throw new Exception("Error deleting the file", 400);
        }

        if(!unlink($pathTemp)){
            throw new Exception("Error deleting the file", 400);
        }

        $con->beginTransaction();
        $result = $con->delete("files", ["id" => $idFile]);
        if ($result == 0) {
            $con->rollback();
            throw new Exception("Error deleting the file", 400);
        }
        $con->commit();
        $response->data = [
            "status" => 200,
            "message" => "File deleted successfully"
        ];
    }
} catch (Exception $e) {
    if (!file_exists($path) && file_exists($pathTemp)) {
        rename($pathTemp, $path);
    }
    $response->data = [
        "status" => $e->getCode() ?? 500,
        "message" => $e->getMessage()
    ];
} finally {
    echo json_encode($response);
}