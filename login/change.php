<?php
header('Access-Control-Allow-Origin: *');
header('Content-type: application/json');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method,Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
$method = $_SERVER['REQUEST_METHOD'];
require($_SERVER['DOCUMENT_ROOT'] . "/api/classes/DatabaseImpl.php");
$data = json_decode(file_get_contents('php://input'), true);
$token = $data["token"];
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

    $params = [":id" => $idUser];
    $setUpdate = "";
    if ($data['username'] != "") {

        $result = $con->prepareQuery(
            "select",
            "users",
            ["id"],
            [':username' => $data['username']],
            ["where" => "username = :username"]
        );
        if ($result === false || count($result) > 0) {
            throw new Exception("User already exist", 404);
        }
        $params[":username"] = $data['username'];
        $setUpdate = "username = :username";
    }

    if ($data['newPassword'] != '') {
        $params[":password"] = password_hash($data['newPassword'], PASSWORD_DEFAULT);
        $setUpdate .= ($setUpdate != "" ? ", " : "") . "password = :password";
    }

    //update only username
    $resultUpdate = $con->prepareQuery(
        "update",
        "users",
        [$setUpdate],
        $params,
        ["where" => "id = :id"],
    );
    if ($resultUpdate === 0 || $resultUpdate === false) {
        throw new Exception("An error was ocurred, please try again", 500);
    }

    $response->data = [
        "status" => 200,
        "message" => "User updated successfully"
    ];
} catch (Exception $e) {
    $response->data = [
        "status" => $e->getCode() ?? 500,
        "message" => $e->getMessage()
    ];
} finally {
    echo json_encode($response);
}
?>