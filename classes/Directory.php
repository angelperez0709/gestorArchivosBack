
<?
class Directory {
    private $id;
    private $name;
    private $namePath;
    private $id_user;
    private $id_directory;

    public function __construct($id, $name, $namePath, $id_user, $id_directory) {
        $this->id = $id;
        $this->name = $name;
        $this->namePath = $namePath;
        $this->id_user = $id_user;
        $this->id_directory = $id_directory;
    }
    // Getters
    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getNamePath() {
        return $this->namePath;
    }

    public function getUserId() {
        return $this->id_user;
    }

    public function getDirectoryId() {
        return $this->id_directory;
    }

    // Setters
    public function setId($id) {
        $this->id = $id;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function setNamePath($namePath) {
        $this->namePath = $namePath;
    }

    public function setUserId($id_user) {
        $this->id_user = $id_user;
    }

    public function setDirectoryId($id_directory) {
        $this->id_directory = $id_directory;
    }
}