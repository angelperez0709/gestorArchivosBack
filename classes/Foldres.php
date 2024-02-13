<?php
class Folder
{
    private $name;

    private $id;

    private $id_directory;

    public function __construct($name, $id)
    {
        $this->name = $name;
        $this->id = $id;
        $this->id_directory = $id_directory;
    }

    // Getters and Setters

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getIdDirectory()
    {
        return $this->id_directory;
    }

    public function setIdDirectory($id_directory)
    {
        $this->id_directory = $id_directory;
    }
}