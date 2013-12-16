<!DOCTYPE HTML>
<html>
    <head>
        <title><?= $this->title; ?> | FIGEOR</title>
        <link href="/design/css/style.css" type="text/css" rel="stylesheet">
        <link href="/design/css/icons.css" type="text/css" rel="stylesheet">
        <link href="/design/css/icons-data.css" type="text/css" rel="stylesheet">
        <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    </head>
    <body>

        <section id="outerContainer">

            <header id="mainMenu">
                <span class='icon icon-calendar'></span>
                Zobraziť úlohy na:
                <a href="/tasks/view/days/1">dnes</a> |
                <a href="/tasks/view/days/2">2 dni</a> |
                <a href="/tasks/view/days/7">7 dní</a> |
                <a href="/tasks/view/days/30">30 dní</a> |
                <a href="/tasks/view">všetky</a>
                <a href="/index/logout" style='float: right;'><span class='icon icon-logout'></span>Odhlásiť</a>
                <a href="/index/profil" style='float: right;'><span class='icon icon-user'></span>Môj profil</a>
            </header>

            <section id="innerContainer">
                <aside id="leftPanel">
                    <div id="currentProject">
                        <h2><span class='icon icon-chart-curve'></span>Projekty</h2>
                    </div>
                    <div id="allProjects">
                        <a href="/projects/admin"><span class='icon icon-chart-curve'></span>Spravovať projekty</a><br>
                        <?
                        $user = \Figeor\Core\System::currentUser();
                        foreach (Figeor\Models\Project::fetchByUser($user) as $p):
                            echo '<a href="/projects/view/' . $p->getId() . '"><span class="icon icon-project"></span>' . $p->getName() . '</a><br>';
                        endforeach;
                        ?>
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