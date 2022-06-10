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
    <title>Manage skills</title>
</head>

<body>
    <nav class="navbar navbar-expand-sm bg-primary navbar-dark d-flex justify-content-between flex-column flex-sm-row">
        <span class="navbar-brand text-white"><i class="fas fa-cheese m-1"></i>Munster.be</span>
        <ul class="nav navbar-nav navbar-right">
            <li class="nav-item"><a class="nav-link" href="mastering/">Skills</a></li>
            <li class="nav-item"><a class="nav-link" href="experience/experiences/">Experiences</a></li>
            <?php if ($user->role === "admin") : ?>
                <li class="nav-item"><a class="nav-link active" href="Skill/manageSkills">Manage skills</a></li>
                <li class="nav-item"><a class="nav-link" href="Place/managePlaces">Manage places</a></li>
                <li class="nav-item"><a class="nav-link" href="User/manageUsers">Manage users</a></li>
            <?php endif; ?>
            <li class="nav-item"><a class="btn loged nav-link" href="User/profile"><i class="fas fa-user-shield"></i>&nbsp;<?= $user->fullName ?></a></li>
            <li class="nav-item"><a class="btn login nav-link" href="user/logout"><i class="fa-sign-out-alt fas" aria-hidden="true"></i></a></li>
        </ul>
    </nav>
    <div class="container m-0">
        <h1>Manage skills </h1>
        <table>
            <thead>
                <tr>
                    <th style="width: 20%;">Name</th>
                    <th style="width: 10%;">Actions</th>
                    <th>Infos
                    <th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($skills as $skill) : ?>
                    <tr>
                        <td>
                            <form id="edit_<?= $skill->id ?>" action="skill/edit" method="POST" hidden><input type="hidden" name="edit_id" value="<?= $skill->id ?>"></form>
                            <input required form="edit_<?= $skill->id ?>" class="form-control" type="text" name="edit_name" value="<?= isset($name) && isset($edit_id) && $edit_id === $skill->id ? $name : $skill->name ?>">
                        </td>
                        <td>
                            <button type="submit" form="edit_<?= $skill->id ?>" formaction="skill/edit" class="btn btn-outline-primary"><i class="far fa-save"></i></button>
                            <!--add method in controller-->
                            <a href="skill/request_delete/<?= $skill->id ?>" class="btn btn-outline-danger"><i class="far fa-trash-alt"></i></a>

                        </td>
                        <td>
                            <p>mastered by <?= $skill->count_mastered_by() ?> user<?= $skill->count_mastered_by() > 1 ? "s" : "" ?>,
                                used in <?= $skill->count_skills_in_exp() ?> experience<?= $skill->count_skills_in_exp() > 1 ? "s" : "" ?>
                                <!--add foreach count in mastering and users-->
                            </p>
                        </td>

                        <td>
                            <?php if (count($errors) != 0 && isset($edit_id) && $edit_id === $skill->id) : ?>
                                <span class='errors errors_managePlaces' style="border:none;">
                                    <!-- css  errors places-->
                                    error(s) :&nbsp;
                                    <?php foreach ($errors as $error) : ?>
                                        -<?= $error ?></li>
                                    <?php endforeach; ?>
                                </span>
                            <?php endif; ?>
                        </td>

                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td>
                        <form hidden id="add" method="POST" action="skill/index"></form>
                        <input form="add" type="text" class="form-control" name="name" placeholder="New skill" value="<?= $addName ?>" required>
                    </td>
                    <td><button form="add" type="submit" formaction="skill/index" class="btn btn-outline-primary"><i class="fas fa-plus"></i></button></td>
                    <td></td>
                    <td>
                        <?php if (count($errors) != 0 && !isset($name)) : ?>
                            <!-- css errors places -->
                            <span class='errors' style="border: none;">
                                error(s) :&nbsp;
                                <?php foreach ($errors as $error) : ?>
                                    -<?= $error ?>
                                <?php endforeach; ?>
                            </span>
                        <?php endif; ?>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</body>

</html>