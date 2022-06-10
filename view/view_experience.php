<!doctype html>
<html lang="en">

<head>
    <base href="<?= $web_root ?>">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://kit.fontawesome.com/3eef553146.js" crossorigin="anonymous"></script>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link href="css/style.css" rel="stylesheet" type="text/css">
    <link rel="icon" href="img/favicon.ico">
    <title><?= $member->fullName . "'s experiences" ?></title>
</head>

<body>

<nav class="navbar navbar-expand-sm bg-primary navbar-dark d-flex justify-content-between flex-column flex-sm-row">
    <span class="navbar-brand text-white"><i class="fas fa-cheese m-1"></i>Munster.be</span>
    <ul class="nav navbar-nav navbar-right">
        <?php if ($mode_view == "add"): ?>
            <li class="nav-item nav-link active">New experience</li>
        <?php elseif ($mode_view == "edit"): ?>
            <li class="nav-item nav-link active">Experience "<?= $experience->title ?>"</li>
        <?php endif; ?>
        <li class="nav-item"><a class="nav-link" href="mastering/">Skills</a></li>
        <li class="nav-item"><a class="nav-link" href="experience/experiences/">Experiences</a></li>
        <?php if ($user->role === "admin") : ?>
            <li class="nav-item"><a class="nav-link" href="Skill/manageSkills">Manage skills</a></li>
            <li class="nav-item"><a class="nav-link" href="Place/managePlaces">Manage places</a></li>
            <li class="nav-item"><a class="nav-link" href="User/manageUsers">Manage users</a></li>
        <?php endif; ?>
        <li class="nav-item"><a class="btn loged nav-link" href="User/profile"><i
                        class="fas fa-user-shield"></i>&nbsp;<?= $user->fullName ?></a></li>
        <li class="nav-item"><a class="btn login nav-link" href="user/logout"><i class="fa-sign-out-alt fas"
                                                                                 aria-hidden="true"></i></a></li>
    </ul>
</nav>
<div class="container m-0">
    <?php if ($mode_view == "add"): ?>
    <h2>Add a new experience for <?= $member->fullName; ?></h2>
    <form id="experienceForm" method="POST" style="margin:5px;" onsubmit="return validateForm()" action="experience/add/<?= $member->id ?>">
        <?php elseif ($mode_view == "edit"): ?>
        <h2>Edit experience "<?= $experience->title ?>" </h2>
        <form id="experienceForm" method="POST" style="margin:5px;" onsubmit="return validateForm()"
              action="experience/edit/<?= $experience->id ?>/<?= $member->id ?>">
            <?php endif; ?>
            <!-- css  -->
            <input id="member" type="text" hidden name="member" value="<?= $member->id ?>"><br>

            <div>
                <p> &nbsp;Start date :</p>
                <input type="date" oninput="validateStartDate();" onchange="validateDate()" class="form-control"
                       id="start_date" name="start_date" required value="<?= $start ?>">
                <!--<span id="error_start"></span>-->
            </div>
            <br>
            <div>
                <p> &nbsp;End date (optional):</p>
                <input type="date" oninput="validateStopDate();" onchange="validateDate()" class="form-control"
                       id="stop_date" name="stop_date" value="<?= $stop ?>">
                <!--<span id="error_stop"></span>-->
            </div>
            <br>
            <div>
                <p> &nbsp;Title</p>
                <input type="text" oninput="validateTitle();" class="form-control" id="title" name="title" required
                       value="<?= $title ?>">
                <!--<span id="error_title"></span>-->
            </div>
            <br>
            <div>
                <p> &nbsp;Description(optional):</p>
                <textarea onkeyup="totalCharsDescr();" class="form-control" id="description" name="description"
                          rows="3"><?= $description ?></textarea>
                <!--<span id="error_descr"></span>-->
                <div>Total Characters:
                    <span id="totalChars"><?= ($mode_view == "add") ? "0" : "JavaScript not activated" ?>
                    </span><span id="maxSize"></span>
                </div>
            </div>
            <br>
            <div>
                <p> &nbsp;Place:</p>
                <select class="form-control select_view " name="place">
                    <?php foreach ($cities as $city) : ?>
                        <optgroup label=<?= $city; ?>>
                            <?php foreach ($places as $place) : ?>
                                <?php if ($city === $place->city) : ?>
                                    <option value="<?= $place->id ?>" <?= !empty($place_selected) && $place_selected->id === $place->id ? "selected" : "" ?>><?= $place->name ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </optgroup>
                    <?php endforeach; ?>
                </select>
            </div>
            <br>
            <div>
                <p>&nbsp;Skills used</p>
                <div class="checkboxes_skills_container">
                    <?php foreach ($skills_used as $skill) : ?>
                        <input class="checkbox_skill" type="checkbox"
                               name="skill_<?= $skill->id ?>" <?= !empty($skills_checked) && in_array($skill, $skills_checked) ? " checked " : " "; ?>
                               value=<?= $skill->id ?>>
                        <?= $skill->name ?>
                    <?php endforeach; ?>
                </div>
            </div>
            <br>
            <?php if ($mode_view == "add"): ?>
                <input type="submit" class="btn btn-primary btn_add_experience" value="ADD">
            <?php elseif ($mode_view == "edit"): ?>
                <div class="btn_experience_container">
                    <input type="submit" class="btn btn-primary" style="margin:10px;" value="SAVE"><a
                            class="btn btn-primary" href="experience">CANCEL</a>
                </div>
            <?php endif; ?>
        </form>
        <?php if (count($errors) != 0) : ?>
            <div class='errors errors_container'>
                <p>Please correct the following error(s) :</p>
                <ul>
                    <?php foreach ($errors as $error) : ?>
                        <li><?= $error ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
</div>
<script src="https://code.jquery.com/jquery-3.2.1.min.js" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
        integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
        crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
        crossorigin="anonymous"></script>
<script src="lib/just-validate.production.patched.min.js" type="text/javascript"></script>
<script src="lib/just-validate-plugin-date.production.min.js" type="text/javascript"></script>
<script src="view/experience_validation.js"></script>
<script>
    $(document).ready(function () {
        totalCharsDescr();
        validationPlugin();
    });
</script>
</body>

</html>