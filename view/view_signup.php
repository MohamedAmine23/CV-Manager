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
    <title>Signup</title>
  </head>
  <body>
      
    <nav class="navbar navbar-light bg-primary">
        <a class="navbar-brand text-white" href="#">
        <i class="fas fa-cheese m-1"></i>
            Munster.be
        </a>
        <div class="form-inline nav-log my-2 my-lg-0">
            <a class="btn login" href="user/login"><i class="fa-sign-out-alt fas" aria-hidden="true"></i></a>
            <a class="btn signup text-white" href="user/signup"><i class="fa-user-plus fas" aria-hidden="true"></i></a>
        </div>
    </nav>

    <div class="container ">
        <div class="col-12 d-flex justify-content-center p-3">
            <form class="LoginForm card p-3" action="user/signup" method="POST">
                <h5 class="text-center">Sign Up</h5>
                <hr>
                <!--- EMail Field -->
                <div class="form-group">
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <div class="input-group-text h-100"><i class="fas fa-at"></i></div>
                        </div>
                        <input   type="email" class="form-control" name="mail" placeholder="Email" value="<?= $mail ?>" required>
                      
                    </div>
                </div>
                <!--- FullName Field --> 
                <div class="form-group">
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <div class="input-group-text h-100"><i class="fas fa-user"></i></div>
                        </div>
                        <input type="text" class="form-control"  name="fullName" placeholder="Full Name" value="<?= $fullName ?>" required>
                    </div>
                </div>
                <!--- Title Field --> 
                <div class="form-group">
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <div class="input-group-text h-100"><i class="fab fa-redhat"></i></div>
                        </div>
                        <input type="text" class="form-control"  name="title" placeholder="Title" value="<?= $title ?>" required>
                    </div>
                </div>
                <!--- Birthdate Field -->
                <div class="form-group">
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <div class="input-group-text h-100"><i class="fas fa-baby-carriage"></i></div>
                        </div>
                        <input type="date" class="form-control"  name="birthdate"  value="<?= $birthdate ?>" required>
                    </div>
                </div>
                <!--- Password Field -->
                <div class="form-group">
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <div class="input-group-text h-100"><i class="fas fa-lock"></i></div>
                        </div>
                        <input type="password" class="form-control" id="PasswordSignUp" name="password" placeholder="Password" value="<?= $password ?>" required>
                    </div>
                </div>
                <!--- Password Confirm Field -->
                <div class="form-group">
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <div class="input-group-text h-100"><i class="fas fa-lock"></i></div>
                        </div>
                        <input type="password" class="form-control" id="ConfirmPasswordSignUp" name="password_confirm" placeholder="Confirm your password" value="<?= $password_confirm?>" required>
                    </div>
                </div>
                <!-- Button -->
                <input type="submit" class="btn btn-primary" value="SIGN UP">
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