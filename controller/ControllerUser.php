<?php
require_once "framework/Controller.php";
require_once "framework/Configuration.php";
require_once "framework/Tools.php";
require_once "model/User.php";
require_once "model/Skill.php";

class ControllerUser extends Controller
{

    public function index()
    {
        if ($this->user_logged()) {
            $this->redirect("experience", "index");
        } else {
            (new View("index"))->show();
        }
    }

    //--LOGIN--
    public function login()
    {
        $mail = "";
        $password = "";
        $errors = [];
        if (isset($_POST["mail"]) && isset($_POST["password"])) {
            $mail = $_POST["mail"];
            $password = $_POST["password"];
            $user = User::get_user_by_mail($mail);
            $errors = User::validate_login($mail, $password);
            if (empty($errors)) {
                $this->log_user($user);
            }
        }
        (new View("login"))->show(array("mail" => $mail, "password" => $password, "errors" => $errors));
    }

    //--SIGNUP--
    public function signup()
    {
        $mail = '';
        $fullName = '';
        $title = '';
        $birthdate = '';
        $password = '';
        $password_confirm = '';
        $errors = [];
        if (
            isset($_POST['mail']) && isset($_POST['fullName']) && ($_POST['title']) && isset($_POST['birthdate'])
            && isset($_POST['password']) && isset($_POST['password_confirm'])
        ) {
            $mail = trim($_POST['mail']);
            $fullName = trim($_POST['fullName']);
            $title = trim($_POST['title']);
            $birthdate = $_POST['birthdate'];
            $password = $_POST['password'];
            $password_confirm = $_POST['password_confirm'];
            $user = new User($mail, $fullName, $title, Tools::my_hash($password), $birthdate);
            $errors = $user->validate_mail();
            $errors = array_merge($errors, $user->validate_fullName());
            $errors = array_merge($errors, $user->validate_title());
            $errors = array_merge($errors, $user->validate_birthdate());
            $errors = array_merge($errors, User::validate_passwords($password, $password_confirm));
            if (count($errors) == 0) {
                $user->update();
                $member = User::get_user_by_mail($user->mail);
                $this->log_user($member);
            }
        }
        $view = new View('signup');
        $view->show(["mail" => $mail, "password" => $password, "fullName" => $fullName, "title" => $title, "password_confirm" => $password_confirm, "birthdate" => $birthdate, "errors" => $errors]);
    }

    //--VIEW PROFILE--
    public function profile()
    {
        $user = $this->get_user_or_redirect();
        $errors = [];

        (new View("profile"))->show(array("user" => $user, "errors" => $errors));
    }

    //--EDIT PROFILE--
    public function edit_profile()
    {
        $errors = [];
        $user = $this->get_user_or_redirect();
        $member = User::get_user_by_id($user->id);
        if (isset($_POST['edit_mail']) && $_POST['edit_mail'] != "") {
            $editMail = trim($_POST['edit_mail']);
            if ($member->mail !== $editMail) {
                $member->mail = $editMail;
                $errors = $member->validate_mail();
            }
        }
        if (isset($_POST['edit_fullName']) && $_POST['edit_fullName'] != "") {
            $editFullName = trim($_POST['edit_fullName']);
            $member->fullName = $editFullName;
            $errors = array_merge($errors, $member->validate_fullName());
        }
        if (isset($_POST['edit_title']) && $_POST['edit_title'] != "") {
            $editTitle = trim($_POST['edit_title']);
            $member->title = $editTitle;
            $errors = array_merge($errors, $member->validate_title());
        }
        if (isset($_POST['edit_birthdate']) && $_POST['edit_birthdate'] != "") {
            $editBirthdate = $_POST['edit_birthdate'];
            $member->birthdate = $editBirthdate;
            $errors = array_merge($errors, $member->validate_birthdate());
        }

        if (empty($errors)) {
            $member->updateMember();
            $this->log_user($member, "user", "profile");
        } else {
            (new View("profile"))->show(array(
                "user" => $user, "mail" => $editMail, "fullName" => $editFullName,
                "title" => $editTitle, "birthdate" => $editBirthdate, "errors" => $errors
            ));
        }
    }

    //--CHANGE PASSWORD--
    public function password()
    { //REDIRECT VIEW TO CHANGE PASSWORD
        $user = $this->get_user_or_redirect();
        if (isset($_GET['param1']) && $_GET['param1'] != '' && $member = User::get_user_by_id($_GET['param1'])) {
            $member = User::get_user_by_id($_GET['param1']);
            if ($user->id === $member->id || $user->role === "admin") {
                (new View("password"))->show(array("user" => $user, "member" => $member, "password" => "", "password_confirm" => "", "errors" => []));
            } else {
                Tools::abort("password no permited: You must be admin");
            }
        } else {
            Tools::abort("password: Wrong/missing param or action no permited");
        }
    }

    public function password_change()
    {
        $user = $this->get_user_or_redirect();
        $errors = [];
        $password = '';
        $password_confirm = '';
        if (isset($_POST['member_id']) && $_POST['member_id'] != "") {
            $member = User::get_user_by_id($_POST['member_id']);
            if (isset($_POST['password']) && isset($_POST['password_confirm'])) {
                $password = $_POST['password'];
                $password_confirm = $_POST['password_confirm'];
                $errors = User::validate_passwords($password, $password_confirm);
                if (empty($errors)) {
                    $member->password = Tools::my_hash($password);
                    $member->updatePassword();
                    $this->redirect("user", "manageUsers");
                }
            }
            (new View("password"))->show(array(
                "user" => $user,
                "member" => $member,
                "password" => $password,
                "password_confirm" => $password_confirm,
                "errors" => $errors
            ));
        } else {
            Tools::abort("password: Wrong/missing param or action no permited");
        }
    }

    //--MANAGE USERS--
    public function manageUsers()
    {
        $user = $this->get_user_or_redirect();
        if ($user->role === "admin") {
            $masterings = [];
            $errors = [];
            $users = [];
            $skill_selected = "";
            $skills = Skill::get_all_skills();
            if (isset($_GET['param1']) && $_GET['param1'] != '' && $skill_selected = Skill::get_skill_by_id($_GET['param1'])) {
                $skill_selected = Skill::get_skill_by_id($_GET['param1']);
                $masterings = Mastering::get_mastering_skills($skill_selected);
                foreach ($masterings as $mastering) {
                    $users[] = User::get_user_by_id($mastering->get_user()->id);
                }
                (new View("manageUsers"))->show(array(
                    "user" => $user, "mail" => "", "fullName" => "",
                    "title" => "", "birthdate" => "", "role" => "", "skill_selected" => $skill_selected,
                    "members" => $users, "skills" => $skills, "errors" => $errors
                ));
            } else {
                $users = User::get_users();
                (new View("manageUsers"))->show(array("user" => $user, "members" => $users, "errors" => $errors, "skills" => $skills));
            }
        } else {
            Tools::abort("manageUsers no permited: You must be admin");
        }
    }

    //--EDIT USER--   
    public function member_edit()
    {
        $errors = [];
        $editRole = '';
        $user = $this->get_user_or_redirect();
        if (isset($_POST['edit_id']) && $_POST['edit_id'] != "" && $member = User::get_user_by_id($_POST['edit_id'])) {
            $id = $_POST['edit_id'];
            $member = User::get_user_by_id($id);
            if ($user->id === $member->id || $user->role === "admin") {
                if (isset($_POST['edit_mail']) && $_POST['edit_mail'] != "") {
                    $editMail = trim($_POST['edit_mail']);
                    if ($member->mail !== $editMail) {
                        $member->mail = $editMail;
                        $errors = $member->validate_mail();
                    }
                }
                if (isset($_POST['edit_fullName']) && $_POST['edit_fullName'] != "") {
                    $editFullName = trim($_POST['edit_fullName']);
                    $member->fullName = $editFullName;
                    $errors = array_merge($errors, $member->validate_fullName());
                }
                if (isset($_POST['edit_title']) && $_POST['edit_title'] != "") {
                    $editTitle = trim($_POST['edit_title']);
                    $member->title = $editTitle;
                    $errors = array_merge($errors, $member->validate_title());
                }
                if (isset($_POST['edit_birthdate']) && $_POST['edit_birthdate'] != "") {
                    $editBirthdate = $_POST['edit_birthdate'];
                    $member->birthdate = $editBirthdate;
                    $errors = array_merge($errors, $member->validate_birthdate());
                }
                if (isset($_POST['edit_role']) && $_POST['edit_role'] != "") {
                    $editRole = trim($_POST['edit_role']);
                    $member->role = $editRole;
                    $errors = array_merge($errors, $member->validate_role());
                }
                if (empty($errors)) {
                    $member->updateMember();
                    if ($user->id === $member->id) {
                        $this->log_user($member, "user", "manageUsers");
                    }
                    $this->redirect("user", "manageUsers");
                } else {

                    $skills = Skill::get_all_skills();
                    (new View("manageUsers"))->show(array(
                        "user" => $user,
                        "edit_id" => $id,
                        "mail" => $editMail,
                        "fullName" => $editFullName,
                        "title" => $editTitle,
                        "birthdate" => $editBirthdate,
                        "role" => $editRole,
                        "members" => User::get_users(),
                        "skills" => $skills,
                        "errors" => $errors
                    ));
                }
            } else {
                Tools::abort("member_edit no permited: You must be admin");
            }
        } else {
            Tools::abort("member_edit: Wrong/missing param or action no permited");
        }
    }

    //--DELETE USER--
    public function delete()
    {
        $user = $this->get_user_or_redirect();
        if (isset($_POST['delete_id']) && $_POST['delete_id'] != "" && $member = User::get_user_by_id($_POST['delete_id'])) {
            $id = $_POST['delete_id'];
            $member = User::get_user_by_id($id);
            if ($user->role === "admin" && $member->id !== $user->id) {
                $result = $member->delete();
                if ($result) {
                    $this->redirect("User", "manageUsers");
                } else {
                    Tools::abort("ERROR DELETING:Wrong/missing param or action no permited");
                }
            } else {
                Tools::abort("delete no permited: You must be admin ");
            }
        } else {
            Tools::abort("delete: Wrong/missing param or action no permited");
        }
    }

    public function request_delete()
    {
        $user = $this->get_user_or_redirect();
        if (isset($_GET['param1']) && $_GET['param1'] != '' && $member = User::get_user_by_id($_GET['param1'])) {
            $member = User::get_user_by_id($_GET['param1']);
            if ($user->role === "admin" && $member->id !== $user->id) {
                $delete_class = "User";
                $delete_element = "$member->fullName<br>($member->mail)";
                (new View("delete_confirm"))->show(array("user" => $user, "delete_class" => $delete_class, "delete_element" => $delete_element, "field" => $member));
            } else {
                Tools::abort("request_delete no permited: You must be admin");
            }
        } else {
            Tools::abort("request_delete: Wrong/missing param or action no permited");
        }
    }

    //--FILTER--
    public function filter_by_skills()
    {
        $user = $this->get_user_or_redirect();

        if ($user->role === "admin") {
            if (isset($_POST["submit"])) {
                if (isset($_POST["filter"]) && $_POST["filter"] != "" && $skill_selected = Skill::get_skill_by_id($_POST["filter"])) {
                    $skill_selected = Skill::get_skill_by_id($_POST["filter"]);
                    $this->redirect("user", "manageUsers", $skill_selected->id);
                }
            } elseif (isset($_POST["reset"])) {
                $this->redirect("user", "manageUsers");
            }
        } else {
            Tools::abort("filter no permited: You must be admin");
        }
    }

    public function logged_user()
    {
        return $this->get_user_or_false();
    }

    public function get_user_birthdate()
    {
        $user = $this->get_user_or_redirect();
        if (isset($_POST['member']) && $_POST['member'] != "") {
            $member = User::get_user_by_id($_POST['member']);
            echo $member->birthdate;
        }
    }
}
