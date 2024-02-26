<?php
header('Access-Control-Allow-Origin: *');
header('Content-type: application/json');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
$method = $_SERVER['REQUEST_METHOD'];
require($_SERVER['DOCUMENT_ROOT'] . "/api/classes/DatabaseImpl.php");
$data = json_decode(file_get_contents('php://input'), true);
$response = new stdClass();
try {
    if ($data['username'] == '' || $data['password'] == '') {
        throw new Exception("User and password are required", 400);
    }

    $con = new DatabaseImpl();
    $result = $con->prepareQuery(
        "select",
        "users",
        ["id"],
        [':username' => $data['username']],
        ["where" => "username = :username"]
    );


    if ($result !== false && count($result) > 0) {
        throw new Exception("User already exists", 400);
    }

    $result = $con->insert("users", ["username" => $data['username'], "password" => password_hash($data['password'], PASSWORD_DEFAULT)]);

    if ($result == 0) {
        throw new Exception("Error creating the user, please try again", 400);
    }

    $response->data = [
        "status" => 200,
        "message" => "User created successfully"
    ];

} catch (Exception $e) {
    $response->data = [
        "status" => $e->getCode() ?? 500,
        "message" => $e->getMessage()
    ];
} finally {
    echo json_encode($response);
}