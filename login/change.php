<?php
header('Access-Control-Allow-Origin: *');
header('Content-type: application/json');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method,Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
$method = $_SERVER['REQUEST_METHOD'];
$token = apache_request_headers()['Authorization'] ?? "";
require($_SERVER['DOCUMENT_ROOT'] . "/api/classes/DatabaseImpl.php");
$data = json_decode(file_get_contents('php://input'), true);
$response = new stdClass();
try {
    $con = new DatabaseImpl();
    $idUser = $con->checkToken($token);
    if (!$idUser) {
        throw new Exception("Invalid token", 401);
    }

    if ($data['newPassword'] != $data['repeatedPassword']) {
        throw new Exception("Passwords are not the same", 400);
    }

    if ($data['username'] != "") {

        $result = $con->prepareQuery(
            "select",
            "users",
            ["id"],
            [':username' => $data['username']],
            ["where" => "username = :username"]
        );
    }
    if ($result === false || count($result) > 0) {
        throw new Exception("User already exist", 404);
    }

    if ($data['newPassword'] == '') {
        //update only username
        $resultUpdate = $con->prepareQuery(
            "update",
            "users",
            ["username = :username"],
            [':username' => $data['username'], ':id' => $idUser],
            ["where" => "id = :id"],
        );
        if ($resultUpdate === 0 || $resultUpdate === false) {
            throw new Exception("An error was ocurred, please try again", 500);
        }
    } else {
        //update username and password
        $result = $con->prepareQuery(
            "update",
            "users",
            ["username = :username, password = :password"],
            [':username' => $data['username'], ':password' => password_hash($data['newPassword'], PASSWORD_DEFAULT), ':id' => $idUser],
            ["where" => "id = :id"],
        );
        if ($result === 0 || $result === false) {
            throw new Exception("An error was ocurred, please try again", 500);
        }
    }

    $response->ok = true;

} catch (Exception $e) {
    $response->ok = false;
    $response->error = $e->getMessage();
} finally {
    echo json_encode($response);
}
?>