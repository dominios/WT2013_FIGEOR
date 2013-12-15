
<section id="graphs" style="margin: 25px 0 ;">
    <p>
        <?
        $projectDeadline = $this->project->getDeadline();
        $projectDeadlieFormated = $this->project->getDeadline('d.m.Y H:i:s');
        $projectPoints = $this->project->getPointsOverall();
        $projectTasks = $this->project->getAllTaskCount();

        $dStart = new DateTime("@$projectDeadline");
        $dEnd = new DateTime("@" . time());
        $dDiff = $dStart->diff($dEnd);
        $projectDaysLeft = $dDiff->days;

        $projectAvgBurnout = $projectPoints / $projectDaysLeft;
        $projectBurntPoints = $this->project->getBurntPoints();
        ?>
        Celkový deadline: <?= $projectDeadlieFormated ?><br>
        Celkom počet bodov: <?= $projectPoints; ?><br>
        Celkom počet úloh: <?= $projectTasks; ?><br>
        Zostáva dní: <?= $projectDaysLeft; ?><br>
        Priemerne treba spáliť: <?= $projectAvgBurnout; ?><br>
        Spálené / zostáva: <?= $projectBurntPoints . ' / ' . ($projectPoints - $projectBurntPoints); ?>

        <img src="/graph.php?project=<?= $this->project->getId(); ?>">
    </p>
</section>

<h3>Zoznam úloh:</h3>
<?
foreach ($this->tasks as $task):
    echo '<div class="task" style="margin: 15px 0; padding: 5px; border: 1px solid #ccc;">';
    echo '<strong>' . $task->getName() . '</strong>';
    echo ', termín: ' . $task->getDeadline('d.m.Y H:i:s');
    echo ', priorita: ' . $task->getPriority();
    echo ', body: ' . $task->getPoints();

    echo '<p>' . $task->getDescription() . '</p>';

    echo '<div class="subTasks">';
    if ($task->hasSubTasks()) {
        echo 'Podúlohy:';
        echo '<ul>';
        $tasks = $task->getSubTasks();
        foreach ($tasks as $t):
            echo '<li>';
            echo $t->getName();
            if ($t->isFinished()) {
                echo ' [done]';
            } else {
                echo ' <a class="button" href="/tasks/markAsDone/' . $t->getId() . '" onclick="return confirm(\'Označiť úlohu ako splnenú?\');"><span class="icon icon-tick"></span>Splniť</a>';
            }
            echo ' <a class="button" href="/tasks/edit/' . $t->getId() . '"><span class="icon icon-edit"></span>Upraviť</a>';
            echo ' <a class="button" href="/tasks/delete/' . $t->getId() . '" onclick="return confirm(\'Naozaj vymazať túto úlohu?\');"><span class="icon icon-cross"></span>Vymazať</a>';
            echo '</li>';
        endforeach;
        echo '</ul>';
    }
    echo '<div class="button" onclick="javascript: window.location = \'/tasks/add/project/' . $this->project->getId() . '/task/' . $task->getId() . '\';"><span class="icon icon-add"></span>Pridať podúlohu</div>';
    echo '</div>';

    echo '<div class="attachments">';
    if (sizeof($task->getAttachments())) {
        foreach ($task->getAttachments() as $att):
            echo '<a class="button" href="/' . $att->getUrl() . '" target="_blank"><span class="icon icon-attachment"></span>' . $att->getName() . '</a> ';
        endforeach;
    }
    echo ' <a class="button" href="/tasks/addAttachment/task/' . $task->getId() . '"><span class="icon icon-add"></span>Pridať prílohu</a>';
    echo '</div>';

    // cp
    echo '<div style="float: right;">';
    if ($task->isFinished()) {
        echo ' [done]';
    } elseif ($task->isFinishable()) {
        echo ' <a href="/tasks/markAsDone/' . $task->getId() . '" onclick="return confirm(\'Označiť úlohu ako splnenú?\');">[complete]</a>';
    }
    echo '<a class="button" href="/tasks/edit/' . $task->getId() . '"><span class="icon icon-edit"></span>Upraviť</a>';
    echo '<a class="button" href="/tasks/delete/' . $task->getId() . '" onclick="return confirm(\'Naozaj vymazať túto úlohu, vrátane jej podúloh?\');"><span class="icon icon-cross"></span>Vymazať</a>';
    echo '</div>';

    echo '<div style="clear: both;"></div>';

    echo '</div>';
endforeach;
?>

<button onclick="javascript: window.location = '/tasks/add/project/<?= $this->project->getId(); ?>';"><span class='icon icon-add'></span>Pridať novú úlohu</button>