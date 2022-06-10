<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset="UTF-8">
    <title>Calendar</title>
    <base href="<?= $web_root ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/3eef553146.js" crossorigin="anonymous"></script>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link href="css/style.css" rel="stylesheet" type="text/css">
    <link rel="icon" href="img/favicon.ico">
    <title><?= $member->fullName . "'s calendar" ?></title>
    <script src="lib/jquery-3.6.0.min.js" type="text/javascript"></script>
    <!--    <link href='lib/fullcalendar-5.10.2/lib/main.css' rel='stylesheet' />
        <script src='lib/fullcalendar-5.10.2/lib/main.js'></script>-->
    <link href='lib/fullcalendar-scheduler-5.11.0/lib/main.min.css' rel='stylesheet'/>
    <script src='lib/fullcalendar-scheduler-5.11.0/lib/main.min.js'></script>

</head>
<body>
<nav class="navbar navbar-expand-sm bg-primary navbar-dark d-flex justify-content-between flex-column flex-sm-row">
    <span class="navbar-brand text-white"><i class="fas fa-cheese m-1"></i>Munster.be</span>
    <ul class="nav navbar-nav navbar-right">
        <li class="nav-item"><a class="nav-link" href="mastering/">Skills</a></li>
        <li class="nav-item"><a class="nav-link active" href="experience/experiences">Experiences</a></li>
        <?php if ($user->role === "admin"): ?>
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
    <h1><?= $member->fullName ?>,<?= $user->title ?></h1>
    <div class="pull-right">
        <a href="experience/experiences" class="btn btn-outline-primary">List view</a>
    </div>
    <h2>Experiences</h2>
</div>

<div style="Margin:10px" id="calendar"></div>
</body>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var calendarEl = $("#calendar")[0];
        var calendar = new FullCalendar.Calendar(calendarEl, {
            schedulerLicenseKey: 'CC-Attribution-NonCommercial-NoDerivatives',
            initialView: 'timelineMax',
            events: {
                url: 'experience/get_json_experience/<?=$member->id?>',
                method: 'POST',
                success: function(data) {
                    //var minDate = Math.min(...data.start);
                    data.sort((a,b) => new Date(a.start) - new Date(b.start));
                    var minStartDate = data[0].start;
                    data.sort((a,b) => new Date(b.end) - new Date(a.end));
                    var maxEndDate = data[0].end;;
                    calendar.setOption('visibleRange', {
                        start: minStartDate,
                        end: maxEndDate
                    });
                },
                failure: function() {
                    console.log('there was an error while fetching events!');
                },
            },
            views: {
                timelineMax: {
                    type: 'timeline',
                    buttonText: "max",
                    slotDuration: {years: 1}
                },
                timelineOneYear: {
                    type: 'timeline',
                    buttonText: "1 year",
                    duration: {year: 1},
                    slotDuration: {months: 1}
                },
                timelineFiveYear: {
                    type: 'timeline',
                    buttonText: "5 years",
                    duration: {year: 5},
                    slotDuration: {months: 3}
                },
                timelineTenYear: {
                    type: 'timeline',
                    buttonText: "10 years",
                    duration: {year: 10},
                    slotDuration: {months: 6}
                },
                timelineTwentyYear: {
                    type: 'timeline',
                    buttonText: "20 years",
                    duration: {year: 20},
                    slotDuration: {years: 1}
                },
            },
            headerToolbar: {
                left: 'today prev,next',
                center: 'title',
                right: 'timelineMax,timelineTwentyYear,timelineTenYear,timelineFiveYear,timelineOneYear'
            },
        });
        calendar.render();
    })

</script>
</html>
