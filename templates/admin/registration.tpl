<html lang="en"><head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Регистрация</title>
    <link rel="shortcut icon" href="/misc/favicon.ico" type="image/x-icon">
    <link href="/assets/global/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
    <link href="/misc/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="/misc/css/admin/login.css" rel="stylesheet">
    <link href="/misc/css/admin/auth-login.css" rel="stylesheet">
  </head>
  <body class="page-login">

    <div class="bg bg-1"></div>

    <div id="login">

        <div class="succesful text-center">
            <p>На Ваш email отправлено письмо с подтверждением!</p>
            <a href="/" class="button button-border button-green">Перейти на главную страницу</a>
        </div>

        <div class="page-wrapper">
            <div class="container">

                <form id="register-form" class="reg-form form-lg" data-bg="bg-1">
                    <a href="/" class="logo">
                        <img src="/misc/images/images/logo-orange.png" alt="">
                    </a>

                    <div id="tabs">
                        <div id="activeMenu" style="display:none;"></div>
                          <ul>
                              <li><a href="#tab-webmaster" data-role="webmaster" class="mainlevel" {if $role=='webmaster'}id="active_menu"{/if}>Вебмастер</a></li>
                              <li><a href="#tab-advertiser" data-role="advertiser" class="mainlevel" {if $role=='advertiser'}id="active_menu"{/if}>Рекламодатель</a></li>
                          </ul>
                      </div>

                    <input type="hidden" id="frole" name="frole" value="{$role}">
                    <input type="hidden" id="fref" name="fref" value="{$ref}">

                    <div style="width: 390px; margin: 0 auto;">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group has-icon">
                                  <input type="text" class="form-control" id="fname" name="fname" placeholder="Имя *" required>
                                  <i class="glyphicon glyphicon-user" aria-hidden="true"></i>
                                </div>
                                <div class="form-group has-icon">
                                  <input class="form-control" type="text" name="femail" id="femail" placeholder="E-mail *" required>
                                  <i class="glyphicon glyphicon-envelope" aria-hidden="true"></i>
                                </div>
                                <div class="info_block_error">
                                  <span class="info_error_peaasword">Введите пароль</span>
                                </div>
                                <div class="form-group has-icon">
                                  <div class="input-icon">
                                    <input class="form-control" type="text" name="fpass" id="fpass" onblur="validatePass(this)" placeholder="Пароль *" required>
                                    <i class="glyphicon glyphicon-lock" aria-hidden="true"></i>
                                </div>
                                </div>
                                <div class="info_block">
                                  <ul class="info_block_ul">
                                    <li class="info_block_li">Пароль должен содержать:</li>
                                    <li class="info_block_li">минимум одну латинскую букву в верхнем регистре</li>
                                    <li class="info_block_li">минимум одну латинскую букву в нижнем регистре</li>
                                    <li class="info_block_li">минимум одну цифру</li>
                                  </ul>
                                </div>
                                <div class="form-group has-icon">
                                  <div id="passwordStrengthDiv" class="is0"></div>
                                </div>
                                <div class="form-group has-icon">
                                  <input class="form-control" type="text" name="fpass2" id="fpass2" placeholder="Подтверждение пароля *" required>
                                  <i class="glyphicon glyphicon-lock" aria-hidden="true"></i>
                                </div>
                                <div class="form-group has-icon">
                                  <input class="form-control numbers-only" type="text" name="fphone" id="fphone" placeholder="Телефон {if $role=='advertiser'}*{/if}">
                                  <i class="glyphicon glyphicon-earphone" aria-hidden="true"></i>
                                </div>

                                  <div class="form-group has-icon">
                                      <input class="form-control" type="text" name="fskype" id="fskype" placeholder="Skype *" required>
                                      <i class="glyphicon glyphicon-user" aria-hidden="true"></i>
                                  </div>
                                  <!-- captcha -->
                            </div>
                        </div>

                        <!-- Tab panes -->
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane {if $role=='advertiser'}active{/if}" id="tab-advertiser">
                                <div class="row">

                                </div>
                            </div>
                            <div role="tabpanel" class="tab-pane {if $role=='webmaster'}active{/if}" id="tab-webmaster">
                                <div class="row">
                                    <div class="checkbox">
                                        <label>
                                          <input type="checkbox" checked name="frules" value="1"> <span style="font-size: 15px">Я ознакомился с <a href="/rules" target="_blank">правилами системы</a> и принимаю их</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div id="g-recaptcha"></div>
                                    <input type="hidden" id="captcha" name="captcha" value="0">
                                    <div class="alert alert-danger" style="display:none;"></div>
                                    <div class="text-center" style="margin-top:20px">
                                        <input type="submit" class="btn btn-join" value="Зарегистрироваться">
                                    </div>
                                    <p class="actions" style="margin-top:35px; font-size: 15px;">
                                        Если вы уже зарегистрированы, нажмите <a href="/admin">Войти</a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script src="/misc/bootstrap/js/bootstrap.min.js"></script>
    <script src="/misc/js/jquery.mask.min.js"></script>
    <script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit"></script>
    {literal}
    <script>
        function onloadCallback() {
            grecaptcha.render('g-recaptcha', {
              sitekey : '6LdAciETAAAAANSVzVnAOEGiIDvXXc7dIwWA2xvA',
              callback: function(r){
                $('#captcha').val("1");
              }
            });
        }
    </script>
    {/literal}
    <script src='/assets/global/plugins/uniform/jquery.uniform.min.js'></script>
    <script src='/misc/js/jquery.validate.min.js'></script>
    <script src='/misc/js/messages_ru.js'></script>
    <script src="/misc/js/login.js"></script>
    </div>

  </body>
</html>
