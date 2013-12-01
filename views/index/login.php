<div id="loginForm">
    <form method="GET">
        <fieldset>
            <table>
                <tr><td>Meno:</td><td><input type="text" name="login"></td></tr>
                <tr><td>Heslo:</td><td><input type="password" name="password"></td></tr>
                <!--<tr><td colspan="2"><input type="submit"></td></tr>-->
                <tr><td><a href='/tasks/view'">Prihlásiť</a></td></tr>
            </table>
        </fieldset>
    </form>
</div>

//
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>FIGEOR</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <link href="bootstrap/css/bootstrap.css" rel="stylesheet">
    <link href="bootstrap/css/bootstrap-responsive.css" rel="stylesheet">
    <style type="text/css">
        body {
            padding-top: 60px;
            padding-bottom: 40px;
            padding-left: 40px;
        }
    </style>
</head>

<body>

<form class="form-inline">
    <fieldset>
        <legend>FIGEOR</legend>
        <div class="control-group">
            <label class="control-label" for="textinput">Login</label>
            <div class="controls">
                <input id="login" name="login" type="text" placeholder="Tvoje meno" class="input-xlarge">

            </div>
        </div>

        <!-- Password input-->
        <div class="control-group">
            <label class="control-label" for="passwordinput">Password</label>
            <div class="controls">
                <input id="password" name="password" type="password" placeholder="Tvoje heslo" class="input-xlarge">

            </div>
        </div>

        <!-- Button -->
        <div class="control-group">
            <br>
            <div class="controls">
                <a href="'/tasks/view"><button id="singlebutton" name="singlebutton" class="btn btn-primary">Prihlás sa.</button></a>
            </div>
        </div>

    </fieldset>
</form>


</body>
</html>

