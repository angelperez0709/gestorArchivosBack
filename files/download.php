<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With,Content-Description,Content-Disposition, Content-Type, Accept, Access-Control-Request-Method, Authorization,Access-Control-Allow-Methods");
header("Access-Control-Allow-Methods: POST,GET");
require($_SERVER['DOCUMENT_ROOT'] . "/api/classes/DatabaseImpl.php");
$con = new DatabaseImpl();
$token = apache_request_headers()["Authorization"] ?? "";
$response = new stdClass();
$data = json_decode(file_get_contents('php://input'), true);
$idFile = $data['idFile'];
$idUser = $con->checkToken($token);
if ($idUser) {

    $file = $con->prepareQuery(
        "select",
        "files",
        ["path, name,extension"],
        ["id" => $idFile],
        ["where" => "id = :id"]
    );
    $filePath = $file[0]['path'];
    $extension = $file[0]['extension'];
    $fileName = $file[0]['name'];
    $filePath = $_SERVER['DOCUMENT_ROOT'] . "/api/filesData/" . $filePath . "." . $extension;

    if (file_exists($filePath)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename='. $fileName . "." . $extension);
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));
        header("Access-Control-Expose-Headers: *");

        readfile($filePath);

        exit;
    } else {
        echo 'File not found.';
    }
} else {
    echo 'Invalid request.';
}
