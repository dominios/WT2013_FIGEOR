<table border='0' cellpadding='5' cellspacing='10'>
    <tr><th>Názov</th><th>počet úloh</th><th>posledná aktivita</th><th>&nbsp;</th></tr>
    <?
    foreach ($this->projects as $project):
        echo '<tr>
            <td><span class="icon icon-project"></span>' . $project->getName() . '</td>
            <td align="center">' . $project->getNumberOfTasks() . '</td>
            <td>' . $project->getLastActivity('d.m.Y H:i:s') . '</td>
            <td>
                <a class="button" href="/projects/view/' . $project->getId() . '"><span class="icon icon-date-task"></span>Úlohy</a>
                <a class="button" href="/projects/edit/' . $project->getId() . '"><span class="icon icon-edit"></span>Upraviť</a>
                <a class="button" href="/projects/delete/' . $project->getId() . '" onclick="return confirm(\'Naozaj vymazať tento projekt, vrátane všetkých jeho údajov?\');"><span class="icon icon-cross"></span>Vymazať</a>
            </td>
        </tr>';
    endforeach;
    ?>
</table>

<div class="button" onclick="javascript: window.location = '/projects/create';"><span class="icon icon-add"></span>Nový projekt</div>