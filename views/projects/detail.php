
<section id="stats" style="margin: 10px 0 ;">
    <p>
        <?
        $projectStart = $this->project->getDateCreated('d.m.Y');
        $projectDeadline = $this->project->getDeadline();
        $projectDeadlieFormated = $this->project->getDeadline('d.m.Y H:i:s');
        $projectPoints = $this->project->getPointsOverall();
        $projectTasks = $this->project->getAllTaskCount();

        if ($projectDeadline < time()) {
            $projectDeadlieFormated = 'nezadaný';
            $projectDaysLeft = 'n/a';
        } else {

            $dStart = new DateTime("@$projectDeadline");
            $dEnd = new DateTime("@" . time());
            $dDiff = $dStart->diff($dEnd);
            $projectDaysLeft = $dDiff->days;
//            $projectAvgBurnout = $projectPoints / $projectDaysLeft;
        }
        $projectBurntPoints = $this->project->getBurntPoints();
        ?>
    <table cellspacing='5' cellpadding='5' border='0' style="float: left; margin-right: 50px;">
        <tr>
            <td><span class="icon icon-flag-green"></span>Začiatok:</td><td><?= $projectStart; ?></td>
        </tr><tr>
            <td><span class="icon icon-flag-finish"></span>Koniec:</td><td><?= $projectDeadlieFormated; ?></td>
        </tr><tr>
            <td><span class="icon icon-clock-red"></span>Zostáva dní:</td><td><?= $projectDaysLeft; ?></td>
        </tr>
    </table>
    <table cellspacing='5' cellpadding='5' border='0'>
        <tr>
            <td><span class="icon icon-date-task"></span>Počet všetkých úloh:</td><td><?= $projectTasks; ?></td>
        </tr><tr>
            <td><span class="icon icon-chart-stock"></span>Celkový počet bodov:</td><td><?= $projectPoints; ?></td>
        </tr><tr>
            <td><span class="icon icon-chart-down"></span>Spálené / zostáva:</td><td><?= $projectBurntPoints . ' / ' . ($projectPoints - $projectBurntPoints); ?> (bodov)</td>
        </tr>
    </table>
    <?
    if ($projectTasks < 1):
        echo '<p>Pre zostrojenie grafu spaľovania je potrebné pridať do projektu úlohy a zadať im body.</p>';
    else :
        echo '<img src="/graph.php?project=' . $this->project->getId() . '" id="burndownchart">';
        ?>
        Začiatok:
        <input type="text" name="chartStart" size="1">
        Koniec:
        <input type="text" name="chartTo" size="1">
        <div class="button" onclick="javascript: regenerateChart();">zobraziť v danom rozsahu</div>
        <script type="text/javascript">
            function regenerateChart() {
                from = $("input[name='chartStart']").val();
                to = $("input[name='chartTo']").val();
                $("#burndownchart").attr('src', '/graph.php?project=<?= $this->project->getId(); ?>&from=' + from + '&to=' + to);
            }
        </script>
    <?
    endif;
    ?>
</p>
</section>

<h3>Zoznam úloh:</h3>
<?
foreach ($this->tasks as $task):
    echo '<div class="task">';
    echo '<strong>' . $task->getName() . '</strong>';
    echo ', termín: ' . $task->getDeadline('d.m.Y H:i:s');
    $priority = $task->getPriority();
    if ($priority <= 3 && $priority > 0) {
        echo ', <span class="prio' . $priority . '">priorita: ' . $task->getPriority() . '</span>';
    } else {
        echo ', žiadna priorita';
    }
    echo ', body: ' . $task->getPoints();

    echo '<p>' . $task->getDescription() . '</p>';

    echo '<div class="subTasks">';
    if ($task->hasSubTasks()) {
        echo '<div class="subHeader">Podúlohy:</div>';
        echo '<ul>';
        $tasks = $task->getSubTasks();
        foreach ($tasks as $t):
            $class = array();
            if ($t->isFinished()) {
                $class[] = 'finished';
            }
            $priority = $t->getPriority();
            if ($priority <= 3 && $priority > 0) {
                $class[] = 'prio' . $priority;
            }

            echo '<li ' . 'class="' . implode(' ', $class) . '"' . ' title="' . $t->getDescription() . '">';
            echo $t->getName();
            if ($t->isFinished()) {
                echo ' (splnené ' . $t->getDateFinished('d.m.Y H:i:s') . ', body: ' . $t->getPoints() . ')';
            } else {
                echo ' (T: ' . $t->getDeadline('d.m.Y H:i:s') . ', body: ' . $t->getPoints() . ')';
            }
            if ($t->isFinished()) {
                echo '<span class="icon icon-tick" style="margin-left: 8px;"></span>';
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
    echo '<div class="subHeader" style="margin-bottom: 10px;">Prílohy:</div>';
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