<?php
require_once "framework/Controller.php";
require_once "framework/Configuration.php";
require_once "framework/Tools.php";
require_once "model/Place.php";
require_once "model/User.php";

class ControllerPlace extends Controller
{

    public function index()
    {

        $this->managePlaces();
    }

    //--ADD PLACE--
    public function add()
    {
        $errors = [];
        $name = '';
        $city = '';
        $user = $this->get_user_or_redirect();
        if ($user->role === "admin") {
            if (isset($_POST['name']) && $_POST['name'] != "" && isset($_POST['city']) && $_POST['city'] != "") {
                $name = trim($_POST['name']);
                $city = trim($_POST['city']);
                $place = new Place($name, $city);
                $errors = $place->validate();
                $errors = array_merge($errors, $place->validate_unicity());
                if (empty($errors)) {
                    $place->update();
                    $this->redirect("place");
                } else {
                    (new View("managePlaces"))->show(array("user" => $user, "addCity" => $city, "addName" => $name, "places" => Place::get_places(), "errors" => $errors));
                }
            } else {
                Tools::abort("add: Wrong/missing param or action no permited");
            }
        } else {
            Tools::abort("add no permited: You must be admin");
        }
    }

    //--EDIT PLACE--
    public function edit()
    {
        $errors = [];
        $addcity = '';
        $addName = '';
        $user = $this->get_user_or_redirect();
        if ($user->role === "admin") {
            if (isset($_POST['edit_id']) && $_POST['edit_id'] != "" && $oldPlace = Place::get_place_by_id($_POST['edit_id'])) {
                $id = $_POST['edit_id'];
                $oldPlace = Place::get_place_by_id($id);
                if (isset($_POST['edit_name']) && $_POST['edit_name'] != "" && isset($_POST['edit_city']) && $_POST['edit_city'] != "") {
                    $editName = trim($_POST['edit_name']);
                    $editCity = trim($_POST['edit_city']);
                    $oldPlace->name = $editName;
                    $oldPlace->city = $editCity;
                    $errors = $oldPlace->validate();
                    $errors = array_merge($errors, $oldPlace->validate_unicity());
                    if (empty($errors)) {
                        $oldPlace->update();
                        $this->redirect("place");
                    } else {
                        (new View("managePlaces"))->show(array(
                            "user" => $user,
                            "city" => $editCity,
                            "name" => $editName,
                            "edit_id" => $id,
                            "addCity" => $addcity,
                            "addName" => $addName,
                            "places" => Place::get_places(), "errors" => $errors
                        ));
                    }
                }
            } else {
                Tools::abort("edit: Wrong/missing param or action no permited");
            }
        } else {
            Tools::abort("edit no permited: You must be admin");
        }
    }

    //--DELETE PLACE--
    public function delete()
    {
        $user = $this->get_user_or_redirect();
        if ($user->role === "admin") {
            if (isset($_POST['delete_id']) && $_POST['delete_id'] != "" && $place = Place::get_place_by_id($_POST['delete_id'])) {
                $id = $_POST['delete_id'];
                $place = Place::get_place_by_id($id);
                $result = $place->delete();
                if ($result) {
                    $this->redirect("Place");
                } else {
                    Tools::abort("ERROR DELETING: Wrong/missing param or action no permited");
                }
            } else {
                Tools::abort("delete: Wrong/missing param or action no permited");
            }
        } else {
            Tools::abort("delete no permited: You must be admin ");
        }
    }

    public function request_delete()
    {
        $user = $this->get_user_or_redirect();
        if ($user->role === "admin") {
            if (isset($_GET['param1']) && $_GET['param1'] != '' && $place = Place::get_place_by_id($_GET['param1'])) {
                $place = Place::get_place_by_id($_GET['param1']);
                $delete_class = "Place";
                $delete_element = "$place->name($place->city)";
                (new View("delete_confirm"))->show(array("user" => $user, "delete_class" => $delete_class, "delete_element" => $delete_element, "field" => $place));
            } else {
                Tools::abort("request_delete: Wrong/missing param or action no permited");
            }
        } else {
            Tools::abort("request_delete no permited: You must be admin");
        }
    }

    //--MANAGE PLACES--
    public function managePlaces()
    {
        $user = $this->get_user_or_redirect();
        if ($user->role === "admin") {
            $errors = array();
            (new View("managePlaces"))->show(array("user" => $user, "city" => "", "name" => "", "addCity" => "", "addName" => "", "places" => Place::get_places(), "errors" => $errors));
        } else {
            Tools::abort("managePlaces no permited: You must be admin");
        }
    }
}
