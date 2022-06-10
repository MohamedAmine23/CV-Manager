<?php
require_once "framework/Controller.php";
require_once "framework/Configuration.php";
require_once "framework/Tools.php";
require_once "model/Skill.php";
require_once "model/Mastering.php";
require_once "model/User.php";

class ControllerMastering extends Controller
{
    public function index()
    {

        $this->skills_mastered();
    }

    //--LIST MASTERINGS--
    public function skills_mastered()
    {
        $user = $this->get_user_or_redirect();
        if (isset($_GET['param1']) && $_GET['param1'] != '') //if we don't have param ->the current user page
        {
            if ((!$member = User::get_user_by_id($_GET['param1']))) {
                Tools::abort("skills_mastered: Wrong/missing param or action no permited");
            } else {
                $member = User::get_user_by_id($_GET['param1']);
                if ($user->role !== "admin" && $member->id !== $user->id) {
                    Tools::abort("skills_mastered no permited: You must be admin !");
                }
            }
        } else {
            $member = User::get_user_by_id($user->id);
        }
        $masterings = [];
        $skills = [];
        $skills = $member->get_my_skills_not_mastered();
        $not_mastered = json_encode($skills);
        $masterings = $member->get_my_skills_mastered();
        $masterings_json = $member->get_skills_mastered_as_json();
        (new View("Skills"))->show(array("user" => $user, "member" => $member, "masterings" => $masterings, "masterings_json" => $masterings_json, "skills" => $skills, "not_mastered" => $not_mastered, "errors" => []));
    }

    //--ADD MASTERING--
    public function add()
    {
        $masterings = [];
        $skills = [];
        $errors = array();
        $user = $this->get_user_or_redirect();
        if (isset($_GET['param1']) && $_GET['param1'] != '' && $member = User::get_user_by_id($_GET['param1'])) {
            $member = User::get_user_by_id($_GET['param1']);
            if ($user->id === $member->id || $user->role === "admin") {
                $skills = $member->get_my_skills_not_mastered();
                $masterings = $member->get_my_skills_mastered();

                if (isset($_POST['skill']) && $skill = Skill::get_skill_by_id($_POST['skill']) && isset($_POST["level"])) {
                    $skill = Skill::get_skill_by_id($_POST['skill']);
                    $level = (int)$_POST["level"];
                    $mastery = new Mastering($member, $skill, $level);
                    $errors = $mastery->validate_level($level);
                    if (empty($errors)) {
                        $member->add_mastering($mastery);
                        $this->redirect("mastering", "skills_mastered", $member->id);
                    }
                }
                (new View("Skills"))->show(array(
                    "user" => $user,
                    "member" => $member,
                    "masterings" => $masterings,
                    "skills" => $skills,
                    "errors" => $errors
                ));
            } else {
                Tools::abort("add: Wrong/missing param or action no permited");
            }
        } else {
            Tools::abort("add no permited: You must be admin");
        }
    }

    //--EDIT MASTERING--
    public function level_up()
    {
        $masterings = [];
        $skills = [];
        $id = "";
        $errors = array();
        $user = $this->get_user_or_redirect();
        if (isset($_GET['param1']) && $_GET['param1'] != '' && $member = User::get_user_by_id($_GET['param1'])) {
            $member = User::get_user_by_id($_GET['param1']);
            if ($user->id === $member->id || $user->role === "admin") {
                $skills = $member->get_my_skills_not_mastered();
                $masterings = $member->get_my_skills_mastered();
                if (isset($_POST["skill_id"]) && $_POST["skill_id"] != "" && $skill = Skill::get_skill_by_id($_POST['skill_id'])) {
                    $id = $_POST['skill_id'];
                    $skill = Skill::get_skill_by_id($id);
                    $edit_mastering = $member->get_mastering($skill);
                    $errors = $edit_mastering->validate_level($edit_mastering->level++);
                    if (empty($errors)) {
                        $member->update_mastering_level_up($edit_mastering);
                        $this->redirect("mastering", "skills_mastered", $member->id);
                    }
                }
                (new View("Skills"))->show(array("user" => $user, "member" => $member, "skill_id" => $id, "masterings" => $masterings, "skills" => $skills, "errors" => $errors));
            } else {
                Tools::abort("level_up no permited: You must be admin");
            }
        } else {
            Tools::abort("level_up: Wrong/missing param or action no permited");
        }
    }

    public function level_down()
    {
        $masterings = [];
        $skills = [];
        $id = "";
        $errors = array();
        $user = $this->get_user_or_redirect();
        if (isset($_GET['param1']) && $_GET['param1'] != '' && $member = User::get_user_by_id($_GET['param1'])) {
            $member = User::get_user_by_id($_GET['param1']);
            if ($user->id === $member->id || $user->role === "admin") {
                $skills = $member->get_my_skills_not_mastered();
                $masterings = $member->get_my_skills_mastered();
                if (isset($_POST["skill_id"]) && $_POST["skill_id"] != "" && $skill = Skill::get_skill_by_id($_POST['skill_id'])) {
                    $id = $_POST['skill_id'];
                    $skill = Skill::get_skill_by_id($id);
                    $edit_mastering = $member->get_mastering($skill);
                    $errors = $edit_mastering->validate_level($edit_mastering->level--);
                    if (empty($errors)) {
                        $member->update_mastering_level_down($edit_mastering);
                        $this->redirect("mastering", "skills_mastered", $member->id);
                    }
                }
                (new View("Skills"))->show(array("user" => $user, "member" => $member, "skill_id" => $id, "masterings" => $masterings, "skills" => $skills, "errors" => $errors));
            } else {
                Tools::abort("level_down no permited: You must be admin");
            }
        } else {
            Tools::abort("level_down: Wrong/missing param or action no permited");
        }
    }

    //--DELETE MASTERING--
    public function delete()
    {
        $user = $this->get_user_or_redirect();
        if (isset($_GET['param1']) && $_GET['param1'] != '' && $member = User::get_user_by_id($_GET['param1'])) {
            $member = User::get_user_by_id($_GET['param1']);
            if ($user->id === $member->id || $user->role === "admin") {
                if (isset($_POST["skill_id"]) && $_POST["skill_id"] != "") {
                    $skill = Skill::get_skill_by_id($_POST['skill_id']);
                    $mastering = $member->get_mastering($skill);
                    $member->delete_mastering($mastering);
                    $this->redirect("mastering", "skills_mastered", $member->id);
                } else {
                    Tools::abort("ERROR DELETING: Wrong/missing param or action no permited");
                }
            } else {
                Tools::abort("delete no permited: You must be admin ");
            }
        } else {
            Tools::abort("delete: Wrong/missing param or action no permited");
        }
    }

    //--AJAX--
    function add_service()
    {
        $user = $this->get_user_or_redirect();
        $response = "false";
        if (isset($_POST["level"]) && isset($_POST["skill"]) && $_POST["skill"] != "" && $_POST["level"] != "" && isset($_POST["user"]) && $_POST["user"] != "") {

            $skill = Skill::get_skill_by_id($_POST['skill']);
            $member = User::get_user_by_id($_POST["user"]);
            $level = (int)$_POST["level"];
            $mastery = new Mastering($member, $skill, $level);
            $member->add_mastering($mastery);
            $response = "true";
        }
        echo $response;
    }

    function update_service()
    {
        $user = $this->get_user_or_redirect();
        $response = "false";
        if (isset($_POST["level"]) && isset($_POST["skill"]) && $_POST["skill"] != "" && $_POST["level"] != "" && isset($_POST["user"]) && $_POST["user"] != "") {
            $skill = Skill::get_skill_by_id($_POST['skill']);
            $member = User::get_user_by_id($_POST["user"]);
            $level = (int)$_POST["level"];
            $mastery = new Mastering($member, $skill, $level);
            $member->update_mastering($mastery);
            $response = "true";
        }
        echo $response;
    }

    function delete_service()
    {
        $user = $this->get_user_or_redirect();
        $response = "false";
        if (isset($_POST["user"]) && $_POST["user"] != "" && isset($_POST["skill"]) && $_POST["skill"] != "") {
            $skill = Skill::get_skill_by_id($_POST['skill']);
            $member = User::get_user_by_id($_POST["user"]);
            $mastering = $member->get_mastering($skill);
            $member->delete_mastering($mastering);
            $response = "true";
        }
        echo $response;
    }
}
