<!doctype html>
<html lang="en">

<head>
    <base href="<?= $web_root ?>">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://kit.fontawesome.com/3eef553146.js" crossorigin="anonymous"></script>
    <script src="lib/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link href="css/style.css" rel="stylesheet" type="text/css">
    <link rel="icon" href="img/favicon.ico">
    <title> <?= $member->fullName ?>'s Skills </title>
</head>

<body>
    <nav class="navbar navbar-expand-sm bg-primary navbar-dark d-flex justify-content-between flex-column flex-sm-row">
        <span class="navbar-brand text-white"><i class="fas fa-cheese m-1"></i>Munster.be</span>
        <ul class="nav navbar-nav navbar-right">
            <li class="nav-item"><a class="nav-link active" href="mastering/">Skills</a></li>
            <li class="nav-item"><a class="nav-link" href="experience/experiences/">Experiences</a></li>
            <?php if ($user->role === "admin") : ?>
                <li class="nav-item"><a class="nav-link" href="Skill/manageSkills">Manage skills</a></li>
                <li class="nav-item"><a class="nav-link" href="Place/managePlaces">Manage places</a></li>
                <li class="nav-item"><a class="nav-link " href="User/manageUsers">Manage users</a></li>
            <?php endif; ?>
            <li class="nav-item"><a class="btn loged nav-link" href="User/profile"><i class="fas fa-user-shield"></i>&nbsp;<?= $user->fullName ?></a></li>
            <li class="nav-item"><a class="btn login nav-link" href="user/logout"><i class="fa-sign-out-alt fas" aria-hidden="true"></i></a></li>
        </ul>
    </nav>
    <div class="container m-0">
        <h1><?= $member->fullName ?>,<?= $member->title ?></h1>
        <h3>Skills </h3>
        <script>
            $(function() {
                level = $(".skillLevel").text();
                member_id = <?= $member->id ?>;
                my_skills = <?= $masterings_json ?>;
                skills_not_mastered = <?= $not_mastered ?>;
                displayTable();
                display_stars_empty();
                $(".addskill").append("<div class=\"all_mastered\">All skills are mastered</div>")
                skills_menu();

            });

            function skills_menu() {
                if ($("select").children().length == 0) {
                    $(".add_skillz").hide();
                    $(".all_mastered").show();
                } else {
                    $(".add_skillz").show();
                    $(".all_mastered").hide();
                }
            }
            //hover stars
            function hover_in_skill_not_mastered(level) {
                for (let k = level; k >= 1; k--) {
                    $("#star_" + k).attr("class", 'fas fa-star').css("color", "blueviolet");
                }
            }

            function hover_out_skill_not_mastered(level) {
                $("#add_star ").children().attr("class", 'far fa-star').css("color", "");
            }

            function hover_in_skill_mastered(level, id) {
                for (let k = level; k >= 1; k--) {
                    $("#edit_star_" + id + "_" + k).css("color", "blueviolet");
                }
            }

            function hover_out_skill_mastered(level, id) {
                for (let k = level; k >= 1; k--) {
                    $("#edit_star_" + id + "_" + k).css("color", "");
                }
            }

            //display
            function display_stars(skill) { //display the stars (5stars)
                let html = "<span class='stars_" + skill.id + "'>";
                for (n = 1; n <= 5; n++) {
                    html += "<i onclick='update_mastering(" + n + "," + skill.id + ");' ";
                    html += "onmouseover='hover_in_skill_mastered(" + n + "," + skill.id + ");' ";
                    html += "onmouseout='hover_out_skill_mastered(" + n + "," + skill.id + ");' ";
                    html += "id='edit_star_" + skill.id + "_" + n + "' class='fa" + (n <= skill.level ? "s" : "r") + " fa-star'></i>";
                }
                html += "</span>"
                return html;
            }

            function display_row(skill) { //displaying the row with the skill mastered ,delete and stars
                let html = "";
                html += "<div class='masteringSkill'>";
                html += "<div class='mx-1  bg-info text-white skillsUser'>" + skill.name + "</div>";
                html += "<div class='buttons'><i  onclick='delete_mastering(" + skill.id + ");' skill='" + skill.id + "' class='delete_mastering far fa-trash-alt'></i>";
                html += display_stars(skill);
                html += "</div>";
                html += "</div>";
                return html;
            }

            function displayTable() { //display table with the skills mastered
                let html = "";
                let btn_html = $("")
                for (let skill of my_skills) {
                    html += display_row(skill);
                }
                $(".Skills").html(html);
            }

            function display_stars_empty() { //display the stars for the add skill
                let html = "";
                add_star = $("#add_star").html("");
                for (n = 1; n <= 5; n++) {
                    html += "<i onclick='add_mastering(" + n + ");' ";
                    html += "onmouseover='hover_in_skill_not_mastered(" + n + ");' ";
                    html += "onmouseout='hover_out_skill_not_mastered(" + n + ");' ";
                    html += "id='star_" + n + "' class='far fa-star'></i>";
                }
                $("#add_star").html(html);
            }
            //Ajax
            function add_mastering(level) {
                
                var skill_id = $("select[name='skill']").val();
                var skill_name = $("#myselect option:selected").text();
                $.ajax({
                    type: "POST",
                    url: "mastering/add_service/",
                    data: {
                        "level": level,
                        "skill": skill_id,
                        "user": member_id,
                    },
                    success: function(data) {
                        skill_added = {
                            "name": skill_name,
                            "level": level,
                            "id": skill_id
                        };
                        if(data=="true"){
                        my_skills.push(skill_added);
                        $('select').children('option[value="' + skill_id + '"]').remove();
                        $(".Skills").append(display_row(skill_added));
                        skills_menu();
                        }else {
                           alert_error("add");
                        }
                        
                    }
                })
            }

            function update_mastering(level, skill_id) {
                $.ajax({
                    type: "POST",
                    url: "mastering/update_service/",
                    data: {
                        "level": level,
                        "skill": skill_id,
                        "user": member_id,
                    },
                    success: function(data) {
                        
                        if(data=="true"){
                            skill = {
                            "level": level,
                            "id": skill_id
                        };
                            $(".stars_" + skill_id + "").html(display_stars(skill));
                        }else {
                            alert_error("update");
                        }
                        
                    }
                });
            }

            function delete_mastering(id) {

                $.ajax({
                    type: "POST",
                    url: "mastering/delete_service",
                    data: {
                        "skill": id,
                        "user": member_id
                    },
                    success: function(data) {
                        name = $("i[skill=" + id + "]").parent().parent().first().text();
                        if(data=="true"){
                            $("#myselect").append("<option  value='" + id + "'>" + name + "</option>")
                            $("i[skill=" + id + "]").parent().parent().remove();
                            skills_menu();
                        }else { 
                            alert_error("delete");
                            
                        }
                       
                    }
                });
            }
            function alert_error(feature){
            alert(feature+" mastering:Error !");
            }
        </script>
        <div class="Skills">
            <?php foreach ($masterings as $mastering) : ?>
                <div class="masteringSkill">
                    <form id="skill_<?= $mastering->get_skill()->id ?>" method="POST"><input type="hidden" name="skill_id" value="<?= $mastering->get_skill()->id ?>">
                    </form>
                    <div class="mx-1  bg-info text-white skillsUser">
                        <div><?= $mastering->get_skill()->name ?></div>
                        <div class="skillLevel"><?= $mastering->level ?></div>
                    </div>
                    <div class="buttons">
                        <button type="submit" class="btn btn-outline-info" form="skill_<?= $mastering->get_skill()->id ?>" formaction="mastering/level_up/<?= $member->id ?>"><i class="fas fa-sort-up"></i></button>
                        <button type="submit" class="btn btn-outline-info" form="skill_<?= $mastering->get_skill()->id ?>" formaction="mastering/level_down/<?= $member->id ?>"><i class="fas fa-sort-down"></i>
                        </button>
                        <button type="submit" class="btn btn-outline-danger" form="skill_<?= $mastering->get_skill()->id ?>" formaction="mastering/delete/<?= $member->id ?>"><i class="far fa-trash-alt"></i></button>
                        <?php if (count($errors) != 0 && isset($skill_id) && $skill_id == $mastering->get_skill()->id) : ?>
                            <span class='errors' style="border:none;">
                                error(s) :&nbsp;
                                <?php foreach ($errors as $error) : ?>
                                    -<?= $error ?></li>
                                <?php endforeach; ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <hr>
        <h3>Add a skill</h3>

        <form method="POST" class="addskill" id="add_skill" action="mastering/add/<?= $member->id ?>">
            <br>
            <div class="add_skillz" style="width: 20%;">
                <p>Skill</p>
                <select id="myselect" name="skill" form="add_skill">
                    <?php foreach ($skills as $skill) : ?>
                        <option <?= !empty($skill_selected) && $skill_selected->id === $skill->id ? "selected" : "" ?> value="<?= $skill->id ?>"><?= $skill->name ?></option>
                    <?php endforeach; ?>
                </select>
                <p>Level (1-5)</p>
                <div id="add_star">
                    <input form="add_skill" min="1" max="5" type="range" name="level" list="level"><br>
                    <datalist id="level">
                        <?php for ($nb = 1; $nb <= 5; $nb++) : ?>
                            <option value="<?= $nb ?>" label="<?= $nb ?>"></option>
                        <?php endfor; ?>
                    </datalist>
                    <br>
                    <input class="btn btn-outline-primary btn-lg btn-block " form="add_skill" type="submit" value="Add">
                </div>
            </div>
        </form>
    </div>


    <script src="https://code.jquery.com/jquery-3.2.1.min.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>

</html>