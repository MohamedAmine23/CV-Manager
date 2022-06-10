<?php
require_once "framework/Controller.php";
require_once "framework/Configuration.php";
require_once "framework/Tools.php";
require_once "model/Experience.php";
require_once "model/User.php";
require_once "model/Using.php";

class ControllerExperience extends Controller
{

    public function index()
    {
        $this->experiences();
    }

    //--LIST EXPERIENCES--
    public function experiences()
    {
        $user = $this->get_user_or_redirect();
        if (isset($_GET['param1']) && $_GET['param1'] != '') //if we don't have param ->the current user page
        {
            if ((!$member = User::get_user_by_id($_GET['param1']))) {
                Tools::abort("experiences: Wrong/missing param or action no permited");
            } else {
                $member = User::get_user_by_id($_GET['param1']);
                if ($user->role !== "admin" && $member->id !== $user->id) {
                    Tools::abort("experiences no permited: You must be admin !");
                }
            }
        } else {
            $member = User::get_user_by_id($user->id);
        }
        (new View("experiences"))->show(
            array(
                "user" => $user, //loged
                "member" => $member, // of a member that we have access
                "experiences" => $member->get_my_experiences(),
                "skills_used" => Using::get_all_used()
            )
        );
    }

    //--ADD EXPERIENCE--
    public function add()
    {
        $user = $this->get_user_or_redirect();
        $start = "";
        $stop = "";
        $title = "";
        $description = "";
        $member = "";
        $place_selected = "";
        $places = Place::get_places();
        $skills = Skill::get_all_skills();
        $skills_checked = array();
        $errors = array();
        if (isset($_GET['param1']) && $_GET['param1'] != '' && $member = User::get_user_by_id($_GET['param1'])) {
            $member = User::get_user_by_id($_GET['param1']);
            if ($user->id === $member->id || $user->role === "admin") {
                if (isset($_POST["start_date"]) && isset($_POST["title"]) && isset($_POST["place"]) && $place_selected = Place::get_place_by_id($_POST["place"])) {
                    $start = $_POST["start_date"];
                    $title = trim($_POST["title"]);
                    $place_selected = Place::get_place_by_id($_POST["place"]);
                    $experience = new Experience($start, $title, $member, $place_selected);
                    $errors = $experience->validate_start();
                    $errors = array_merge($errors, $experience->validate_title());
                    if (isset($_POST["description"]) && $_POST["description"] != "") {
                        $description = trim($_POST["description"]);
                        $experience->description = $description;
                        $errors = array_merge($errors, $experience->validate_description());
                    }
                    if (isset($_POST["stop_date"]) && $_POST["stop_date"] != "") {
                        $stop = $_POST["stop_date"];
                        $experience->stop = $stop;
                        $errors = array_merge($errors, $experience->validate_stop());
                    }
                    foreach ($skills as $skill) {
                        if (isset($_POST["skill_" . $skill->id]) && $_POST["skill_" . $skill->id] != "" && Skill::get_skill_by_id($_POST["skill_" . $skill->id])) {
                            $skill_for_using = $_POST["skill_" . $skill->id];
                            $skills_checked[] = Skill::get_skill_by_id($skill_for_using);
                        }
                    }
                    if (empty($errors)) {
                        $member->add_experience($experience);
                        foreach ($skills_checked as $skill) {
                            $used = new Using($experience->id, $skill->id);
                            $member->add_skill_of_experience($used);
                        }
                        $this->redirect("Experience", "experiences", $member->id);
                    }
                }
                (new View("experience"))->show(
                    array(
                        "user" => $user,
                        "member" => $member,
                        "places" => $places,
                        "cities" => Place::get_cities(),
                        "place_selected" => $place_selected,
                        "skills_used" => $skills,
                        "skills_checked" => $skills_checked,
                        "description" => $description,
                        "start" => $start,
                        "stop" => $stop,
                        "title" => $title,
                        "errors" => $errors, "mode_view" => "add"
                    )
                );
            } else {
                Tools::abort("add no permited: You must be admin");
            }
        } else {
            Tools::abort("add: Wrong/missing param or action no permited");
        }
    }

    //--EDIT EXPERIENCE--
    public function edit()
    {
        $user = $this->get_user_or_redirect();
        $errors = array();
        // verifie les parametres passé via l'url (GET)
        if (isset($_GET['param1']) && $_GET['param1'] != '' && $experience = Experience::get_experience_by_id($_GET['param1']) &&
                isset($_GET['param2']) && $_GET['param2'] != '' && $member = User::get_user_by_id($_GET['param2'])
        ) {
            // recupere l'experience via la variable GET
            $experience = Experience::get_experience_by_id($_GET['param1']);
            // recupere l'utilisateur via la variable GET
            $member = User::get_user_by_id($_GET['param2']);
            // verifie que l'experience appartient a l'utilisteur courant ou que l'utilisateur a le role d'admin
            if (($user->id === $member->id || $user->role === "admin") && $member->id === $experience->get_user()->id) {
                // recupere toutes les places
                $places = Place::get_places();
                // recupere touts les skills
                $skills = Skill::get_all_skills();
                $skills_checked = array();
                // recupere les skills de l'experience passé en parametre
                $skills_checked_by_using = Using::get_using_experiences($experience);
                // rajouter dans le tableau skills_checked touts les skills précochés
                foreach ($skills_checked_by_using as $used) {
                    $skill_for_using = $used->get_skill()->id;
                    $skills_checked[] = Skill::get_skill_by_id($skill_for_using);
                }
                // atributs de l'objet Experience
                $start = $experience->start;
                $stop = $experience->stop;
                $title = $experience->title;
                $description = $experience->description;
                $place_selected = Place::get_place_by_id($experience->get_place()->id);


                // rajoute les bons Using (les skills cochés) dans la DB pour l'experience
                foreach ($skills as $skill) {
                    // verifie si le skill est bien passé en parametre POST et qu'il existe dans la DB
                    if (
                        isset($_POST["skill_" . $skill->id])
                        && $_POST["skill_" . $skill->id] != ""
                        && Skill::get_skill_by_id($_POST["skill_" . $skill->id])
                        && !Using::get_using($experience->id, $skill->id)
                    ) {
                        $skills_checked = array();
                        $skill_for_using = $_POST["skill_" . $skill->id];
                        $skills_checked[] = Skill::get_skill_by_id($skill_for_using);
                        $used = new Using($experience->id, $skill_for_using);
                        $member->add_skill_of_experience($used);
                    }
                }

                // validation
                if (isset($_POST['start_date']) || isset($_POST['stop_date']) || isset($_POST['title']) || isset($_POST['description']) || isset($_POST['place'])) {
                    if (isset($_POST['start_date'])) {
                        $start = $_POST['start_date'];
                        $experience->start = $start;
                        $errors = array_merge($errors, $experience->validate_start());
                    }
                    if (isset($_POST['stop_date'])) {
                        $stop = $_POST['stop_date'];
                        if ($stop == "") {
                            $experience->stop = null;
                        } else {
                            $experience->stop = $stop;
                            $errors = array_merge($errors, $experience->validate_stop());
                        }
                    }
                    if (isset($_POST['title'])) {
                        $title = trim($_POST['title']);
                        $experience->title = $title;
                        $errors = array_merge($errors, $experience->validate_title());
                    }
                    if (isset($_POST['description'])) {
                        $description = trim($_POST['description']);
                        if ($description == "") {
                            $experience->description = null;
                        } else {
                            $experience->description = $description;
                            $errors = array_merge($errors, $experience->validate_description());
                        }
                    }
                    if (isset($_POST['place']) && Place::get_place_by_id($_POST["place"])) {
                        $place_selected = Place::get_place_by_id($_POST["place"]);
                        $experience->set_place($place_selected);
                    }
                    if (empty($errors)) {
                        // si aucune erreur de validation, sauvegarde les modifications dans le DB et redirige vers la vue experiences
                        $member->update_experience($experience);
                        $this->redirect("Experience", "experiences", $member->id);
                    }
                }


                // redirige l'utilisateur vers la vue edit en cas d'erreur de validation
                (new View("experience"))->show(
                    array(
                        "user" => $user,
                        "member" => $member,
                        "experience" => $experience,
                        "places" => $places,
                        "cities" => Place::get_cities(),
                        "place_selected" => $place_selected,
                        "skills_used" => $skills,
                        "skills_checked" => $skills_checked,
                        "description" => $description,
                        "start" => $start,
                        "stop" => $stop,
                        "title" => $title,
                        "errors" => $errors, "mode_view" => "edit"
                    )
                );
            } else {
                Tools::abort("edit no permited: You must be admin");
            }
        } else {
            Tools::abort("edit: Wrong/missing param or action no permited");
        }
    }

    //--DELETE EXPERIENCE--
    public function request_delete()
    {
        $user = $this->get_user_or_redirect();
        if (isset($_GET['param1']) && $_GET['param1'] != '' && $experience = Experience::get_experience_by_id($_GET['param1']) &&
                isset($_GET['param2']) && $_GET['param2'] != '' && $member = User::get_user_by_id($_GET['param2'])
        ) {
            $member = User::get_user_by_id($_GET['param2']);
            $experience = Experience::get_experience_by_id($_GET['param1']);
            if (($user->id === $member->id || $user->role === "admin") && $member->id === $experience->get_user()->id) {

                $delete_class = "experience";
                $delete_element = $experience->title . " (" . $experience->get_place()->name . ", " . $experience->get_place()->city . ")";
                (new View("delete_confirm"))->show(
                    array(
                        "user" => $user,
                        "member" => $member,
                        "delete_class" => $delete_class,
                        "delete_element" => $delete_element,
                        "field" => $experience
                    )
                );
            } else {
                Tools::abort("request_delete no permited: You must be admin");
            }
        } else {
            Tools::abort("request_delete: Wrong/missing param or action no permited");
        }
    }

    public function delete()
    {
        $user = $this->get_user_or_redirect();
        if (
            isset($_POST['delete_id']) && $_POST['delete_id'] != ""
            && isset($_POST['member_id']) && $_POST['member_id'] != ""
        ) {
            $member = User::get_user_by_id($_POST['member_id']);
            if ($user->id === $member->id || $user->role === "admin") {
                $id = $_POST['delete_id'];
                $experience = Experience::get_experience_by_id($id);
                $result = $member->delete_experience($experience);
                if ($result) {
                    $this->redirect("Experience", "experiences", $member->id);
                } else {
                    Tools::abort(" ERROR DELETING: Wrong/missing param or action no permited");
                }
            } else {
                Tools::abort("delete no permited: You must be admin ");
            }
        } else {
            Tools::abort("delete: Wrong/missing param or action no permited");
        }
    }


    //--Ajax--
    public function delete_service()
    {
        $user = $this->get_user_or_redirect();
        $res = "false";
        if (
            isset($_POST['id']) && $_POST['id'] != ""
            && isset($_POST['member']) && $_POST['member'] != ""
        ) {
            $member = User::get_user_by_id($_POST['member']);
            if ($user->id === $member->id || $user->role === "admin") {
                $id = $_POST['id'];
                $experience = Experience::get_experience_by_id($id);
                $result = $member->delete_experience($experience);
                if ($result) {
                    $res = "true";
                } else {
                    Tools::abort(" ERROR DELETING: Wrong/missing param or action no permited");
                }
            } else {
                Tools::abort("delete no permited: You must be admin ");
            }
        } else {
            Tools::abort("delete: Wrong/missing param or action no permited");
        }
        echo $res;
    }

    public function valid_start_date()
    {
        $user = $this->get_user_or_redirect();
        if (isset($_POST["member"]) && $_POST["member"] != "") {
            $member = User::get_user_by_id($_POST["member"]);
            if ($user->id === $member->id || $user->role === "admin") {
                $birthdate_time = strtotime($member->birthdate);
                $start_time = strtotime($_POST['start_date']);
                //var_dump($_POST);
                if (isset($_POST['start_date']) && $_POST['start_date'] != "" && $birthdate_time < $start_time) {
                    echo "true";
                } else {
                    echo "false";
                }
            } else {
                Tools::abort("valid no permited: You must be admin ");
            }

        } else {
            Tools::abort("valid: Wrong/missing param or action no permited");
        }

    }

    public function get_experiences_filtered()
    {
        $user = $this->get_user_or_redirect();
        if(isset($_POST['user']) && $_POST['user'] != ""){
            $member = User::get_user_by_id($_POST["user"]);
            if (isset($_POST['filter_start']) && $_POST['filter_start'] != ""
                && isset($_POST['filter_end']) && $_POST['filter_end'] != "") {
                echo json_encode($member->get_experiences_filtered($_POST['filter_start'], $_POST['filter_end']));
            }
        }else {
            Tools::abort("experiences_filtered: Wrong/missing param or action no permited");
        }

    }

    //Ajax
    public function get_max_length()
    {
        $user = $this->get_user_or_redirect();
        $max_char = Configuration::get("description_max_length");
        echo $max_char;
    }



    public function get_validation_config()

    {
        $user = $this->get_user_or_redirect();
        $validationPlugin = Configuration::get("validate_plugin");
        echo($validationPlugin ? "true" : "false");
    }

    public function get_slider_config()
    {
        $user = $this->get_user_or_redirect();
        $sliderPlugin = Configuration::get("slider_plugin");
        echo($sliderPlugin ? "true" : "false");
    }

    //Calendar
    public function calendar()
    {
        $user = $this->get_user_or_redirect();
        if (isset($_GET['param1']) && $_GET['param1'] != '') //if we don't have param ->the current user page
        {
            if ((!$member = User::get_user_by_id($_GET['param1']))) {
                Tools::abort("experiences: Wrong/missing param or action no permited");
            } else {
                $member = User::get_user_by_id($_GET['param1']);
                if ($user->role !== "admin" && $member->id !== $user->id) {
                    Tools::abort("experiences no permited: You must be admin !");
                }
            }
        } else {
            $member = User::get_user_by_id($user->id);
        }

        (new View("calendar"))->show(array(
            "user" => $user, //loged
            "member" => $member, // of a member that we have access
            "experiences" => $member->get_my_experiences(),));
    }

    public function get_json_experience()
    {
        $user = $this->get_user_or_redirect();
        $events = [];
        if (isset($_GET["param1"]) && $_GET["param1"] !== "") {
            $member = User::get_user_by_id($_GET["param1"]);
            $member_experiences = $member->get_my_experiences();

            foreach ($member_experiences as $exp) {
                $stop = $exp->stop == null ? date("Y-m-d") : $exp->stop;
                $events[] = ["start" => $exp->start, "end" => date('Y-m-d', strtotime("$stop +1 day")), "title" => $exp->title, "color" => $this->rand_color()];
            }
            echo json_encode($events);
        }else{
            Tools::abort("experience: Wrong/missing param or action no permited");
        }
    }

    private function rand_color()
    {
        return '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
    }

}
