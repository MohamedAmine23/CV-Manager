<?php
require_once "framework/Model.php";
require_once "model/Using.php";
require_once "model/User.php";
require_once "model/Place.php";
class Experience extends Model{

    public  $id;
    public  $start;
    public  $stop;
    public  $title;
    public  $description;
    private  $user;
    private  $place;
    

    

    public function __construct($start,$title,$user,$place,$stop=null,$description=null,$id=null){
        $this->start=$start;
        $this->stop=$stop;
        $this->title=$title;
        $this->description=$description;
        $this->user=$user;
        $this->set_place($place);
        $this->id=$id;
        
    }
//---GETTERS---
    public function get_user(){
        return $this->user;
    }
    public function get_place(){
        return $this->place;
    }
    
    public static function get_experience_by_id($id){
        $query = self::execute("SELECT * FROM experience WHERE ID = :id", array("id" => $id));
        $row = $query->fetch(); 
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return new Experience($row['Start'],$row['Title'],User::get_user_by_id($row['User']),Place::get_place_by_id($row['Place']),$row['Stop'],$row['Description'],$row['ID']);
        }
    }
    public static function get_experiences(){//return all experience objects
        $query = self::execute("SELECT * FROM experience ORDER BY Stop,Start", array());
        $data = $query->fetchAll();
        $results = [];
        foreach ($data as $row) {
            $results[] = new Experience($row['Start'],$row['Title'],User::get_user_by_id($row['User']),Place::get_place_by_id($row['Place']),$row['Stop'],$row['Description'],$row['ID']);
        }
        return $results;
    }
    
    public function set_place(Place $place ){
        $this->place=$place;
    }
    
//---DELETE---
    public function delete(){//delete the current experience (cascading delete)
        if(self::get_experience_by_id($this->id)){
            Using::delete_using_experiences($this);
            self::execute('DELETE FROM Experience WHERE ID = :id', array('id' => $this->id));
            return $this;
        }    
        return false;
    }
    public  static function delete_experiences_of_place(Place $place){
        self::execute('DELETE FROM `using` WHERE Experience IN (SELECT ID FROM Experience WHERE Place = :place)', array('place' => $place->id));
        self::execute('DELETE FROM Experience WHERE Place = :place', array('place' => $place->id));
    }
    public static function delete_experiences_of_user(User $user){
        self::execute('DELETE FROM `using` WHERE Experience IN (SELECT ID FROM Experience WHERE User=:user)',array("user"=>$user->id));

        self::execute('DELETE FROM Experience WHERE User =:user',array("user"=>$user->id));
    }
    
//---VALIDATIONS---

    public function validate_title(){
        $errors = array();
        if (!(isset($this->title) && is_string($this->title) && strlen($this->title) >= 3)) {
            $errors[] = "Title length must be greather than 3 ";
        } 
        return $errors;
    }
    public function validate_description(){
        $errors = array();
        if (!(isset($this->description) && is_string($this->description) && strlen($this->description) >= 10)) {
            $errors[] = "description length must be greather than 10 ";
        } 
        return $errors;
    }
    public function validate_start(){
        $errors=[];
        $errors=array_merge(self::validate_date($this->start));
        $birthdate_time=strtotime($this->get_user()->birthdate);
        $start_time=strtotime($this->start);
        if($birthdate_time>$start_time){
            $errors[]="this start has to be greather than ".$this->get_user()->fullName."'s birthdate(".$this->get_user()->birthdate.").";
        }
        return $errors;
    }
    public function validate_stop()
    {
        $errors=array();
        $errors=array_merge(self::validate_date($this->stop));
        $start_time=strtotime($this->start);
        $stop_time=strtotime($this->stop);
        if($start_time>$stop_time){
            $errors[]="this stop has to be greather than the start(".$this->start.")";
        }
        
        return $errors;
    
    }
    private static function validate_date($date)
    {
        $errors=array();
        $today = date("Y-m-d");
        $today_time=strtotime($today);
        $date_time=strtotime($date);
        if($today_time<$date_time){
            $errors[]="this date($date) can't be greather than today($today)";
        }
        return $errors;
    }
//---UPDATE---
    public function update() {
        if(self::get_experience_by_id($this->id))
            self::execute("UPDATE Experience SET Start=:start,Stop=:stop,Title=:title,Description=:description,Place=:place WHERE ID=:id",
            array("start"=>$this->start,
            "stop"=>$this->stop,
            "title"=>$this->title,
            "description"=>$this->description,
            "place"=>$this->get_place()->id,
            "id"=>$this->id));
        return $this;
    }
//---INSERT---
    public function insert() {
        if(!self::get_experience_by_id($this->id))
            self::execute("INSERT INTO Experience(Start,Stop,Title,Description,User,Place) VALUES(:start,:stop,:title,:description,:user,:place)", 
            array("start"=>$this->start,
            "stop"=>$this->stop,
            "title"=>$this->title,
            "description"=>$this->description,
            "user"=>$this->get_user()->id,
            "place"=>$this->get_place()->id));

            $this->id= self::lastInsertId();
              
        return $this;
    }

}