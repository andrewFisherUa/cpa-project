<?php

   $respond = array();

   if ( isset( $_POST['m_id']) ) {

   		$action = $_POST['action'];

   		if ( $action == "edit" ) {
   			$link = Menu::get_item( $_POST['m_id'] );
   			$smarty->assign('link', $link);
   		}

   		if ( $action == "edit" || $action == "add" ) {
            $max_weight = Menu::get_max_weight() + 1;
   			$smarty->assign('weight', range(0, $max_weight));
   			$smarty->assign('parents', Menu::get_all_links() );
            $smarty->assign('admin_url', get_admin_url());
            $respond['title'] = ($action == "edit") ? "Редактирование страницы `" . $link["title"] . "`" : "Добавление страницы";
   			$respond['form'] = $smarty->fetch( 'admin' . DS . 'navigation' . DS . 'ajax' . DS . 'edit-link.tpl' );
   		}

   		if ( $action == "remove" ) {
   			Menu::remove_item( $_POST['m_id'] );
            $link = str_replace('/', '_', $_POST['link']);
            $perm_id = Role::get_perm_id( "view_{$link}" );
            Role::remove_perm( $perm_id );

   			$respond["menu"] = Menu::get_html();
   		}

   		if ( $action == "save" ) {
   			$menu = new Menu( $_POST );
	   		$menu->save();

            $title = $_POST['title'];
            $link = str_replace('/', '_', $_POST['link']);

            Role::add_perm( "view_{$link}", $title, 'view_pages');
	   		$respond["menu"] = Menu::get_html();
   		}

   }

   echo json_encode( $respond );

   die();

?>