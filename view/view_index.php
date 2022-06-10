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
  <title>Munster.be</title>
</head>

<body>
  <nav class="navbar navbar-light bg-primary">
    <span class="navbar-brand text-white"><i class="fas fa-cheese m-1"></i>Munster.be</span>
    <div class="form-inline nav-log my-2 my-lg-0">
      <a class="btn login" href="user/login"><i class="fa-sign-out-alt fas" aria-hidden="true"></i></a>
      <a class="btn signup" href="user/signup"><i class="fa-user-plus fas" aria-hidden="true"></i></a>
    </div>
  </nav>
  <div class="container-fluid">
    <div class="col-12">
      <p class="logUp text-black-50 m-4"> Hello guest! Please <a class="login" href="user/login"> login </a> or <a class="signup" href="user/signup">signup</a>.</p>
    </div>
  </div>

  <!-- Option 1: Bootstrap Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

  <!-- Option 2: Separate Popper and Bootstrap JS -->
  <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>
    -->
</body>

</html>