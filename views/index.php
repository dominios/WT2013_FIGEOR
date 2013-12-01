<!DOCTYPE HTML>
<html>
    <head>
        <title><?= $this->title; ?> | FIGEOR</title>
        <link href="/design/css/style.css" type="text/css" rel="stylesheet">
    </head>
    <body>

        <section id="outerContainer">

            <header id="mainMenu">
                Zobraziť úlohy na:
                <a href="/tasks/view/days/1">dnes</a> |
                <a href="/tasks/view/days/2">2 dni</a> |
                <a href="/tasks/view/days/7">7 dní</a> |
                <a href="/tasks/view/days/30">30 dní</a>
            </header>

            <section id="innerContainer">
                <aside id="leftPanel">
                    <div id="currentProject">
                        <h2>Projekty</h2>
                    </div>
                    <div id="allProjects">
                        <a href="/projects/view/1">Webové technológie</a><br>
                        <a href="/projects/view/2">Stavba počítačov</a><br>
                        <a href="/projects/view/3">Konštrukcia prekladačov</a><br>
                        <a href="/projects/view/3">Administrácia databáz</a>
                        <br><br>
                        <a href="/index/profil">-> Môj profil</a><br>
                        <a href="/projects/admin">-> Spravovať projekty</a>
                    </div>
                </aside>
                <section id="rightPanel">
                    <h2><?= $this->title; ?></h2>
                    <?= $this->mainContent; ?>
                </section>
            </section>

        </section>
    </body>
</html>

//
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?= $this->title; ?> | FIGEOR</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <link href="bootstrap/css/bootstrap.css" rel="stylesheet">
    <link href="bootstrap/css/bootstrap-responsive.css" rel="stylesheet">
    <style type="text/css">
        body {
            padding-top: 60px;
            padding-bottom: 40px;
            padding-left: 40px;
        }
    </style>
</head>

<body>
<div class="navbar navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container">
            <ul class="nav">
                <li class="active">
                    <a class="brand" href="#">Todo planner</a>
                </li>
                <li><a href="/tasks/view/days/1"><button type="button" class="btn btn-default">Today</button></a></li>
                <li><a href="/tasks/view/days/2"><button type="button" class="btn btn-default">2 days</button></a></li>
                <li><a href="/tasks/view/days/7"><button type="button" class="btn btn-default">Week</button></a></li>
                <li><a href="/tasks/view/days/30"><button type="button" class="btn btn-default">Month</button></a></li>
            </ul>

        </div>
    </div>
</div>


<div class="container-fluid">
    <div class="row-fluid">
        <div class="span3">
            <div class="row-fluid">
                <div class="col-md-3 col-md-offset-3"style=" background-color: lightblue">
                    <br><form style="text-align: center">
                        <input type="text" placeholder="Enter task">
                    </form>
                </div>
            </div>
            <div class="row-fluid">
                <div class="col-md-3 col-md-offset-3" style=" background-color: lightblue;text-align: center">
                    <span>My profile</span><br>
                    <span>Task1</span>
                </div>
            </div>
        </div>
        <div class="span8" style="background-color: lightblue">

            <div class="row-fluid" style="text-align: center">
                <span>Matika<div class="progress pull-right" style="width: 30%;>
                    <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100""><span class="sr-only">60% Complete</span>
                </span>
            </div>

         </div>
    </div>
</div>

</body>
</html>

