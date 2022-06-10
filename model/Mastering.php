<?php

require_once "framework/Model.php";
require_once "model/User.php";
require_once "model/Skill.php";

class Mastering extends Model {

    private $user;
    private $skill;
    public $level;

    public function __construct($user, $skill, $level)
    {
        $this->user=$user;
        $this->skill=$skill;
        $this->level=$level;
    }
//---GETTERS---
    public function get_user(){
        return $this->user;
    }
    public function get_skill(){
        return $this->skill;
    }
    public function get_all_mastering(){//return all mastering objects
        $query = self::execute("SELECT * FROM Mastering WHERE User = :user", array("user"=>$this->get_user()->id));
        $data = $query->fetchAll();
        $res = [];
        foreach($data as $row){
            $res[] = new Mastering($row['User'], $row['Skill'], $row['Level']);
        }
        return $res;
    }
    public static function get_mastering(User $user, Skill $skill){//return a mastering object 
        $query = self::execute("SELECT * FROM Mastering WHERE User = :user AND Skill = :skill",
                                array("user"=>$user->id, "skill"=>$skill->id));
        $data = $query->fetch();
        if ($query->rowCount() == 0){
            return false;
        }
        else {
            return new Mastering(User::get_user_by_id($data['User']),Skill::get_skill_by_id($data['Skill']) , $data['Level']);
        }
    }
    public static function get_mastering_skills(Skill $skill): array {//return an array of mastering objects contains  this skill
        $query=self::execute("SELECT * FROM Mastering WHERE Skill =:skill",array("skill"=>$skill->id));
        $rows=$query->fetchAll();
        $results=array();
        foreach($rows as $data){
            $results[]=new Mastering(User::get_user_by_id($data['User']),Skill::get_skill_by_id($data['Skill']) , $data['Level']);
        }
        return $results;
    }
    public static function get_mastering_users(User $user) : array  {//return an array of mastering objects contains this user 
        $query=self::execute("SELECT * FROM Mastering WHERE User =:user",array("user"=>$user->id));
        $rows=$query->fetchAll();
        $results=array();
        foreach($rows as $data){
            $results[]=new Mastering(User::get_user_by_id($data['User']),Skill::get_skill_by_id($data['Skill']) , $data['Level']);
        }
        return $results;
    }
    
//---VALIDATIONS---
    public function validate_level(){
        $errors = [];
        if(!(is_int($this->level) && $this->level <= 5 && $this->level >= 1)){
            $errors[] = "Level must be between 1 and 5";
        }
        return $errors;
    }
//---INSERT---
    public function insert(){
        self::execute("INSERT INTO Mastering (User, Skill, Level) VALUES (:user, :skill, :level)", 
                    array("user"=>$this->get_user()->id, "skill"=>$this->get_skill()->id,"level"=>$this->level));
    }
//---DELETE---
    public  static function delete_mastering_skill(Skill $skill){
         self::execute("DELETE FROM Mastering  WHERE Skill = :skill", 
                            array( "skill"=>$skill->id));
    }
    public static function delete_mastering_user(User $user){
         self::execute("DELETE FROM Mastering  WHERE User = :user", 
                            array( "user"=>$user->id));
    }
    public function delete(){
        if(self::get_mastering($this->get_user(),$this->get_skill())){
            self::execute('DELETE  FROM Mastering  WHERE User = :user AND Skill = :skill', array("user"=>$this->get_user()->id, "skill"=>$this->get_skill()->id));
            return $this;
        }
        return false;
    }
//---UPDATE---

    public function update(){
        if (self::get_mastering($this->get_user(),$this->get_skill())){
            self::execute("UPDATE Mastering SET Level=:level WHERE User = :user AND Skill = :skill",
                        array("user"=>$this->get_user()->id,"level"=>$this->level ,"skill"=>$this->get_skill()->id));
        }
        else {
            self::execute("INSERT INTO Mastering (User, Skill, Level) VALUES (:user, :skill, :level)",
                        array("user"=>$this->get_user()->id, "skill"=>$this->skill, "level"=>$this->level));
        }
    }

    public function level_up(){
        self::execute("UPDATE Mastering SET Level = Level + 1 WHERE User = :user AND Skill = :skill",
                    array("user"=>$this->get_user()->id, "skill"=>$this->get_skill()->id));
    }
    public function level_down(){
        self::execute("UPDATE Mastering SET Level = Level - 1 WHERE User = :user AND Skill = :skill",
        array("user"=>$this->get_user()->id, "skill"=>$this->get_skill()->id));
    }
    

}