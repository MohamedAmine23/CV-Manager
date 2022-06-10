<?php
require_once "framework/Model.php";
require_once "model/Experience.php";
class Place extends Model{
    
    public $id;
    public $name;
    public $city;

    public function __construct($name,$city,$id=null)
    {
        $this->name=$name;
        $this->city=$city;
        $this->id=$id;

    }
    
//---GETTERS---
    public static function get_places(){
        $query = self::execute("SELECT * FROM Place ORDER BY City,Name", array());
        $data = $query->fetchAll();
        $results = [];
        foreach ($data as $row) {
            $results[] = new Place($row["Name"], $row["City"], $row["ID"]);
        }
        return $results;
    }
    public static function get_cities(){
        $query = self::execute("SELECT DISTINCT City FROM Place ORDER BY City", array());
        $data = $query->fetchAll();
        $results = [];
        foreach ($data as $row) {
            $results[] =  $row["City"];
        }
        return $results;
    }
    public static function get_place_by_id($id){
        $query = self::execute("SELECT * FROM Place WHERE ID = :id", array("id" => $id));
        $row = $query->fetch(); 
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return new Place($row["Name"], $row["City"], $row["ID"]);
        }
    }
    public function nb_experiences_used_place(){// return the number experiences of the current place 
        return count($this->get_experiences_of_place());
    }
//---UPDATE/INSERT---
    public function update() {
        if(self::get_place_by_id($this->id))
            self::execute("UPDATE place SET Name=:name,City=:city WHERE ID=:id",array("name"=>$this->name,"city"=>$this->city, "id"=>$this->id));
        else
            self::execute("INSERT INTO Place(Name,City) VALUES(:name,:city)", 
                        array("name"=>$this->name,"city"=>$this->city));
        return $this;
    }
//---DELETE---
    public  function delete() { //delete the current place (cascading delete)
        if (self::get_place_by_id($this->id)) {
            Experience::delete_experiences_of_place($this);
            self::execute('DELETE FROM Place WHERE ID = :id', array('id' => $this->id));
            return $this;
        }
        return false;
    }
 
//---VALIDATIONS---
    public function validate(){
        $errors = array();
        if (!(isset($this->name) && is_string($this->name) && strlen($this->name) >= 3)) {
            $errors[] = "Name length must be greather than 3 ";
        } if (!(isset($this->city) && is_string($this->city) && strlen($this->city) >= 3)) {
            $errors[] = "City length must be greather than 3 ";
        }
        return $errors;
    }
    public  function validate_unicity(){
        $errors = [];
        $query = self::execute("SELECT * FROM Place WHERE Name = :name and City = :city ", array("name" => $this->name,"city"=>$this->city));
        $row = $query->fetch();
        if ($query->rowCount() != 0) {
            $place = new Place($row["Name"], $row["City"], $row["ID"]);
            $errors[] = "This place in this City already exists.";
        }
        return $errors;
    }
//---PRIVATE TOOLS---
    private function get_experiences_of_place(){ //return all experiences with the current place
        $query= self::execute("SELECT * FROM Experience where Place=:place",array("place" => $this->id));
        $rows=$query->fetchAll();
        $results = [];
        foreach($rows as $row){
            $results[]= new Experience($row['Start'],$row['Title'],User::get_user_by_id($row['User']),Place::get_place_by_id($row['Place']),$row['Stop'],$row['Description'],$row['ID']);
        }
        return $results;
    }
    
}