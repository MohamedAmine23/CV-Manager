<!doctype html>
<html lang="en">
<head>
    <base href="<?= $web_root ?>">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://kit.fontawesome.com/3eef553146.js" crossorigin="anonymous"></script>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
          integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link href="css/style.css" rel="stylesheet" type="text/css">
    <link rel="icon" href="img/favicon.ico">
    <title>Manage Users </title>
</head>
<body>
<nav class="navbar navbar-expand-sm bg-primary navbar-dark d-flex justify-content-between flex-column flex-sm-row">
    <span class="navbar-brand text-white"><i class="fas fa-cheese m-1"></i>Munster.be</span>
    <ul class="nav navbar-nav navbar-right">
        <li class="nav-item"><a class="nav-link" href="mastering/">Skills</a></li>
        <li class="nav-item"><a class="nav-link" href="experience/experiences/">Experiences</a></li>
        <?php if ($user->role === "admin"): ?>
            <li class="nav-item"><a class="nav-link" href="Skill/manageSkills">Manage skills</a></li>
            <li class="nav-item"><a class="nav-link" href="Place/managePlaces">Manage places</a></li>
            <li class="nav-item"><a class="nav-link active" href="User/manageUsers">Manage users</a></li>
        <?php endif; ?>
        <li class="nav-item"><a class="btn loged nav-link" href="User/profile"><i
                        class="fas fa-user-shield"></i>&nbsp;<?= $user->fullName ?></a></li>
        <li class="nav-item"><a class="btn login nav-link" href="user/logout"><i class="fa-sign-out-alt fas"
                                                                                 aria-hidden="true"></i></a></li>
    </ul>
</nav>
<div class="container m-0">
    <h1>Manage Users <?= empty($skill_selected) ? "" : "($skill_selected->name)" ?></h1>
    <table>
        <thead class="user">
        <tr>
            <th>Mail</th>
            <th>Fullname</th>
            <th>Title</th>
            <th>Birthdate</th>
            <th>Role</th>
            <th>Action</th>
            <th>&nbsp;</th>
        </tr>
        </thead>
        <tbody class="tabUser">
        <?php foreach ($members as $member): ?>
            <tr>
                <!-- Mail -->
                <td>
                    <form hidden id="edit_<?= $member->id ?>" method="POST" action="user/member_edit"><input type="text"
                                                                                                             name="edit_id"
                                                                                                             value="<?= $member->id ?>"
                                                                                                             hidden>
                    </form>
                    <input form="edit_<?= $member->id ?>" type="text" class="form-control" name="edit_mail" required
                           value="<?= isset($mail) && isset($edit_id) && $edit_id === $member->id ? $mail : $member->mail ?>">
                </td>
                <!-- fullname -->
                <td>
                    <input form="edit_<?= $member->id ?>" type="text" class="form-control" name="edit_fullName" required
                           value="<?= isset($fullName) && isset($edit_id) && $edit_id === $member->id ? $fullName : $member->fullName ?>">
                </td>
                <!--title  -->
                <td>
                    <input form="edit_<?= $member->id ?>" type="text" class="form-control" name="edit_title" required
                           value="<?= isset($title) && isset($edit_id) && $edit_id === $member->id ? $title : $member->title ?>">
                </td>
                <!-- birthdate  -->
                <td>
                    <input form="edit_<?= $member->id ?>" type="date" class="form-control" name="edit_birthdate"
                           required
                           value="<?= isset($birthdate) && isset($edit_id) && $edit_id === $member->id ? $birthdate : $member->birthdate ?>">
                </td>
                <!-- Role -->
                <td>
                    <select <?= $member->id === $user->id ? "disabled" : "" ?>
                            form="edit_<?= $member->id ?>" class="form-control select_view " name="edit_role">
                        <option <?= $member->role === "admin" ? " selected " : "" ?>value="admin">admin</option>
                        <option <?= $member->role === "user" ? " selected " : "" ?> value="user">user</option>
                    </select>
                </td>
                <!-- Actions -->
                <td>
                    <div class="dropdown">
                        <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">Edit
                        </button>
                        <div class="dropdown-menu">
                            <button class="dropdown-item" form="edit_<?= $member->id ?>" type="submit">Save</button>
                            <a class="dropdown-item" href="experience/experiences/<?= $member->id ?>">Show experiences
                                (<?= $member->nb_experiences() ?>)</a>
                            <a class="dropdown-item" href="mastering/skills_mastered/<?= $member->id ?>">Show Skills
                                (<?= $member->nb_skills_mastered() ?>)</a>
                            <a class="dropdown-item" href="User/Password/<?= $member->id ?>">Change Password</a>
                            <?php if ($member->id !== $user->id): ?>
                                <a class="dropdown-item" href="user/request_delete/<?= $member->id ?>">Delete</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </td>
                <!-- Errors -->
                <td>
                    <?php if (count($errors) != 0 && isset($edit_id) && $edit_id === $member->id): ?>
                        <span class='errors' style="border:none;">
                                    error(s) :&nbsp;
                                    <?php foreach ($errors as $error): ?>
                                        -<?= $error ?></li>
                                    <?php endforeach; ?>   
                                </span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?= empty($members) ? " user not found " : "" ?>
    <?php if (count($errors) != 0 && isset($edit_id) && $edit_id === $member->id): ?>
        <span class='errors' style="border:none;">
                error(s) :&nbsp;
                <?php foreach ($errors as $error): ?>
                    -<?= $error ?></li>
                <?php endforeach; ?>   
            </span>
    <?php endif; ?>
    <br>
    <h1>Filter users by skill </h1>
    <form class="filter_skill" method="POST" id="filter_skills" action="user/filter_by_skills">
        <select name="filter" form="filter_skills">
            <?php foreach ($skills as $skill): ?>
                <option <?= !empty($skill_selected) && $skill_selected->id === $skill->id ? "selected" : "" ?>
                        value="<?= $skill->id ?>"><?= $skill->name ?></option>
            <?php endforeach; ?>
        </select>
        <input type="submit" name="submit" class="btn btn-primary" value="Filter">
        <input type="submit" name="reset" class="btn btn-secondary" value="Reset">
    </form>
</div>
<script src="https://code.jquery.com/jquery-3.2.1.min.js"
        crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
        integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
        crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
        crossorigin="anonymous"></script>
</body>
</html>