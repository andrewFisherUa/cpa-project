<html lang="en"><head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Авторизация</title>
    <link href="/assets/global/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
    <link href="/misc/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="/misc/css/admin/login.css" rel="stylesheet">
    <link href="/misc/css/admin/auth-login.css" rel="stylesheet">
    <link rel="shortcut icon" href="/misc/favicon.ico" type="image/x-icon">

  </head>
  <body class="page-login">


    <div id="login">
        <div class="bg bg-1" style="display:none"></div>
        <div class="bg bg-2"></div>

        <div class="succesful text-center">
            <p>Поздравляем! Первый шаг на пути к финансовой свободе сделан.</p>
            <p>Теперь начните зарабатывать!</p>
            <a href="/admin" class="button button-border button-green">Перейти в личный кабинет</a>
        </div>

        <div class="page-wrapper">
            <div class="container">

                <form id="login-form" class="reg-form" action="/check/" method="post" data-bg="bg-2">
                    <a href="/" class="logo">
                        <img src="/misc/images/images/logo-orange.png" alt="">
                    </a>
                    <h1>Войдите в свою учетную запись</h1>
                    <div class="form-group has-icon">
                        <input type="text" class="form-control" id="alogin" name="alogin" placeholder="Email" autocomplete="off">
                        <i class="glyphicon glyphicon-user" aria-hidden="true"></i>
                    </div>
                    <div class="form-group has-icon">
                        <input type="password" class="form-control"id="apassword"  name="apassword" placeholder="Пароль" autocomplete="off">
                        <i class="glyphicon glyphicon-lock" aria-hidden="true"></i>
                    </div>
                    <div class="form-error" style="display: none"></div>

                    <div class="clearfix"></div>
                    <div class="checkbox pull-right">
                        <label id="remember-me">
                          <input type="checkbox" name="remember" value="1"> Запомнить меня
                        </label>
                    </div>

                    <div class="clearfix"></div>

                    <div class="form-group">
                        <button type="submit" id="adminloginok" class="btn btn-sm btn-join pull-right">Войти</button>

                    </div>

                    <div class="clearfix"></div>
                    <!--
                    <div class="form-group">
                        <p>Или войдите с:</p>
                        <ul class="social">
                            <li><a href="#" class="ico-facebook"></a></li>
                            <li><a href="#" class="ico-google"></a></li>
                            <li><a href="#" class="ico-twitter"></a></li>
                            <li><a href="#" class="ico-linkedin"></a></li>
                        </ul>
                    </div>
                    -->
                    <div class="separator"></div>

                    <div class="form-group actions">
                        <a href="javascript:;" data-form="forget-password">Забыли пароль</a>
                    </div>

                    <div class="form-group actions">
                        <h4 class="margin-top-5">Еще не зарегистрированы?</h4>
                        <p>
                            <a href="/registration/advertiser" data-role="advertiser" class="text-primary pull-right">Я рекламодатель</a>
                            <a href="/registration/webmaster" data-role="webmaster" class="text-primary">Я вебмастер</a>
                        </p>
                    </div>
                </form>

                <form id="forget-password" class="reg-form" action="" style="display:none;" data-bg="bg-2">
                    <a href="/" class="logo">
                        <img src="/misc/images/images/logo-orange.png" alt="">
                    </a>
                    <div class="form-wrap">
                        <h1>Забыли пароль?</h1>
                        <p>Введите адрес электронной почты для восстановления пароля</p>
                        <div class="form-group has-icon">
                            <input class="form-control" type="email" name="email" id="email" placeholder="E-mail" required="required">
                            <i class="glyphicon glyphicon-envelope" aria-hidden="true"></i>
                        </div>
                        <div class="form-error" style="display:none"></div>
                        <button class="btn btn-sm btn-green pull-right" type="submit" id="forget-password-btn">Отправить</button>

                        <div class="separator clearfix"></div>
                    </div>
                    <div class="form-success" style="display: none">
                        Письмо было отправлено на ваш электронный адрес.
                    </div>
                    <div class="actions">
                        <a href="/registration/">Регистрация</a>
                        <a href="javascript:;" data-form="login-form">Войти</a>
                    </div>

                </form>
            </div>
        </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script src="/misc/js/login.js"></script>
    <script src="/misc/js/jquery.mask.min.js"></script>
    <script src='/assets/global/plugins/uniform/jquery.uniform.min.js'></script>

    </div>
  </body>
</html>