<?php

	// Обновляем права
	if ( isset($_POST['save_perms']) ) {
		$roles = $_POST['roles'];

		$sql = "DELETE FROM role_perm WHERE perm_id = :perm_id";
		$sth = $GLOBALS['DB']->prepare($sql);


		$un = Role::getUntouchable();

		foreach ($un as &$a) {
			$a = "'".$a."'";
		}

		$q = "SELECT perm_id
			  FROM permissions 
			  WHERE perm_group = 'view_pages' AND perm_name NOT IN (".implode(",", $un).") ";

		$stmt = $GLOBALS['DB']->query($q);

		while ( $r = $stmt->fetchColumn() ) {
			$sth->execute( array( 'perm_id' => $r ) );
		}

		foreach ( $roles as $a=>$b ) {
			$role_id = Role::get_role_id($a);
			$perms = $filter->sanitize($b['perms'], "int!");
			$role = new Role($perms);
			$role->save_perms( $role_id );
		}

		// Сохранение записи о входе
		$record = new Audit([
		    "group" => "edit_navigation",
		    "action" => "Редактирование навигации",
		]);

		$record->save();
	}

	$labels = Role::get_roles_labels();

	$smarty->assign( 'rolesNum', count($labels) );
	$smarty->assign( 'colWidth', floor( 100 / count($labels) ) );
	$smarty->assign( 'labels', $labels );
	$smarty->assign( 'links', Menu::get_html() );
    $smarty->display( 'admin' . DS . 'navigation' . DS . 'index.tpl' );

    enqueue_scripts(array(
    	"/assets/global/plugins/select2/js/select2.min.js",
    	"/misc/js/page-level/menus.js"
    ));
?>