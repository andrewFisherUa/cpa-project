<?php

$message = array();
$template = "index";

// Обновляем права
if ( isset($_POST['save_perms']) ) {
	$GLOBALS['DB']->query("DELETE FROM role_perm WHERE perm_id IN (SELECT perm_id FROM permissions WHERE perm_group != 'view_pages')");
	foreach ( $_POST['roles'] as $role_name=>$perms ) {
		$role_id = Role::get_role_id($role_name);
		$role = new Role( $perms['perms'] ) ;
		$role->save_perms( $role_id );
	}
}

// Добавляем роль
if ( isset($_POST['add_role']) ) {
	$role_name = $_POST['role_name'];
	if ( Role::get_role_id( $role_name ) ) {
		$message = array( 'text' => "Роль `{$role_name}` уже существует.", 'class_name' => "danger" );
	} else {
		$role_id = Role::add( $role_name );
		$role = new Role( $_POST['perms'] ) ;
		$role->save_perms( $role_id );
	}
}

// Редактируем роль
if ( isset($_POST['save_role']) ) {
	Role::upd( $_POST['role_id'], $_POST['role_name'] );
}

// Удаляем роль
if ( isset($_POST['remove_role']) ) {
	$r = Role::delete( $_POST['role_id'] );
	if ( $r ) {
		$message = array( 'text' => "Роль `".$_POST['role_name']."` успешно удалена", 'class_name' => "success" );
	}
}



// Права доступа
if ( $_REQUEST['k'] == '' ) {
	$roles = Role::get_all();

	$stmt = $GLOBALS['DB']->query("SELECT * FROM permissions WHERE perm_group != 'view_pages' ORDER BY perm_group ASC, perm_name ASC");
    $perms = $stmt->fetchAll( PDO::FETCH_ASSOC );

	foreach ( $roles as $role ) {
		$roleObj[$role['role_name']] = Role::getRolePerms($role['role_id']);
	}

	$rows = '';

	foreach ( $perms as $perm ) {
		$perm_name = $perm['perm_name'];

		$rows .= '<tr>';
		$rows .= '<td> '. $perm["perm_id"] . '</td>';
		$rows .= '<td> '. $perm["perm_group"] . '</td>';
		$rows .= '<td> '. $perm["perm_desc"] . '</td>';
		$rows .= '<td> '. $perm_name . '</td>';
		
		foreach ( $roles as $role ) {
			$rows .= '<td class="text-center">';
			$role_name = $role['role_name'];

			$r = $roleObj[$role_name];
			$checked = ( $r->hasPerm($perm_name) ) ? 'checked' : '';
			$rows .= '<input type="checkbox" name="roles['.$role_name.'][perms][]" value="'.$perm['perm_id'].'" ' . $checked . '></td>';
		}
		$rows .= "</tr>";
	}

	$smarty->assign('perms', $perms);
	$smarty->assign('roles', $roles);
	$smarty->assign('rows', $rows);
	$template = "index";
} else {

	// Добавление пользователя
	if ( $_REQUEST['k'] == 'add-user' ) {
		$smarty->assign('roles', Role::get_all() );
		$template = "add-user";
	}

	// Просмотр и редактирование ролей
	if ( $_REQUEST['k'] == 'roles' ) {

		// Редактирование роли
		if ( $_REQUEST['b'] == 'edit' ) {
			$role_id = $_REQUEST['c'];
			$role_name = Role::get_role_name( $role_id );

			if ( $role_name ) {
				$smarty->assign( "role_id" , $role_id );
				$smarty->assign( "role_name" , $role_name );
				$template = "edit-role";
			} else {
				// Если нет роли с таким ID перенаправляем на страницу со списком ролей.
				echo "<script>window.location = '/admin/permissions/roles' </script>";
			}
		} else {
			// Список ролей
			$smarty->assign('roles', Role::get_all() );
			$template = "roles";
		}
	}
}

$smarty->assign('message', $message);
$smarty->display( 'admin' . DS . 'permissions' . DS . $template . '.tpl' );

enqueue_scripts( array(
    "/assets/global/plugins/datatables/datatables.min.js",
    "/assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js",
    "/assets/global/scripts/datatable.js",
    "/assets/global/plugins/uniform/jquery.uniform.min.js",
    "/misc/js/page-level/roles.js" ));



?>