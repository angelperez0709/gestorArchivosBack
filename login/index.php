<?php
header('Access-Control-Allow-Origin: *');
header('Content-type: application/json');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
$method = $_SERVER['REQUEST_METHOD'];
require($_SERVER['DOCUMENT_ROOT'] . "/api/classes/DatabaseImpl.php");
require($_SERVER['DOCUMENT_ROOT'] . "/api/classes/SessionManager.php");
$data = json_decode(file_get_contents('php://input'), true);
$response = new stdClass();
if ($data['username'] != '' && $data['password'] != '') {
    $con = new DatabaseImpl();
    $result = $con->prepareQuery(
        "select",
        "users",
        ["id,password"],
        [':username' => $data['username'], ":password" => $data["password"]],
        ["where" => "username = :username and password = :password"]
    );
    if ($result !== false && count($result) === 1 && $result[0]['password'] == $data['password']) {
        [$result,$token] = $con->updateToken($result[0]['id']);
        if ($result == 1) {
            $response->ok = true;
            $response->data = $token;
        }else{
            $response->ok = false;
        }
    } else {
        $response->ok = false;
    }

} else {
    $response->ok = false;
}
echo json_encode($response);
?>