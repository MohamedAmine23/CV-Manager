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
    <title>Change Password</title>
  </head>
  <body>
    <nav class="navbar navbar-expand-sm bg-primary navbar-dark d-flex justify-content-between flex-column flex-sm-row">
        <span class="navbar-brand text-white" ><i class="fas fa-cheese m-1"></i>Munster.be</span>    
        <ul class="nav navbar-nav navbar-right">
            <li class="nav-item nav-link active">Change Password</li>
            <li class="nav-item"><a class="nav-link" href="mastering/">Skills</a></li>
            <li class="nav-item"><a class="nav-link" href="experience/experiences/">Experiences</a></li>
            <?php if($user->role==="admin"): ?>
                <li class="nav-item"><a class="nav-link" href="Skill/manageSkills">Manage skills</a></li>
                <li class="nav-item"><a class="nav-link" href="Place/managePlaces">Manage places</a></li>
                <li class="nav-item"><a class="nav-link" href="User/manageUsers">Manage users</a></li>
            <?php endif; ?>
            <li class="nav-item"><a class="btn loged nav-link" href="User/profile"><i class="fas fa-user-shield"></i>&nbsp;<?=$user->fullName?></a></li>
            <li class="nav-item"><a class="btn login nav-link" href="user/logout"><i class="fa-sign-out-alt fas" aria-hidden="true"></i></a></li>
        </ul>
    </nav>
    <div class="container ">
        <div class="col-12 d-flex justify-content-center p-3">
            <form class="LoginForm card p-3" action="user/password_change" method="POST">
                <input  type="text" name="member_id" value="<?=$member->id?>" hidden>
                <h5 class="text-center">Change <?=$member->fullName ?>'s Password</h5>
                <hr>
                <!--- Password Field -->
                <div class="form-group">
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <div class="input-group-text h-100"><i class="fas fa-lock"></i></div>
                        </div>
                        <input required autofocus type="password" class="form-control" id="PasswordSignUp" name="password" placeholder="Password" value="<?= $password ?>" >
                    </div>
                </div>
                <!--- Password Confirm Field -->
                <div class="form-group">
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <div class="input-group-text h-100"><i class="fas fa-lock"></i></div>
                        </div>
                        <input required type="password" class="form-control" id="ConfirmPasswordSignUp" name="password_confirm" placeholder="Confirm your password" value="<?= $password_confirm?>">
                    </div>
                </div>
                <!-- Button -->
                <input type="submit" class="btn btn-primary" value="Confirm Change">
            </form>
        </div>    
    </div>
    <?php if (count($errors) != 0): ?>
        <div class='errors'>
            <p>Please correct the following error(s) :</p>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= $error ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <!-- Option 2: Separate Popper and Bootstrap JS -->
    <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>
    -->
  </body>
</html>