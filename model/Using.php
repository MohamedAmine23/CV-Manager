<?php

require_once 'framework/Model.php';
require_once 'model/Experience.php';
require_once 'model/Skill.php';

class Using extends Model
{

    private $experience;
    private $skill;

    public function __construct($experience, $skill)
    {

        $this->experience = $experience;
        $this->skill = $skill;

    }

//---GETTERS---
    public function get_experience()
    {
        return $this->experience;
    }

    public function get_skill()
    {
        return $this->skill;
    }

    public static function get_all_used()
    {//return an array of all Using objects  from the table Using
        $query = self::execute("SELECT * FROM `using`", array());
        $rows = $query->fetchAll();
        $results = [];
        foreach ($rows as $row) {
            $results[] = new Using(Experience::get_experience_by_id($row['Experience']), Skill::get_skill_by_id($row['Skill']));
        }
        return $results;
    }

    public static function get_using($id_experience, $id_skill)
    {//return an Using object that have this id (experience and skill)
        $query = self::execute("SELECT * FROM `using` WHERE Experience = :experience and Skill = :skill", array("experience" => $id_experience, "skill" => $id_skill));
        $row = $query->fetch();
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return new Using(Experience::get_experience_by_id($row['Experience']), Skill::get_skill_by_id($row['Skill']));
        }
    }

    public static function get_using_experiences(Experience $experience)
    {//used to have all Using objects that contains this experiences
        $query = self::execute("SELECT * FROM `using` WHERE Experience = :experience", array("experience" => $experience->id));
        $rows = $query->fetchAll();
        $results = [];
        foreach ($rows as $row) {
            $results[] = new Using(Experience::get_experience_by_id($row['Experience']), Skill::get_skill_by_id($row['Skill']));
        }
        return $results;
    }


    public static function get_using_skills(Skill $skill)
    {//used to have all Using objects that contains this skill
        $query = self::execute("SELECT * FROM `using` WHERE Skill = :skill", array("skill" => $skill->id));
        $rows = $query->fetchAll();
        $results = [];
        foreach ($rows as $row) {
            $results[] = new Using(Experience::get_experience_by_id($row['Experience']), Skill::get_skill_by_id($row['Skill']));
        }
        return $results;
    }

//---INSERT---
    public function insert()
    {
        if (!self::get_using($this->experience, $this->skill)) {//insert into the database ,the datas from the current Using object
            self::execute("INSERT INTO `using`(Experience,Skill) VALUES(:experience,:skill)", array("experience" => $this->experience, "skill" => $this->skill));
        }
    }

//---DELETE---
    public static function delete_using_experiences(Experience $experience)
    {//delete all Using object that contains the experience of the current Using object
        self::execute("DELETE FROM `using` WHERE Experience = :experience", array("experience" => $experience->id));
    }

    public static function delete_using_skills(Skill $skill)
    {//delete all Using object that contains  the skill of the current Using object
        self::execute("DELETE FROM `using` WHERE Skill = :skill", array("skill" => $skill->id));
    }

    public function delete()
    {
        self::execute("DELETE FROM `using` WHERE Skill = :skill AND Experience = :experience", array("skill" => $this->skill->id, "experience" => $this->experience->id));
    }
}