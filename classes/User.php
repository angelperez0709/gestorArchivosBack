<?php
class User
{
    private $username;
    private $token;

    public function __construct($username, $token)
    {
        $this->username = $username;
        $this->token = $token;
    }

    public function __toString()
    {
        return "Usuario: " . $this->username . ", Token: " . $this->token;
    }

}