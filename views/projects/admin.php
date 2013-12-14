<table border='1' cellpadding='5' cellspacing='0'>
    <tr><th>Názov</th><th>počet úloh</th><th>posledná aktivita</th><th>&nbsp;</th></tr>
    <?
    foreach ($this->projects as $project):
        echo '<tr>
            <td>' . $project->getName() . '</td>
            <td>' . $project->getNumberOfTasks() . '</td>
            <td>' . $project->getLastActivity('d.m.Y H:i:s') . '</td>
            <td>
                <a href="/projects/view/' . $project->getId() . '">[úlohy]</a>
                <a href="/projects/edit/' . $project->getId() . '">[upraviť]</a>
            </td>
        </tr>';
    endforeach;
    ?>
</table>

<button onclick="javascript: window.location = '/projects/create';">Nový projekt</button>