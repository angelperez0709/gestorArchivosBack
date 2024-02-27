<?php
header('Access-Control-Allow-Origin: *');
header('Content-type: application/json');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
require($_SERVER['DOCUMENT_ROOT'] . "/api/classes/DatabaseImpl.php");
try {
    $response = new stdClass();

    $data = json_decode(file_get_contents('php://input'), true);
    $token = $data["token"];
    $namePath = $data['path'];
    $con = new DatabaseImpl();
    $idUser = $con->checkToken($token);
    if (!$idUser) {
        throw new Exception("Invalid token", 401);
    }
    $resultId = $con->prepareQuery(
        "select",
        "directories dir",
        ["dir.id"],
        ["namePath" => $namePath, "idUser" => $idUser],
        ["where" => "dir.namePath = :namePath AND dir.id_user = :idUser"]
    );
    if ($resultId === false || count($resultId) === 0) {
      throw new Exception("Directory not found", 400);
    }

    $idDirectory = $resultId[0]['id'];

    // get data from the child directories
    $result = $con->prepareQuery(
        "select",
        "directories dir",
        ["dir.name,dir.id,dir.namePath"],
        ["id" => $idDirectory, "idUser" => $idUser],
        ["where" => "dir.id_directory = :id AND dir.id_user = :idUser"]
    );

    // get the path of the parent directory
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

    // get the files from the directory
    $resultFiles = $con->prepareQuery(
        "select",
        "files fi",
        ["concat(fi.name,'.',fi.extension) name,fi.id"],
        ["id" => $idDirectory],
        ["where" => "fi.id_directory = :id", "join" => ["INNER JOIN" => "directories dir ON dir.id = fi.id_directory"]]
    );
    if ($resultFiles === false || count($resultFiles) === 0) {
        $resultFiles = [];
    }
    $path = $con->Preparequery("function", null, ["GetFullPath(:idDirectory) path"], ["idDirectory" => $idDirectory], [])[0]['path'];
    $response->data = ["status" => 200, "directories" => $result, "files" => $resultFiles, "prevPath" => $resultPath[0]['namePath'], "path" => $path, "id" => $idDirectory];

} catch (Exception $e) {
    $response->data = [
        "status" => $e->getCode() ?? 500,
        "message" => $e->getMessage()
    ];
} finally {
    echo json_encode($response);
}