<form method="POST">
    <input type="hidden" name="action" value="/index/updateProfil">
    <input type="hidden" name="userId" value="<?= $this->user->getId(); ?>">
    <table border='0'>
        <tr><td>Email:</td><td><input type='text' name="userEmail" value='<?= $this->user->getEmail(); ?>'></td></tr>
        <tr><td>Meno:</td><td><input type='text' name="userName" value='<?= $this->user->getName(); ?>'></td></tr>
        <tr><td>Priezvisko:</td><td><input type='text' name="userSurname" value='<?= $this->user->getSurname(); ?>'></td></tr>
        <tr><td>Súčasné heslo:</td><td><input type='password' name="userPass1" value=''></td></tr>
        <tr><td>Nové heslo:</td><td><input type='password' name="userPass2"  value=''></td></tr>
        <tr><td>Potvrdiť nové heslo:</td><td><input type='password' name="userPass3"  value=''></td></tr>
        <tr><td><hr></td></tr>
        <tr><td>Posielať pripomienky:</td><td><input type="checkbox" name="userReminders" <?= $this->user->useReminders() ? 'checked' : ''; ?>></td></tr>
        <tr><td colspan='2'><i>* heslo bude zmenené, keď bude zadané správne súčasné heslo<br>&nbsp;&nbsp;nové sa musí zhodovať v oboch prípadoch</i></td></tr>
        <tr><td colspan="2"><input type="submit" value="Uložiť"></td></tr>
    </table>
</form>