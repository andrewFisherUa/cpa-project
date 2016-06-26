<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Восстановление пароля</title>
    <link rel="shortcut icon" href="/misc/images/favicon.ico" type="image/x-icon">
    <link href="/misc/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="/misc/css/admin/auth.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script src="/misc/js/auth.js"></script>

  </head>
  <body>
    <div id="recovery" class="fluid">
        <div class="partner-form centered">
            <a href="/" class="logo">
                <img src="/misc/images/images/logo-orange.png" alt="">
            </a>
            <div id="wrapper">
                <h1>Восстановление пароля</h1>
                <form id="recovery-form" method="post">
                    <input type="hidden" name="key" id="key" value="<?php echo $key; ?>">
                    <div class="form-group has-icon">
                        <input class="form-control" type="email" name="email" id="email" placeholder="E-mail" required="required">
                        <i class="glyphicon glyphicon-envelope" aria-hidden="true"></i>
                    </div>
                    <div class="form-group has-icon">
                        <input class="form-control" type="password" name="password" id="password" placeholder="Пароль" required="required">
                        <i class="glyphicon glyphicon-lock" aria-hidden="true"></i>
                    </div>
                    <div class="form-group has-icon">
                        <input class="form-control" type="password" name="confirmpassword" id="confirmpassword" placeholder="Подтверждение пароля" required="required">
                        <i class="glyphicon glyphicon-lock" aria-hidden="true"></i>
                    </div>

                <div class="form-error"></div>         
                <button class="btn btn-orange pull-right" type="submit" id="recoveryPass">Подтвердить</button>
            </div>
            <div id="success" style="display: none">
                <p class="text-center">Ваш пароль был успешно изменен </p>
                <a href="/admin" class="btn btn-orange"><i class="glyphicon glyphicon-log-in"></i> Войти</a>
            </div>
            </form>
        </div>
     </div>
  </body>
</html>

