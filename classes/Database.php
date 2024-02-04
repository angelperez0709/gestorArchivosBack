<?php
class Database
{
    private $user;
    private $password;
    public $database;
    public function __construct()
    {
        try {
            $this->user = 'root';
            $this->password = '';
        } catch (Exception $e) {
            echo '' . $e->getMessage();
        }
    }
    public function connect()
    {
        try {
            $this->database = new PDO("mysql:dbname=managerfiles;host:localhost", $this->user, $this->password);
            $this->database->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER);
            $this->database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "" . $e->getMessage();
        }
    }

    public function disconnected()
    {
        $this->database = null;
    }

    public function query($sql,$params){
        $sql = $this->database->prepare($sql);
        if($sql->execute($params)){
            return $sql->fetchAll(PDO::FETCH_ASSOC);
        }else{
            return false;
        }
        

    }
    public function checkToken($token){
        $sql = 'SELECT id FROM users WHERE token = :token';
        $result = $this->query($sql, [':token' => $token]);
        if (count($result) == 1) {
            return $result[0]['id'];
        } else {
            return false;
        }
    }
}