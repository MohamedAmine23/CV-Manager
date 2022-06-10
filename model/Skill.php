<?php 

require_once "framework/Model.php";
require_once "model/Mastering.php";
require_once "model/Using.php";
require_once "model/Experience.php";

class Skill extends Model {

    public $id;
    public $name;

    public function __construct($name,$id=NULL){
        $this->name=$name;
        $this->id=$id;
    }
//---GETTERS---
    public static function get_all_skills(){
        $query = self::execute("SELECT * FROM Skill ORDER BY ID", array());
        $data = $query->fetchAll();
        $res = [];
        foreach($data as $row){
            $res[] = new Skill($row['Name'], $row['ID']);
        }
        return $res;
    }
     
    public static function get_skill_by_id($id){
        $query = self::execute("SELECT * FROM Skill WHERE ID = :id", array("id"=>$id));
        $data = $query->fetch(); 
        if ($query->rowCount() == 0){
            return false;
        }
        else {
            return new Skill($data["Name"],$data["ID"]);
        }
    }
    
    public function get_masterings_of_skill(){// return all mastering objects of the current skill
        $query = self::execute("SELECT * FROM Mastering WHERE Skill = :skill", array("skill"=>$this->id));
        $rows = $query->fetchAll();
        $res = [];
        foreach($rows as $row){
            $res [] = new Mastering(User::get_user_by_id($row['User']), Skill::get_skill_by_id($row['Skill']), $row['Level']);
        }
        return $res;
    }

    public function count_mastered_by(){//return the number of all mastering objects of the current object
        return count($this->get_masterings_of_skill());
    }

    public function get_skills_in_experiences(){ // return all using objects of the current skill
        $query = self::execute("SELECT * FROM `using` WHERE Skill = :skill", array("skill"=>$this->id));
        $rows = $query->fetchAll();
        $res = [];
        foreach($rows as $row){
            $res [] = new Using(Experience::get_experience_by_id($row['Experience']), Skill::get_skill_by_id($row['Skill']));
        }
        return $res;
    }

    public function count_skills_in_exp(){//return the number of all using objects of the current skill
        return count($this->get_skills_in_experiences());
    }
//---VALIDATIONS---
    public function validate_name(){
        $errors =[];

        if(!(isset($this->name)&& is_string($this->name) && strlen($this->name) >= 1 )){

            $errors[] = "Skill name must contain at least one character";
        }
        return $errors;
    }

    public function validate_unicity(){
        $errors = [];
        $query = self::execute("SELECT * FROM Skill WHERE Name=:name", array("name"=>$this->name));
        if($query->rowCount() != 0){
            $errors[]= "This skill already exists";
        }
        return $errors;
    }
//---INSERT---
    public function insert(){
        self::execute("INSERT INTO `Skill` (Name) VALUES (:name)", array("name"=>$this->name));
    }
//---DELETE---    
    public function delete(){//delete the current skill (cascading delete)
        if (self::get_skill_by_id($this->id)) {
            //delete all skills in table using
            Using::delete_using_skills($this);
            //delete all skills in table mastering
            Mastering::delete_mastering_skill($this);
            self::execute("DELETE FROM Skill WHERE ID=:id", array("id"=>$this->id));
            return $this;
        }
        return false;
    }
//---UPDATE---
    public function update(){
        if (self::get_skill_by_id($this->id)){
            self::execute("UPDATE `Skill` SET Name=:name WHERE ID=:id", array("name"=>$this->name, "id"=>$this->id));
        }
        else {
            self::execute("INSERT INTO Skill(Name) VALUES(:name)", array("name"=>$this->name));
        }
    }

    

    
}


?>