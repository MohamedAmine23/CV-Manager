<?php

require_once "framework/Model.php";
require_once "model/Experience.php";
require_once "model/Place.php";
require_once "model/Using.php";
require_once "model/Skill.php";
require_once "model/Mastering.php";

class User extends Model{

    
    public $mail;
    public $fullName;
    public $title;
    public $password;
    public $birthdate;
    public $role;
    public $id;
    public $registeredAt;
   
    

    public function __construct($mail,$fullName,$title,$password,$birthdate,$role=null,$id=null,$registeredAt=null)
    {
        $this->mail=$mail;
        $this->fullName=$fullName;
        $this->title=$title;
        $this->password=$password;
        $this->birthdate=$birthdate;
        $this->role=$role;
        $this->id=$id;
        $this->registeredAt=$registeredAt;
   
    }
//---GETTERS---
   
    public static function get_users() {
        $query = self::execute("SELECT * FROM User", array());
        $data = $query->fetchAll();
        $results = [];
        foreach ($data as $row) {
            $results[] = new User($row["Mail"], $row["FullName"], $row["Title"], $row["Password"],$row["Birthdate"],$row["Role"],$row["ID"],$row["RegisteredAt"]);
        }
        return $results;
    }
    public static function get_user_by_id($id){
        $query = self::execute("SELECT * FROM User where ID = :id", array("id" => $id));
        $row = $query->fetch(); 
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return new User($row["Mail"], $row["FullName"], $row["Title"], $row["Password"],$row["Birthdate"],$row["Role"],$row["ID"],$row["RegisteredAt"]);
        }
    }
    public static function get_user_by_mail($mail){
        $query = self::execute("SELECT * FROM User where Mail = :mail", array("mail" => $mail));
        $row = $query->fetch(); 
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return new User($row["Mail"], $row["FullName"], $row["Title"], $row["Password"],$row["Birthdate"],$row["Role"],$row["ID"],$row["RegisteredAt"]);
        }
    }

        
        
    

//---VALIDATIONS---

    public function validate_mail(){
        
        $errors = array();
        if (!(isset($this->mail) && is_string($this->mail) && strlen($this->mail) > 0)) {
            $errors[] = "Mail is required.";
        } elseif (!(isset($this->mail) && (filter_var($this->mail, FILTER_VALIDATE_EMAIL)))) {
            $errors[]= "Invalid email format.";
        }elseif(self::get_user_by_mail($this->mail)){
            $errors[] = "user with this mail already exists.";
        }   
        
        return $errors;
    }

    public function validate_fullName(){
        $errors = array();
        if (!(isset($this->fullName) && is_string($this->fullName) && strlen($this->fullName) > 0)) {
            $errors[] = "Name is required.";
        } elseif (!(isset($this->fullName) && is_string($this->fullName) && strlen($this->fullName) >= 3 && strlen($this->fullName) <= 40)) {
            $errors[] = "Name length must be between 3 and 16.";
        }
        return $errors;
    }

    public function validate_title(){
        $errors = array();
        if (!(isset($this->title) && is_string($this->title) && strlen($this->title) > 0)) {
        $errors[] = "Title is required.";
        } elseif (!(isset($this->title) && is_string($this->title) && strlen($this->title) >= 3 && strlen($this->title) <= 40)) {
            $errors[] = "Title length must be between 3 and 40.";
        }
        return $errors;
    }
    public function validate_role(){
        $errors = array();
        if (!(isset($this->role) && is_string($this->role) && in_array($this->role,["admin","user"]))) {
        $errors[] = "this role doesn't exist /not valid ";
        }
    return $errors;
    }
    
    public function validate_birthdate(){
        $errors = [];
         $age = self::convert_date_to_year($this->birthdate);
         if($age<18){
             $errors[] = "you must be 18 years or older";
         }
        return $errors;
    }

    private static function convert_date_to_year($date){
    
        $today = date("Y-m-d");
        
        $diff = date_diff(date_create($today),date_create($date));
        if($diff){
            $year=intval($diff->format('%y'));
            return $year;
        }    
    }
    
    
    
    public static function validate_passwords($password, $password_confirm){
        $errors = self::validate_password($password);
        if ($password != $password_confirm) {
            $errors[] = "You have to enter twice the same password.";
           
        }
        return $errors;
    }

    private static function validate_password($password){
        $errors = [];
        if (strlen($password) < 8 || strlen($password) > 16) {
            $errors[] = "Password length must be between 8 and 16.";
        } if (!((preg_match("/[A-Z]/", $password)) && preg_match("/\d/", $password) && preg_match("/['\";:,.\/?\\-]/", $password))) {
            $errors[] = "Password must contain one uppercase letter, one number and one punctuation mark.";
        }
        return $errors;
    }

//---LOGIN---
    //Validations
        public static function validate_login($mail, $password) {
            $errors = [];
            $user = User::get_user_by_mail($mail);
            if ($user) {
                if (!self::check_password($password, $user->password)) {
                    $errors[] = "Wrong password. Please try again.";
                }
            } else {
                $errors[] = "Can't find a user with this email '$mail'. Please sign up.";
            }
            return $errors;
        }

        private static function check_password($clear_password, $hash) {
            return $hash === Tools::my_hash($clear_password);
        }

    

//---INSERT/UPDATE---
    public function update() {// update or insert the current user (without role,id,registeredAt)
        if(self::get_user_by_mail($this->mail))
            self::execute("UPDATE User SET Password=:password, FullName=:fullName,Birthdate=:birthdate, Title=:title, WHERE  Mail=:mail ", 
                          array("fullName"=>$this->fullName,"birthdate"=>$this->birthdate, "title"=>$this->title, "mail"=>$this->mail, "password"=>$this->password));
                          else
            self::execute("INSERT INTO User(Mail,Password,FullName,Title,Birthdate) VALUES(:mail,:password,:fullName,:title,:birthdate)", 
                          array("mail"=>$this->mail,"birthdate"=>$this->birthdate, "password"=>$this->password, "title"=>$this->title, "fullName"=>$this->fullName));
        return $this;
    
    }
    public function updatePassword(){// update the password only
        if(self::get_user_by_id($this->id))
            self::execute("UPDATE user SET Password=:password WHERE  ID=:id ", 
                          array("password"=>$this->password,"id"=>$this->id));
                    
        return $this;
    }
    public function updateMember() {//update the current user(without password,registeredAt,id)
        if(self::get_user_by_id($this->id))
            self::execute("UPDATE user SET Mail=:mail, FullName=:fullName,Birthdate=:birthdate, Title=:title, Role=:role  WHERE  ID=:id ", 
                          array("mail"=>$this->mail,"fullName"=>$this->fullName,"birthdate"=>$this->birthdate, "title"=>$this->title, "role"=>$this->role, "id"=>$this->id));
                    
        return $this;
    
    }
//---MANAGING---
    //Place
        public function get_places(){// return all places of the current user (must be admin)
            if($this->role==="admin"){
                return Place::get_places();
            }else{
                Tools::abort("action no permited:You have to be admin !");
            }
        }
    //Experience
        //CRUDL
        public function add_experience( Experience $experience){
            if($this->id===$experience->get_user()->id || $this->role==="admin"){
                return $experience->insert();
            }
            else{
                Tools::abort("action add experience not permited:You have to be admin !");
            }
        }
        public function update_experience( Experience $experience){
            if($this->id===$experience->get_user()->id || $this->role==="admin"){
                return $experience->update();;
            }
            else{
                Tools::abort("action update experience not  permited:You have to be admin !");
            }
        }
        public function delete_experience( Experience $experience){
            if($this->id===$experience->get_user()->id || $this->role==="admin"){
                return $experience->delete();
            }
            else{
                Tools::abort("action delete experience not permited:You have to be admin !");
            }
        }
    public  function get_my_experiences(){//return an array of experience objects
        $query = self::execute("SELECT * FROM experience WHERE User=:user ORDER BY Stop,Start DESC", array("user"=>$this->id));
        $data = $query->fetchAll();
        $results = [];
        foreach ($data as $row) {
            $results[] = new Experience($row['Start'],$row['Title'],User::get_user_by_id($row['User']),Place::get_place_by_id($row['Place']),$row['Stop'],$row['Description'],$row['ID']);
        }
        return $results;
    }
    public function nb_experiences(){//return the number of experiences (of the current user )
        return count($this->get_my_experiences());
    }

    public  function get_experiences_filtered($start, $end){//return an array of experience objects between start and end date
        $query = self::execute("SELECT * FROM experience 
                WHERE User=:user AND YEAR(Start)>=:start AND (Stop IS NULL OR YEAR(Stop)<=:stop) 
                ORDER BY Stop,Start DESC",
            array("user"=>$this->id, "start"=>$start, "stop"=>$end));
        $data = $query->fetchAll();
        $results = [];
        foreach ($data as $row) {
            $results[] = new Experience($row['Start'],$row['Title'],User::get_user_by_id($row['User']),Place::get_place_by_id($row['Place']),$row['Stop'],$row['Description'],$row['ID']);
        }
        return $results;
    }

    //Using
        public function add_skill_of_experience(Using $used){
            $experience=Experience::get_experience_by_id($used->get_experience());
            if($this->id===$experience->get_user()->id || $this->role==="admin"){
                return  $used->insert();
            }
            else{
                Tools::abort("action no permited:You have to be admin !");
            }

        }
        public function delete_skill_of_experience(Using $used){
            $experience=Experience::get_experience_by_id($used->get_experience()->id);
            if($this->id===$experience->get_user()->id || $this->role==="admin"){
                return  $used->delete();
            }
            else{
                Tools::abort("action no permited:You have to be admin !");
            }

        }
    //Mastering
        //CRUDL
            public function add_mastering( Mastering $mastering){
                if($this->id===$mastering->get_user()->id || $this->role==="admin"){
                    return $mastering->insert();
                }
                else{
                    Tools::abort("action add mastering not permited:You have to be admin !");
                }
            }
            public function update_mastering_level_up( Mastering $mastering){
                if($this->id===$mastering->get_user()->id || $this->role==="admin"){
                    return $mastering->level_up();
                }
                else{
                    Tools::abort("action update mastering not  permited:You have to be admin !");
                }
            }
            public function update_mastering_level_down( Mastering $mastering){
                if($this->id===$mastering->get_user()->id || $this->role==="admin"){
                    return $mastering->level_down();
                }
                else{
                    Tools::abort("action update  mastering not  permited:You have to be admin !");
                }
            }
            public function update_mastering(Mastering $mastering){
                if($this->id===$mastering->get_user()->id || $this->role==="admin"){
                    return $mastering->update();
                }
                else{
                    Tools::abort("action update  mastering not  permited:You have to be admin !");
                }
            }
            public function delete_mastering( Mastering $mastering){
                if($this->id===$mastering->get_user()->id || $this->role==="admin"){
                    return $mastering->delete();
                }
                else{
                    Tools::abort("action delete mastering not permited:You have to be admin !");
                }
            }
            public function get_my_skills_mastered(){//return an array of mastering objects that the current user masters
                return Mastering::get_mastering_users($this);
            }
            public function nb_skills_mastered(){
                return count($this->get_my_skills_mastered());
            }
            public function get_my_skills_not_mastered(){//return an array of skill objects that the current user doesn't master
                $masterings=[];
                $skills=[];
                $skills=Skill::get_all_skills();
                $masterings=$this->get_my_skills_mastered() ;
                foreach($masterings as $mastering){
                    if(in_array($mastering->get_skill(),$skills)){
                        unset($skills[array_search($mastering->get_skill(),$skills)]);
                    }
                        
                }
                return $skills;
            }
            public function get_mastering(Skill $skill){
                return Mastering::get_mastering($this,$skill);

            }
        //--JSON--
            public function get_skills_mastered_as_json(){//convert a mastering object to json
                $masterings=$this->get_my_skills_mastered(); 
                $str = "";
                foreach($masterings as $mastering){
                    $id=json_encode($mastering->get_skill()->id);
                    $skill_name=json_encode($mastering->get_skill()->name);
                    $level= json_encode($mastering->level);
                    $str .= "{\"name\":$skill_name,\"level\":$level,\"id\":$id},";
                }

                if($str !== "")
                    $str = substr($str,0,strlen($str)-1);
                return "[$str]";
            }

    
        
//---DELETE---   
    public  function delete() {//delete the current user (cascading delete)
        if(self::get_user_by_id($this->id)){
            Experience::delete_experiences_of_user($this);
            Mastering::delete_mastering_user($this);        
            self::execute('DELETE FROM User WHERE ID = :id', array('id' => $this->id));
            return $this;    
        }
        return false;
    }

}