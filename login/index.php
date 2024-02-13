<?php
header('Access-Control-Allow-Origin: *');
header('Content-type: application/json');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
$method = $_SERVER['REQUEST_METHOD'];
require($_SERVER['DOCUMENT_ROOT'] . "/api/classes/DatabaseImpl.php");
$data = json_decode(file_get_contents('php://input'), true);
$response = new stdClass();
$con = new DatabaseImpl();
if ($data['username'] != '' && $data['password'] != '') {
    session_start();
    $con = new DatabaseImpl();
    $sql = 'SELECT token, password FROM users WHERE username = :username';
    $result = $con->prepareQuery($sql, [':username' => $data['username']]);
    if ($result !== false && count($result) === 1  && $result[0]['password'] == $data['password']) {
        $response->ok = true;
        $response->data = $result[0]['token'];
        $_SESSION['token'] = $result[0]['token'];
    } else {
        $response->ok = false;
    }

} else {
    $response->ok = false;
}
echo json_encode($response);
?>