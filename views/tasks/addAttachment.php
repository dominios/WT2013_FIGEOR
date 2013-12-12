<form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="action" value="/tasks/attachmentSubmit">
    <input type="hidden" name="taskId" value="<?= $_GET['task']; ?>">
    <table border="0" cellcpacing="0" cellpadding="0">
        <tr><td>Názov súboru:</td><td><input type="text" name="fileName"></td></tr>
        <tr><td>Súbor:</td><td><input type="file" name="fileUrl"></td></tr>
        <tr><td colspan="2"><input type="submit"></td></tr>
    </table>
</form>