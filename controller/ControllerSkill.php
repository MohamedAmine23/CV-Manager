<?php

require_once 'model/Skill.php';
require_once 'framework/View.php';
require_once 'framework/Controller.php';
require_once "model/User.php";

class ControllerSkill extends Controller
{


    public function index()
    {
        $this->manageSkills();
    }

    //--MANAGE SKILLS / ADD SKILL--
    public function manageSkills()
    {
        $user = $this->get_user_or_redirect();
        if ($user->role === "admin") {
            $errors = [];
            $name = '';
            if (isset($_POST['name']) && $_POST['name'] != "") {
                $name = trim($_POST['name']);
                $skill = new Skill($name);
                $errors = $this->add_skill($skill);
                if (empty($errors)) $name = '';
            }
            $skills = Skill::get_all_skills();
            (new View("manageSkills"))->show(array("user" => $user, "addName" => $name, "skills" => $skills, "errors" => $errors));
        } else {
            Tools::abort("action no permited : You must be admin");
        }
    }

    //ADD SKILL
    private function add_skill(Skill $skill)
    {
        $errors = [];
        $errors = $skill->validate_name();
        $errors = array_merge($errors, $skill->validate_unicity());
        if (empty($errors)) {
            $skill->insert();
        }
        return $errors;
    }

    //-- DELETE SKILL--
    public function delete()
    {
        $user = $this->get_user_or_redirect();
        if ($user->role === "admin") {
            if (isset($_POST['delete_id']) && $_POST['delete_id'] != "" && $skill = Skill::get_skill_by_id($_POST['delete_id'])) {
                $id = $_POST['delete_id'];
                $skill = Skill::get_skill_by_id($id);
                $result = $skill->delete();
                if ($result) {
                    $this->redirect("Skill");
                } else {
                    Tools::abort(" ERROR DELETING Wrong/missing param or action no permited");
                }
            } else {
                Tools::abort("Wrong/missing param or action no permited");
            }
        } else {
            Tools::abort("action no permited : You must be admin ");
        }
    }

    public function request_delete()
    {
        $user = $this->get_user_or_redirect();
        if ($user->role === "admin") {
            if (isset($_GET['param1']) && $_GET['param1'] != '' && $skill = Skill::get_skill_by_id($_GET['param1'])) {
                $skill = Skill::get_skill_by_id($_GET['param1']);
                $delete_class = "Skill";
                $delete_element = "$skill->name";
                (new View("delete_confirm"))->show(array("user" => $user, "delete_class" => $delete_class, "delete_element" => $delete_element, "field" => $skill));
            } else {
                Tools::abort("Wrong/missing param or action no permited");
            }
        } else {
            Tools::abort("action no permited : You must be admin");
        }
    }

    //--EDIT SKILL--
    public function edit()
    {
        $user = $this->get_user_or_redirect();
        $errors = [];
        $editName = "";
        if ($user->role === "admin") {
            if (isset($_POST['edit_id']) && $_POST['edit_id'] != "" && $oldskill = Skill::get_skill_by_id($_POST['edit_id'])) {
                $id = $_POST['edit_id'];
                $oldskill = Skill::get_skill_by_id($id);
                if (isset($_POST['edit_name']) && $_POST['edit_name'] != "") {
                    $editName = trim($_POST['edit_name']);
                    $oldskill->name = $editName;
                    $errors = $oldskill->validate_name();
                    $errors = array_merge($errors, $oldskill->validate_unicity());
                    if (empty($errors)) {
                        $oldskill->update();
                        $this->redirect("skill");
                    } else {
                        $skills = Skill::get_all_skills();
                        (new View("manageSkills"))->show(array("user" => $user, "skills" => $skills, "name" => $editName, "edit_id" => $id, "addName" => "", "errors" => $errors));
                    }
                }
            } else {
                Tools::abort("Wrong/missing param or action no permited");
            }
        } else {
            Tools::abort("action no permited : You must be admin");
        }
    }
}
