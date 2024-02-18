<?php
header('Access-Control-Allow-Origin: *');
header('Content-type:  multipart/form-data');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept ,Access-Control-Request-Method, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
require($_SERVER['DOCUMENT_ROOT'] . "/api/classes/DatabaseImpl.php");
$file = $_FILES['file'];
//get the name of file without the extension
$name = pathinfo($file['name'], PATHINFO_FILENAME);
$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$idDirectory = $_POST['id'];
$response = new stdClass();
$token = apache_request_headers()["Authorization"] ?? "";
// generate a random name for the file
$fileName = md5(uniqid(rand(), true));
try {
    $con = new DatabaseImpl();
    if ($con->checkToken($token)) {
        //check if the name of the file already exists, but check if the name has a number between parenthesis at the end
        $regex = "^" . $name . "\\([0-9]+\\)$";
        $result = $con->prepareQuery(
            "select",
            "files",
            ["name"],
            [":id" => $idDirectory, ":regex" => $regex, ":extension" => $extension],
            ["where" => "id_directory = :id and name REGEXP :regex and extension = :extension"]
        );
        if (count($result) > 0) {
            $name = pathinfo($file['name'], PATHINFO_FILENAME) . "(" . (count($result) + 1) . ")";
        }
        //create an sql transaction
        $con->beginTransaction();
        $result = $con->insert("files", ["name" => $name, "extension" => $extension, "path" => $fileName, "id_directory" => $idDirectory]);
        if ($result) {
            //move the file to the server
            if (move_uploaded_file($file['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . "/api/filesData/" . $fileName . "." . $extension)) {
                $con->commit();
                $response->data = [
                    "status" => 200,
                    "message" => "File uploaded successfully",

                ];
            } else {
                $con->rollback();
                $response->error = "Error uploading the file";
            }
        }
    } else {
        $response->status = "error";
        $response->message = "Invalid token";

    }
} catch (Exception $e) {
    $con->rollback();
    $response->status = "error";
} finally {
    echo json_encode($response);
}

