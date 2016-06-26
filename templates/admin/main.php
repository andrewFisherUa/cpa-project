<!DOCTYPE html>
<html lang="en" class="no-js">
<head>
<meta charset="utf-8"/>
<title>Univer-Mag</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="width=device-width, initial-scale=1" name="viewport"/>
<meta content="" name="description"/>
<meta content="" name="author"/>

<?php get_stylesheets($component); ?>

<!-- END THEME STYLES -->

<script type="text/javascript" src="/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="/AjexFileManager/ajex.js"></script>
<script type="text/javascript">
CKEDITOR.editorConfig = function( config ) {
    config.language = 'en';
};
</script>
</head>

<body class="page-boxed page-header-fixed page-sidebar-closed-hide-logo page-container-bg-solid page-sidebar-closed-hide-logo">
<!-- BEGIN HEADER -->
<div class="page-header navbar navbar-fixed-top">
	<!-- BEGIN HEADER INNER -->
	<div class="page-header-inner">
		<!-- BEGIN LOGO -->
		<div class="page-logo">
			<a href="/admin">
				 <img src="/misc/images/images/logo.png" alt="Univer-Mag.com" class="logo-default"/>
			</a>
			<div class="menu-toggler sidebar-toggler">
				<!-- DOC: Remove the above "hide" to enable the sidebar toggler button on header -->
			</div>
		</div>
		<!-- END LOGO -->
		<!-- BEGIN RESPONSIVE MENU TOGGLER -->
		<a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse">
		</a>
		<!-- END RESPONSIVE MENU TOGGLER -->
		<!-- BEGIN PAGE TOP -->
		<div class="page-top">
			<!-- BEGIN HEADER SEARCH BOX -->
			<!-- DOC: Apply "search-form-expanded" right after the "search-form" class to have half expanded search box -->
            <?php if ($isAdmin) : ?>
            <span class="login-as-panel">
                <a href="/admin/users/">Все пользователи</a>
                <a href="javascript:;" class="login-as" data-role="webmaster">Кабинет вебмастера</a>
                <a href="javascript:;" class="login-as" data-role="advertiser">Кабинет рекламодателя</a>
            </span>
            <?php endif; ?>
			<!-- END HEADER SEARCH BOX -->
			<!-- BEGIN TOP NAVIGATION MENU -->
			<div class="top-menu">

				<ul class="nav navbar-nav pull-right">

                    <?php if ($bad_orders_count) : ?>
                    <li class="dropdown dropdown-extended dropdown-inbox dropdown-notify" id="header_inbox_bar">
                        <a href="/admin/bad_orders/" class="dropdown-toggle" data-hover="dropdown"
                           data-close-others="true">
                            <i class="icon-layers"></i> <span class="badge badge-danger"><?php echo $bad_orders_count; ?></span>
                        </a>
                    </li>
                    <?php endif; ?>

					<!-- BEGIN NOTIFICATION DROPDOWN -->
					<!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
                    <li class="dropdown dropdown-extended dropdown-notification" id="header_inbox_bar">

                        <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown"
                           data-close-others="true">
                            <i class="fa fa-send-o"></i>
                            <?php if ($tickets["count"]) : ?>
                                <span class="badge badge-awesome"><?php echo $tickets["count"];?></span>
                            <?php endif; ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="external">
                                <h3>У Вас
                                    <a class="bold" href="/admin/tickets"><?php echo $tickets["count"];?> новых</a>
                                    сообщений
                                </h3>
                            </li>
                            <li>
                                <ul class="dropdown-menu-list scroller" style="height: 275px;"
                                    data-handle-color="#637283">
                                    <?php foreach ($tickets["items"] as $m) : ?>
                                        <li>
                                            <a href="/admin/tickets/<?php echo $m["ticket_id"]; ?>">
                                                <span class="time"><?php echo $m["created"]; ?></span>
                                                <span class="details"><?php echo $m["text"]; ?></span>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </li>
                        </ul>
                    </li>
					<?php if ($isPartner) : ?>
                        <li class="dropdown dropdown-extended dropdown-inbox dropdown-notification" id="header_inbox_bar">
                            <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown"
                               data-close-others="true">
                                <i class="icon-envelope-open"></i>
                                <?php if ($news["count"]) : ?>
                                    <span class="badge badge-awesome"><?php echo $news["count"]; ?></span>
                                <?php endif; ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="external">
                                    <h3>У Вас
                                        <a class="bold" href="/admin/news"><?php echo $news["count"]; ?> новых</a>
                                        новостей
                                    </h3>
                                    <!--<a href="/admin/news">Все новости</a>-->
                                </li>
                                <li>
                                    <ul class="dropdown-menu-list scroller" style="height: 275px;"
                                        data-handle-color="#637283">
                                        <?php  foreach ($news["items"] as $a) : ?>
                                            <li>
                                                <a href="/admin/news/<?php echo $a->getId();?>">
                                                    <?php echo $a->getIcon();?>
                                                    <?php echo $a->getTitle();?>
                                                    <span class="time"><?php echo $a->getActivateTime(true);?> </span>
                                                    <div class="clearfix"></div>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                    <?php endif; ?>
                    <?php if ($isAdmin || $isSupport) : ?>
                        <li class="dropdown dropdown-extended dropdown-inbox dropdown-notify" id="header_inbox_bar">
                            <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown"
                               data-close-others="true">
                                <i class="icon-bell"></i>
                                <?php if ($notifications["count"]) : ?>
                                    <span class="badge badge-awesome"><?php echo $notifications["count"]; ?></span>
                                <?php endif;?>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="external">
                                    <h3>У Вас <a class="bold" href="/admin/news"><?php echo $notifications["count"]; ?></a> уведомлений </h3>
                                </li>
                                <li>
                                    <ul class="dropdown-menu-list scroller" style="height: 275px;"
                                        data-handle-color="#637283">
                                        <?php foreach ($notifications["items"] as $a) : ?>
                                            <li><a href="<?php echo $a['page'];?>"><?php echo $a['message'];?></a></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                    <?php endif; ?>

                <!-- BEGIN BALANCE DROPDOWN -->

                    <?php if (!empty($balance)) : ?>
                        <li class="dropdown dropdown-balance dropdown-hold-balance">
                            <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                                <span class="balance">
                                    <div>Холд</div>
                                    <span class="flag flag-<?php echo $balance["default"]->getCountryCode();?>"></span>
                                    <span class="money"><?php echo $balance["default"]->getHold();?></span>&nbsp;<?php echo $balance["default"]->getCurrencyCode();?>
                                </span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-default">
                                <?php foreach ($balance["list"] as $k=>$v) : ?>
                                <li>
                                    <a href="javascript:;">
                                        <span class="flag flag-<?php echo $k;?>"></span>
                                        <span class="money"><?php echo $v->getHold();?></span>&nbsp;<?php echo $v->getCurrencyCode();?></a>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </li>

                        <li class="dropdown dropdown-balance">
                            <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                                <span class="balance">
                                    <div>Баланс</div>
                                    <span class="flag flag-<?php echo $balance["default"]->getCountryCode();?>"></span>
                                    <span class="money"><?php echo $balance["default"]->getCurrent();?></span>&nbsp;<?php echo $balance["default"]->getCurrencyCode();?>
                                </span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-default">
                                <?php foreach ($balance["list"] as $k=>$v) : ?>
                                <li>
                                    <a href="javascript:;">
                                        <span class="flag flag-<?php echo $k;?>"></span>
                                        <span class="money"><?php echo $v->getCurrent();?></span>&nbsp;<?php echo $v->getCurrencyCode();?></a>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                    <?php endif; ?>

                    <!-- END BALANCE DROPDOWN -->

					<!-- BEGIN USER LOGIN DROPDOWN -->
					<!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
    				<li class="dropdown dropdown-user">
                        <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown"
                           data-close-others="true" <?php if ($isAdmin): ?>style="padding-right:10px;"<?php endif;?>>
                            <span class="username username-hide-on-mobile">
                                <?php if ($isBoss) : ?>
                                    <span class="badge badge-roundless badge-awesome">BOSS</span>
                                <?php endif; ?>

                                <?php if ($isAdmin || $was_admin) : ?>
                                    <span class="badge badge-roundless badge-awesome">&nbsp;ADMIN</span>
                                <?php endif;?>

                                <?php if ($isSupport && !$was_admin) : ?>
                                    <span class="badge badge-roundless" style="background-color:#D770AD">&nbsp;SUPPORT</span>
                                <?php endif;?>

                                &nbsp;<?php if ($was_admin): ?>Вы вошли в кабинет <?php endif;?><?php echo $user["login"]; ?>
                            </span>
                            <?php if ($isPartner) : ?>
                                <i class="fa fa-angle-down"></i>
                            <?php endif; ?>
                        </a>
                        <?php if ($isPartner) : ?>
                        <ul class="dropdown-menu dropdown-menu-default" <?php if ($was_admin) : ?>style="width: 300px"<?php endif;?>>
                            <li>
                                <a href="/admin/profile/">
                                    <i class="icon-user"></i> Мой профиль
                                </a>
                            </li>
                            <li>
                                <?php if ($was_admin) : ?>
                                    <a href="javascript:;" class="logout">
                                        <i class="icon-logout"></i> Выйти из кабинета <?php echo $user["login"]; ?>
                                    </a>
                                <?php endif; ?>
                            </li>
                        </ul>
                        <?php endif; ?>
                    </li>

                    <li class="nav-logout">
                        <a href="/unlogin/" title="Выйти из кабинета"><i class="icon-logout"></i></a>
                    </li>
				<!-- END USER LOGIN DROPDOWN -->

				</ul>
			</div>
			<!-- END TOP NAVIGATION MENU -->
		</div>
		<!-- END PAGE TOP -->
	</div>
	<!-- END HEADER INNER -->
</div>
<!-- END HEADER -->
<div class="clearfix">
</div>

<div class="page-container">
	<!-- BEGIN SIDEBAR -->
	<div class="page-sidebar-wrapper">
        <div class="page-sidebar navbar-collapse collapse">
          <ul class="page-sidebar-menu page-sidebar-menu-hover-submenu " data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200">
            <?php echo $navigation; ?>
          </ul>
        </div>
	</div>
	<!-- END SIDEBAR -->
	<!-- BEGIN CONTENT -->
	<div class="page-content-wrapper">
		<div class="page-content">
            <!-- BEGIN STYLE CUSTOMIZER -->

            <!-- END STYLE CUSTOMIZER -->
            <div class="page-content-inner">
                <?php if ($isAdmin) : ?>
                <div class="theme-panel">
                    <div class="toggler tooltips" data-container="body" data-placement="left" data-html="true" data-original-title="Нажмите для дополнительной настройки темы">
                        <i class="icon-settings"></i>
                    </div>
                    <div class="toggler-close">
                        <i class="icon-close"></i>
                    </div>
                    <div class="theme-options">
                        <div class="theme-option theme-colors clearfix">
                            <span>Цветовая схема </span>
                            <ul>
                                <li class="color-default current tooltips" data-style="default" data-container="body" data-original-title="По умолчанию">
                                </li>
                                <li class="color-grey tooltips" data-style="grey" data-container="body" data-original-title="Серый">
                                </li>
                                <li class="color-blue tooltips" data-style="blue" data-container="body" data-original-title="Синий">
                                </li>
                                <li class="color-dark tooltips" data-style="dark" data-container="body" data-original-title="Темный">
                                </li>
                                <li class="color-light tooltips" data-style="light" data-container="body" data-original-title="Светлый">
                                </li>
                            </ul>
                        </div>
                        <div class="theme-option">
                            <span>Стиль темы </span>
                            <select class="layout-style-option form-control input-small">
                                <option value="square" selected="selected">Квадратные углы</option>
                                <option value="rounded">Скругленные углы</option>
                            </select>
                        </div>
                        <div class="theme-option">
                            <span>Макет </span>
                            <select class="layout-option form-control input-small">
                                <option value="fluid">Резиновый</option>
                                <option value="boxed" selected="selected">Фиксированный</option>
                            </select>
                        </div>
                        <div class="theme-option">
                            <span>Header </span>
                            <select class="page-header-option form-control input-small">
                                <option value="fixed" selected="selected">Фиксированный</option>
                                <option value="default">По умолчанию</option>
                            </select>
                        </div>
                        <div class="theme-option">
                            <span>Верхнее выпадающее меню</span>
                            <select class="page-header-top-dropdown-style-option form-control input-small">
                                <option value="light" selected="selected">Светлое</option>
                                <option value="dark">Темное</option>
                            </select>
                        </div>
                        <div class="theme-option">
                            <span>Боковая панель</span>
                            <select class="sidebar-option form-control input-small">
                                <option value="fixed">Фиксированная</option>
                                <option value="default" selected="selected">По умолчанию</option>
                            </select>
                        </div>
                        <div class="theme-option">
                            <span>Стиль меню</span>
                            <select class="sidebar-style-option form-control input-small">
                                <option value="default" selected="selected">По умолчанию</option>
                                <option value="compact">Компактное</option>
                            </select>
                        </div>
                        <div class="theme-option">
                            <span>Меню </span>
                            <select class="sidebar-menu-option form-control input-small">
                                <option value="accordion" selected="selected">Аккордеон</option>
                                <option value="hover">По умолчанию</option>
                            </select>
                        </div>
                        <div class="theme-option">
                            <span>Расположение меню </span>
                            <select class="sidebar-pos-option form-control input-small">
                                <option value="left" selected="selected">Слева</option>
                                <option value="right">Справа</option>
                            </select>
                        </div>
                        <div class="theme-option">
                            <span>Footer </span>
                            <select class="page-footer-option form-control input-small">
                                <option value="fixed">Фиксированный</option>
                                <option value="default" selected="selected">По умолчанию</option>
                            </select>
                        </div>
                    </div>
                </div>
                <?php endif;?>

                <?php require_once $components_path . "/{$component}.php"; ?>
            </div>
		</div>
	</div>
	<!-- END CONTENT -->
	<!-- BEGIN QUICK SIDEBAR -->
	<!--Cooming Soon...-->
	<!-- END QUICK SIDEBAR -->
</div>
<!-- END CONTAINER -->
<!-- BEGIN FOOTER -->
<div class="page-footer">
	<div class="page-footer-inner">
			<script>document.write(new Date().getFullYear())</script> &copy;
    	<a href="<?php get_site_url();?>" target="_blank">univer-mag.com</a>
	</div>
	<div class="scroll-to-top">
		<i class="icon-arrow-up"></i>
	</div>
</div>

<div id="success-message">
    <span class="close">&times;</span>
    <div class="message"></div>
<div>

	<!-- END FOOTER -->
<!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
<!-- BEGIN CORE PLUGINS -->
<!--[if lt IE 9]>
<script src="/assets/global/plugins/respond.min.js"></script>
<script src="/assets/global/plugins/excanvas.min.js"></script>
<![endif]-->
<script src="/assets/global/plugins/jquery.min.js"></script>
<script src="/assets/global/plugins/jquery-migrate.min.js"></script>
<!-- IMPORTANT! Load jquery-ui.min.js before bootstrap.min.js to fix bootstrap tooltip conflict with jquery ui tooltip -->
<script src="/assets/global/plugins/jquery-ui/jquery-ui.min.js"></script>

<?php get_scripts(); ?>

<!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>
