<?
foreach ($this->tasks as $task):
    $class = '';
    $priority = $task->getPriority();
    if ($priority > 0 && $priority <= 3) {
        $class = 'prio' . $priority;
    }
    $parentTask = '';
    if ($task->getParentTask() instanceof Figeor\Models\Task) {
        $parentTask = ' / ' . $task->getParentTask()->getName();
    }
    ?>
    <div class="uloha <?= $class; ?>">

        <div>
            <span class="icon icon-date-task"></span><?= $task->getName(); ?>
            (<span class="icon icon-project"></span><?= $task->getProject()->getName() . $parentTask . ' '; ?>)
            <span class="icon icon-time"></span><?= $task->getDeadline('d.m.Y H:i:s'); ?>
        </div>

        <span class="icon icon-chart-down"></span><?= $task->getPoints(); ?> bodov

        <span class="icon icon-comment"></span><?= $task->getDescription(); ?></p>
    <div style="clear: both;"></div>
    </div>
    <?
endforeach;

if (!sizeof($this->tasks)):
    echo 'V zadanom rozsahu sa nenachádzajú žiadne úlohy.';
endif;
