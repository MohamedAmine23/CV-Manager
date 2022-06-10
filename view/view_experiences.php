<!doctype html>
<html lang="en">
    <head>
        <base href="<?=$web_root?>">
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <script src="https://kit.fontawesome.com/3eef553146.js" crossorigin="anonymous"></script>
        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
        <link href="css/style.css" rel="stylesheet" type="text/css">
        <link rel="icon" href="img/favicon.ico">
        <!-- jquery ui libairy -->
        <link href="lib/jquery-ui-1.13.1/jquery-ui.min.css" rel="stylesheet" type="text/css"/>
        <link href="lib/jquery-ui-1.13.1/jquery-ui.theme.min.css" rel="stylesheet" type="text/css"/>
        <link href="lib/jquery-ui-1.13.1/jquery-ui.structure.min.css" rel="stylesheet" type="text/css"/>
        <script src="lib/jquery-3.6.0.min.js" type="text/javascript"></script>
        <script src="lib/jquery-ui-1.13.1/jquery-ui.min.js" type="text/javascript"></script>


        <title><?=$member->fullName."'s experiences"?></title>
    </head>
    <body>
        <nav class="navbar navbar-expand-sm bg-primary navbar-dark d-flex justify-content-between flex-column flex-sm-row">
            <span class="navbar-brand text-white" ><i class="fas fa-cheese m-1"></i>Munster.be</span>
            <ul class="nav navbar-nav navbar-right">
                <li class="nav-item"><a class="nav-link" href="mastering/">Skills</a></li>
                <li class="nav-item"><a class="nav-link active" href="experience/experiences">Experiences</a></li>
                <?php if($user->role==="admin"): ?>
                    <li class="nav-item"><a class="nav-link" href="Skill/manageSkills">Manage skills</a></li>
                    <li class="nav-item"><a class="nav-link" href="Place/managePlaces">Manage places</a></li>
                    <li class="nav-item"><a class="nav-link" href="User/manageUsers">Manage users</a></li>
                <?php endif; ?>
                <li class="nav-item"><a class="btn loged nav-link" href="User/profile"  ><i class="fas fa-user-shield"></i>&nbsp;<?=$user->fullName?></a></li>
                <li class="nav-item"><a class="btn login nav-link" href="user/logout"><i class="fa-sign-out-alt fas" aria-hidden="true"></i></a></li>
            </ul>
        </nav>
        <div class="container m-0">
            <h1><?=$member->fullName?>,<?=$user->title?></h1>
            <div class="pull-right">
                <a href="experience/calendar/<?= $member->id; ?>" class="btn btn-outline-primary">List calendar</a>
            </div>
            <div id="filter"></div>
            <h2>Experiences</h2>
            <p>Skills colored in lightblue are those used in your experiences but which are not yet in your skills list</p>
            <?php if(count($experiences)>0): ?>
                <?php foreach($experiences as $experience): ?>
                <div class="row" member="<?=$member->id?>" experience="<?=$experience->id?>">
                    <input type="checkbox" id="experiences_view_<?=$experience->id?>" style="display:none;"><!-- css  -->
                    <label class="experience_details" for="experiences_view_<?=$experience->id?>"><?=$experience->title." at ".$experience->get_place()->name."(".$experience->get_place()->city.")"?></label>
                    <div class="details" >
                        <?=$experience->title." at ".$experience->get_place()->name."(".$experience->get_place()->city.") from ". date_format(date_create($experience->start),"M Y") ?>
                        <?=empty($experience->stop)? "":" to ".date_format(date_create($experience->stop),"M Y") ?><br>
                        <?php if(!empty($experience->description)):?>
                            <i><?=$experience->description ?></i>
                                <br>
                        <?php endif; ?>
                            Skills used <br>
                                <?php foreach($skills_used as $used): ?>
                                    <?php if($used->get_experience()->id==$experience->id): ?>
                                        <span class="skill_tag<?=in_array($used->get_skill(),$member->get_my_skills_not_mastered())?" bg_blue":" bg_violet"?>" experience="<?=$experience->id?>" skill="<?=$used->get_skill()->id?>"><?=$used->get_skill()->name ?></span>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <br>
                        <div style="text-align: end;">
                            <a class="btn btn-outline-primary" href="experience/edit/<?=$experience->id?>/<?=$member->id?>">Edit</a>&nbsp;&nbsp;<a class="btn  btn-outline-danger"  href="experience/request_delete/<?=$experience->id?>/<?=$member->id?>">DELETE</a></div><!-- css  -->
                        </div>
                    </div>
                <?php endforeach; ?>
                <div id="filter_message"></div>
            <?php else :?>
                <p><?=$member->fullName;?> doesn't have any experiences. </p>
            <?php endif; ?>
            <br><br>
            <div class="add_experience ">
                <a class="btn btn-outline-primary btn-lg btn-block " style="width:100%;" href="experience/add/<?=$member->id?>">ADD A NEW EXPERIENCE</a>
            </div>
        </div>
        <div id="confirmDialogDeleteXP" title="Are you sure ?" hidden>
                <p>
                    Do you really want to delete experience&nbsp;"<span id="message_to_delete_xp"></span>
                    &nbsp;of&nbsp;<?=$member->fullName?> &nbsp;and all of its dependencies?
                    <br><br>
                    This process cannot be undone.
                </p>
        </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
        <script src="view/experience_validation.js"></script>


    <script>
        $(function() {
            clickable_skills(<?=$member->id?>);
            skill_hover();
            init_modal_dialog();
            console.log(getSliderConfig());
            if(getSliderConfig()=="true"){
                sliderFilter(<?=$member->id?>);
            }else if(getSliderConfig()=="false"){
                simpleFilter(<?=$member->id?>);
            }
        });
    </script>

  </body>
</html>
