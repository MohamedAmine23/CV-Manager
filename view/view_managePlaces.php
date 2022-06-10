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
    <title>Manage places</title>
</head>

<body>
    <nav class="navbar navbar-expand-sm bg-primary navbar-dark d-flex justify-content-between flex-column flex-sm-row">
        <span class="navbar-brand text-white"><i class="fas fa-cheese m-1"></i>Munster.be</span>
        <ul class="nav navbar-nav navbar-right">
            <li class="nav-item"><a class="nav-link" href="mastering/">Skills</a></li>
            <li class="nav-item"><a class="nav-link" href="experience/experiences/">Experiences</a></li>
            <?php if ($user->role === "admin") : ?>
                <li class="nav-item"><a class="nav-link" href="Skill/manageSkills">Manage skills</a></li>
                <li class="nav-item"><a class="nav-link active" href="Place/managePlaces">Manage places</a></li>
                <li class="nav-item"><a class="nav-link" href="User/manageUsers">Manage users</a></li>
            <?php endif; ?>
            <li class="nav-item"><a class="btn loged nav-link" href="User/profile"><i class="fas fa-user-shield"></i>&nbsp;<?= $user->fullName ?></a></li>
            <li class="nav-item"><a class="btn login nav-link" href="user/logout"><i class="fa-sign-out-alt fas" aria-hidden="true"></i></a></li>
        </ul>
    </nav>
    <div class="container m-0">
        <h1>Manage places </h1>
        <table>
            <thead>
                <tr>
                    <!-- css th manageplace -->
                    <th style="width: 15%;">Name</th>
                    <th style="width: 15%;">City</th>
                    <th style="width: 10%;">Actions</th>
                    <th style="width: 15%;">Infos</th>
                    <th style="width: 45%;">&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($places as $place) : ?>
                    <tr>
                        <td>
                            <form hidden id="edit_<?= $place->id ?>" method="POST" action="place/edit"><input type="text" name="edit_id" value="<?= $place->id ?>" hidden></form>
                            <input form="edit_<?= $place->id ?>" type="text" class="form-control" name="edit_name" required value="<?= isset($name) && isset($edit_id) && $edit_id === $place->id ? $name : $place->name ?>">
                        </td>
                        <td>
                            <input form="edit_<?= $place->id ?>" type="text" class="form-control" name="edit_city" required value="<?= isset($city) && isset($edit_id) && $edit_id === $place->id ? $city : $place->city ?>">
                        </td>
                        <td>
                            <button form="edit_<?= $place->id ?>" type="submit" class="btn btn-outline-primary">
                                <i class="far fa-save"></i>
                            </button>
                            <a href="place/request_delete/<?= $place->id ?>" class="btn btn-outline-danger">
                                <i class="far fa-trash-alt"></i>
                            </a>
                        </td>
                        <td>
                            used in <?= $place->nb_experiences_used_place() ?> experience<?= $place->nb_experiences_used_place() > 1 ? "s" : "" ?>
                        </td>
                        <td>
                            <?php if (count($errors) != 0 && isset($edit_id) && $edit_id === $place->id) : ?>
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
                        <form id="add" method="POST" action="place/add"></form>
                        <input form="add" type="text" class="form-control" name="name" placeholder="New place" value="<?= $addName ?>" required>
                    </td>
                    <td><input form="add" type="text" class="form-control" name="city" placeholder=" City" value="<?= $addCity ?>" required></td>
                    <td><button form="add" type="submit" class="btn btn-outline-primary"><i class="fas fa-plus"></i></button></td>
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

    <script src="https://code.jquery.com/jquery-3.2.1.min.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>

</html>