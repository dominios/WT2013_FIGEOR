<form method="POST">
    <input type="hidden" name="action" value="/projects/update">
    <input type="hidden" name="projectId" value="<?= $this->project->getId(); ?>">
    <table border='0'>
        <tr><td>Názov:</td><td><input type='text' name="projectName" value='<?= $this->project->getName(); ?>'></td></tr>
        <tr><td>Popis:</td><td><input type='text' name="projectDescription" value='<?= $this->project->getDescription(); ?>'></td></tr>
        <tr><td>Deadline:</td><td><input type='datetime-local' name="projectDeadline" value="<?= $this->project !== null ? $this->project->getDeadline('Y-m-d\TH:i:s') : ''; ?>"></td></tr>
        <tr><td colspan="2"><input type="submit" value="Uložiť"></td></tr>
    </table>
</form>