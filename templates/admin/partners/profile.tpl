<div id="page-profile">

    <h2 class="page-title">Профиль
        <small>информация и финансы</small>
    </h2>

    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
              <i class="fa fa-home"></i>
              <a href="/admin">Главная</a>
              <i class="fa fa-angle-right"></i>
            </li>
            <li>
              <a href="#">Профиль</a>
            </li>
          </ul>
    </div>

    <!-- BEGIN PAGE CONTENT-->
    <div class="row margin-top-20">
        <div class="col-md-12">
            <!-- BEGIN PROFILE SIDEBAR -->
            <div class="profile-sidebar">
                <!-- PORTLET MAIN -->
                <div class="portlet light profile-sidebar-portlet">
                    <!-- SIDEBAR USER TITLE -->
                    <div class="profile-usertitle">
                        <div class="profile-usertitle-name">
                            {$profile.name}
                        </div>
                    </div>
                    <!-- END SIDEBAR USER TITLE -->
                    <!-- SIDEBAR MENU -->
                    <div class="profile-usermenu">
                        <ul class="nav">
                            <li class="active">
                                <a href="#tab_1_1" data-toggle="tab"><i class="icon-home"></i> Персональная информация
                                </a>
                            </li>
                            <li>
                                <a href="#tab_1_3" data-toggle="tab"><i class="icon-lock"></i> Изменить пароль</a>
                            </li>
                            <li>
                                <a href="#tab_1_4" data-toggle="tab"><i class="icon-settings"></i> Платежная информация</a>
                            </li>
                            <li>
                                <a href="#tab_1_5" data-toggle="tab"><i class="icon-bell"></i> Уведомления</a>
                            </li>
                            <li>
                                <a href="#tab_1_6" data-toggle="tab"><i class="icon-rocket"></i> API</a>
                            </li>
                        </ul>
                    </div>
                    <!-- END MENU -->
                </div>
                <!-- END PORTLET MAIN -->
            </div>
            <!-- END BEGIN PROFILE SIDEBAR -->
            <!-- BEGIN PROFILE CONTENT -->
            <div class="profile-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="tab-content">
                            <!-- PERSONAL INFO TAB -->
                            <div class="tab-pane active" id="tab_1_1">
                                <form id="profile-personal-frm" role="form" action="#">
                                    <div class="portlet light">
                                      <div class="portlet-title">
                                        <div class="caption">
                                            Персональная информация
                                        </div>
                                        <div class="actions">
                                            <a href="javascript:;" class="btn blue btn-circle"
                                               onclick="saveProfileInfo({$profile.id})">
                                                Сохранить
                                            </a>
                                        </div>
                                      </div>
                                      <div class="portlet-body">
                                        <div class="alert alert-danger" style="display:none"></div>
                                        <div class="form-group">
                                            <label class="control-label">E-mail</label>
                                            <input type="text" placeholder="{$profile.email}" class="form-control" disabled/>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label">Логин</label>
                                            <input type="text" placeholder="{$profile.login}" class="form-control" disabled/>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label">Skype</label>
                                            <input type="text" placeholder="{$profile.skype}" class="form-control" disabled/>
                                            <p class="help-block">Редактирование возможно только через запрос в тикет</p>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label">Имя</label>
                                            <input type="text" id="profile-first_name" placeholder="{$profile.name}" class="form-control"/>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label">Фамилия</label>
                                            <input type="text" id="profile-last_name" placeholder="{$profile.last_name}" class="form-control"/>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label">Телефон
                                                <span id="profile-phone-error" class="error">Заполните это поле.</span>
                                            </label>
                                            <input type="text" id="profile-phone" placeholder="{$profile.phone}" class="form-control numbers-only"/>
                                        </div>

                                      </div>
                                    </div>
                                </form>
                            </div>
                            <!-- END PERSONAL INFO TAB -->
                            <!-- CHANGE PASSWORD TAB -->
                            <div class="tab-pane" id="tab_1_3">
                                <form action="#" id="profile-pass-frm">
                                    <div class="portlet light">
                                      <div class="portlet-title">
                                        <div class="caption">
                                            Изменение пароля
                                        </div>
                                        <div class="actions">
                                            <a href="javascript:;" class="btn btn-circle blue"
                                               onclick="saveProfilePass({$profile.id})">
                                                Изменить пароль
                                            </a>
                                            <a href="javascript:;" class="btn btn-default btn-circle reset-form">
                                                Отмена
                                            </a>
                                        </div>
                                      </div>
                                      <div class="portlet-body">
                                            <div class="alert alert-danger" style="display:none"></div>
                                            <div class="form-group">
                                                <label class="control-label">
                                                    Новый пароль
                                                    <span id="profile-new_pass-error" class="error"></span>
                                                </label>
                                                <input type="password" id="profile-new_pass" class="form-control"
                                                       required/>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label">
                                                    Подтверждение пароля
                                                    <span id="profile-new_passr-error" class="error"></span>
                                                </label>
                                                <input type="password" id="profile-new_passr" class="form-control"
                                                       required/>
                                            </div>

                                      </div>
                                    </div>
                                 </form>
                            </div>
                            <!-- END CHANGE PASSWORD TAB -->
                            <!-- PRIVACY SETTINGS TAB -->
                            <div class="tab-pane" id="tab_1_4">
                                <div class="portlet light">
                                    <div class="portlet-title">
                                        <div class="caption">Добавить кошелек WMR</div>
                                    </div>
                                    <div class="portlet-body">
                                        <form id="add-wallet-form" >
                                            <div class="alert alert-danger" style="display:none"></div>
                                            <div class="form-group">
                                                <label class="control-label">Введите 12-значный номер Вашего кошелька:</label>
                                            </div>
                                            <div class="form-inline">
                                                <div class="form-group">
                                                    <input type="text" value="" name="wallet" class="form-control">
                                                    <button class="btn green" id="add-wallet-btn">Добавить</button>
                                                </div>
                                                <div class="help-block">
                                                    Формат: XXXX XXXX XXXX
                                                </div>
                                            </div>
                                        </form>
                                        <div id="wallets-list-wrap" {if !$wallets}style="display:none;{/if}">
                                            <h5><strong>Кошелек по умолчанию:</strong></h5>
                                            <ul id="wallets-list">
                                                {foreach from=$wallets item=a}
                                                <li>
                                                    <label>
                                                        <input type="radio" value="{$a.wallet}" name="wallet" {if $a.main == 1}checked{/if}/> {$a.wallet}
                                                    </label>
                                                </li>
                                                {/foreach}
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- END PRIVACY SETTINGS TAB -->
                            <!-- NOTIFY TAB -->
                            <div class="tab-pane" id="tab_1_5">
                                <form method="post" class="notify-form">
                                <div class="portlet light">
                                    <div class="portlet-title">
                                        <div class="caption">Подписка на новости</div>
                                        <div class="actions">
                                            <a href="javascript:;" class="btn blue btn-circle"
                                               onclick="saveProfileNotify()">
                                                Сохранить
                                            </a>
                                        </div>
                                    </div>
                                    <div class="portlet-body">
                                        <div class="alert alert-success" style="display:none"></div>
                                        <div class="header-form clearfix">
                                            <div class="head pull-right">Отправка на E-mail</div>
                                            <div>Группа уведомлений</div>
                                        </div>
                                        {foreach from=$options item=v}
                                            <div class="margin-top-10 clearfix">

                                                <div class="pull-right bootstrap-switch bootstrap-switch-wrapper bootstrap-switch-animate {if $v.value == 1}bootstrap-switch-on{else}bootstrap-switch-off{/if}"
                                                     style="width: 120px;">
                                                    <div class="bootstrap-switch-container"
                                                         style="width: 147px; margin-left: 0;">
                                                        <input type="checkbox" name="notify[]" data-id="{$v.id}"
                                                               data-off-color="warning"
                                                               data-on-color="success" {if $v.value == 1}checked=""{/if}
                                                               class="make-switch">
                                                    </div>
                                                </div>

                                                <div>{$v.desc}</div>
                                            </div>
                                        {/foreach}
                                    </div>
                                    </div>
                                </form>
                            </div>
                            <!-- END NOTIFY TAB -->
                            <!-- API TAB -->
                            <div class="tab-pane" id="tab_1_6">
                                <div class="portlet light">
                                    <div class="portlet-title">
                                        <div class="caption">API</div>
                                    </div>
                                    <div class="portlet-body">
                                        {if $api_key.status == 'moderation'}
                                            <div class="alert alert-info">
                                                Ваш запрос находится в обработке.
                                            </div>
                                        {/if}

                                        {if $api_key.status == 'refused'}
                                            <div class="alert alert-info">
                                                Отказано в доступе к API.
                                            </div>
                                        {/if}

                                        {if !$api_key}
                                            <div class="alert alert-info">
                                                Для доступа к API необходимо отправить запрос.
                                            </div>
                                            <div class="form-group">
                                                <button id="request-api-btn" class="btn green">Отправить запрос</button>
                                            </div>
                                        {/if}

                                        {if $api_key.status == 'accepted'}
                                        <div class="form-group">
                                            <label class="control-label">
                                                Доступ к API:
                                            </label>
                                            <div class="input-group">
                                              <input type="text" class="form-control" id="api-link" value="{$api_link}" disabled="">
                                              <span class="input-group-btn">
                                                <button class="btn green" type="button" id="api-link-btn"><i class="fa fa-copy"></i></button>
                                             </span>
                                            </div>
                                        </div>
                                        {/if}
                                    </div>
                                </div>
                            </div>
                            <!-- END API TAB -->
                        </div>
                    </div>
                </div>
            </div>
            <!-- END PROFILE CONTENT -->
        </div>
    </div>
    <!-- END PAGE CONTENT-->

</div>