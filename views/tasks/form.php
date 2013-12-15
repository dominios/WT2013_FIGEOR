<form method="POST">
    <input type="hidden" name="action" value="/tasks/submitForm">
    <? if ($this->task instanceof Figeor\Models\Task): ?> <input type="hidden" name="taskId" value="<?= $this->task->getId(); ?>">
    <? else: ?>
        <input type="hidden" name="taskProject" value="<?= isset($_GET['project']) ? $_GET['project'] : ''; ?>">
        <input type="hidden" name="parentTask" value="<?= isset($_GET['task']) ? $_GET['task'] : ''; ?>">
    <? endif; ?>
    <fieldset>
        <table>
            <tr>
                <td>Názov:</td>
                <td>
                    <input type="text" name="taskName" value="<?= $this->task !== null ? $this->task->getName() : ''; ?>">
                </td>
            </tr>
            <tr>
                <td>Popis:</td>
                <td>
                    <textarea name="taskDescription"><?= $this->task !== null ? $this->task->getDescription() : ''; ?></textarea>
                </td>
            </tr>
            <tr>
                <td>Počet bodov:</td>
                <td><input type="text" name="taskPoints" value="<?= $this->task !== null ? $this->task->getPoints() : ''; ?>"></td>
            </tr>
            <tr>
                <td>Priorita:</td>
                <td>
                    <input type="text" name="taskPriority"  value="<?= $this->task !== null ? $this->task->getPriority() : ''; ?>">
                </td>
            </tr>
            <tr>
                <td>Termín:</td>
                <td>
                    <input type="datetime-local" name="taskDeadline" value="<?= $this->task !== null ? $this->task->getDeadline('Y-m-d\TH:i:s') : ''; ?>">
                </td>
            </tr>
            <tr>
                <td>Splnená:</td>
                <td>
                    <input type="checkbox" name="taskFinished" <?= $this->task !== null && $this->task->isFinished() ? 'checked' : ''; ?> value='checked'>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <input type="submit" value="Uložiť" name="taskSubmit">
                </td>
            </tr>
        </table>
    </fieldset>
</form>