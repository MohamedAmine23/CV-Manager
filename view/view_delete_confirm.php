<!doctype html>
<html lang="en">

<head>
    <base href="<?= $web_root ?>">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://kit.fontawesome.com/3eef553146.js" crossorigin="anonymous"></script>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link href="css/style.css" rel="stylesheet" type="text/css">
    <link rel="icon" href="img/favicon.ico">
    <title>Delete&nbsp;<?= $delete_class ?></title>
</head>

<body>
    <nav class="navbar navbar-expand-sm bg-primary navbar-dark d-flex justify-content-between flex-column flex-sm-row">
        <span class="navbar-brand text-white"><i class="fas fa-cheese m-1"></i>Munster.be</span>
        <ul class="nav navbar-nav navbar-right">
            <li class="nav-item nav-link active">Delete</li>
            <li class="nav-item"><a class="nav-link" href="mastering/">Skills</a></li>
            <li class="nav-item"><a class="nav-link" href="experience/experiences/">Experiences</a></li>
            <?php if ($user->role === "admin") : ?>
                <li class="nav-item"><a class="nav-link" href="Skill/manageSkills">Manage skills</a></li>
                <li class="nav-item"><a class="nav-link " href="Place/managePlaces">Manage places</a></li>
                <li class="nav-item"><a class="nav-link" href="User/manageUsers">Manage users</a></li>
            <?php endif; ?>
            <li class="nav-item"><a class="btn loged nav-link" href="User/profile"><i class="fas fa-user-shield"></i>&nbsp;<?= $user->fullName ?></a></li>
            <li class="nav-item"><a class="btn login nav-link" href="user/logout"><i class="fa-sign-out-alt fas" aria-hidden="true"></i></a></li>
        </ul>
    </nav>

    <div class="container">
        <div class="container_delete">
            <form hidden id="delete_<?= $field->id ?>" method="POST" action="<?= $delete_class ?>/delete">
                <input type="text" name="delete_id" value="<?= $field->id ?>" hidden>
                <?php if ($delete_class == "experience") : ?>
                    <input type="text" name="member_id" value="<?= $member->id ?>" hidden>
                <?php endif; ?>
            </form>
            <div class="text-center txt_delete">
                <i class="far fa-trash-alt fa-3x"></i>
                <h3>Are you sure ?</h3>
                <hr>
                <p>
                    Do you really want to delete <?= $delete_class ?>&nbsp;"<?= $delete_element ?>"
                    <?= $delete_class == "experience" ? " of " . $member->fullName : "" ?> and all of its dependencies?
                    <br><br>
                    This process cannot be undone.
                </p>
            </div>
            <div class="text-center">
                <a href="<?= $delete_class ?>/<?= $delete_class == "experience" ? $delete_class . "s/" . $member->id : "manage" . $delete_class . "s/" ?>" class="btn btn-secondary">Cancel</a>
                <input form="delete_<?= $field->id ?>" type="submit" class="btn btn-danger" value="Delete">
            </div>
        </div>
    </div>
</body>

</html>