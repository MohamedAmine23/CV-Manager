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
    <nav class="navbar navbar-expand-sm bg-primary navbar-dark d-flex justify-content-between flex-column flex-sm-row">
        <span class="navbar-brand text-white" ><i class="fas fa-cheese m-1"></i>Munster.be</span>   
        <ul class="nav navbar-nav navbar-right">
            <li class="nav-item"><a class="nav-link" href="mastering/">Skills</a></li>
            <li class="nav-item"><a class="nav-link" href="experience/experiences/">Experiences</a></li>
            <?php if($user->role==="admin"): ?>
                <li class="nav-item"><a class="nav-link" href="Skill/manageSkills">Manage skills</a></li>
                <li class="nav-item"><a class="nav-link" href="Place/managePlaces">Manage places</a></li>
                <li class="nav-item"><a class="nav-link" href="User/manageUsers">Manage users</a></li>
            <?php endif; ?>
            <li class="nav-item"><a class="btn loged nav-link active" href="User/profile"><i class="fas fa-user-shield"></i>&nbsp;<?=$user->fullName?></a></li>
            <li class="nav-item"><a class="btn login nav-link" href="user/logout"><i class="fa-sign-out-alt fas" aria-hidden="true"></i></a></li>
        </ul>
    </nav>
    <div class="container m-0">  
        <div class="col-12">    
            <div class="card-header">
                <h5 class="logUp text-black-50 m-2"><?=$user->fullName?></h5>
            </div>
            <table >
                <thead class="user">
                    <tr>
                        <th>Mail</th>
                        <th>Fullname</th>
                        <th>Title</th>
                        <th>Birthdate</th>
                        <th>Role</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody class="tabUser">
                    <tr>
                        <td>
                            <form hidden id="edit_<?=$user->id ?>" method="POST" action="user/edit_profile"></form>
                            <input form="edit_<?=$user->id ?>" type="text" class="form-control" name="edit_mail"  
                            value="<?=isset($mail)?$mail:$user->mail?>">
                        </td>
                        <td>
                            <input form="edit_<?=$user->id?>" type="text" class="form-control" name="edit_fullName" 
                            value="<?=isset($fullName)?$fullName:$user->fullName?>">
                        </td>
                        <td>
                            <input form="edit_<?=$user->id?>" type="text" class="form-control" name="edit_title" 
                            value="<?=isset($title)?$title:$user->title?>">
                        </td>
                        <td>
                            <input form="edit_<?=$user->id?>" type="date" class="form-control" name="edit_birthdate" 
                            value="<?=isset($birthdate)?$birthdate:$user->birthdate?>">
                        </td>
                        <td>
                            <input disabled  type="text" class="form-control" value="<?=$user->role?>" >
                        </td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-primary dropdown-toggle" type="button" id="dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Edit </button>
                                <div class="dropdown-menu">
                                <button class="dropdown-item" form="edit_<?= $user->id ?>" type="submit">Save</button> 
            
                                <a class="dropdown-item" href="User/Password/<?=$user->id?>">Change Password</a>

                                </div>
                            </div>
                        </td>                            
                    </tr>
                </tbody> 
            </table>   
        </div>
    </div>
    <br>
    <?php if (count($errors) != 0): ?>
            <div class='errors'  style="width:60%">
                <p>Please correct the following error(s) :</p>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        
        <?php endif; ?>
    
    <script src="https://code.jquery.com/jquery-3.2.1.min.js"  crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
  </body>
</html>