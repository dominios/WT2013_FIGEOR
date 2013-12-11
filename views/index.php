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
                        <?
                        $user = new Figeor\Models\User(1);
                        foreach (Figeor\Models\Project::fetchByUser($user) as $p):
                            echo '<a href="/projects/view/' . $p->getId() . '">' . $p->getName() . '</a><br>';
                        endforeach;
                        ?>
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