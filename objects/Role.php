<?php

class Role {
    public $permissions;

    public function __construct( $perms = array() ) {
        $this->permissions = $perms;
    }

    // return a role object with associated permissions
    public static function getRolePerms($role_id, $perm_group = null) {
        $role = new Role();
        $sql = "SELECT t2.perm_name FROM role_perm as t1
                JOIN permissions as t2 ON t1.perm_id = t2.perm_id
                WHERE t1.role_id = :role_id ";
        if ( !is_null($perm_group) ) $sql .= " and t2.perm_group = :perm_group";
        $sth = $GLOBALS["DB"]->prepare($sql);
        $sth->bindParam( ':role_id', $role_id, PDO::PARAM_INT );
        if ( !is_null($perm_group) ) $sth->bindParam( ':perm_group', $perm_group, PDO::PARAM_STR );
        $sth->execute();

        while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
            $role->permissions[$row["perm_name"]] = true;
        }
        return $role;
    }

    // get roles labels
    public static function get_roles_labels() {
        $sth = $GLOBALS['DB']->query("SELECT role_name FROM roles");
        return $sth->fetchAll( PDO::FETCH_COLUMN );
    }

    // Add new role
    public static function add( $role_name ) {
        $query = "INSERT INTO roles ( role_name ) VALUES ( :role_name )";
        $stmt = $GLOBALS['DB']->prepare( $query );
        $stmt->bindParam( ':role_name', $role_name, PDO::PARAM_STR );
        $stmt->execute();
        return $GLOBALS['DB']->lastInsertId();
    }

    // Update role
    public static function upd( $role_id, $role_name ) {
        $query = "UPDATE roles SET role_name = :role_name WHERE role_id = :role_id";
        $stmt = $GLOBALS['DB']->prepare( $query );
        $stmt->bindParam( ':role_id', $role_id, PDO::PARAM_INT );
        $stmt->bindParam( ':role_name', $role_name, PDO::PARAM_STR );
        return $stmt->execute();
    }

    // Delete role
    public static function delete ( $role_id ) {
        $query = "DELETE FROM role_perm WHERE role_id = :role_id; DELETE FROM roles WHERE role_id = :role_id";
        $stmt = $GLOBALS['DB']->prepare( $query );
        $stmt->bindParam( ':role_id', $role_id, PDO::PARAM_INT );
        return $stmt->execute();
    }

    // check if a permission is set
    public function hasPerm($permission) {
        return isset($this->permissions[$permission]);
    }

    public function remove_perms( $role_id, $perm_id = null ) {
        $query = "DELETE FROM role_perm WHERE role_id = :role_id";
        if ( !is_null($perm_id) ) $query .= " AND perm_id = :perm_id";
        $stmt = $GLOBALS['DB']->prepare( $query );
        if ( !is_null($perm_id) ) {
            $stmt->bindParam(':perm_id', $perm_id, PDO::PARAM_INT);
        }
        $stmt->bindParam(':role_id', $role_id, PDO::PARAM_INT);
        $stmt->execute();
    }

    // save permissions for role
    public function save_perms( $role_id ) {
        $query = "INSERT INTO role_perm ( role_id, perm_id ) VALUES ( :role_id, :perm_id)";
        $stmt = $GLOBALS['DB']->prepare( $query );
        foreach ( $this->permissions as $perm_id ) {
            $stmt->execute( array( ':role_id' => $role_id, 'perm_id' => $perm_id ) );
        }
    }

    // delete ALL role permissions
    public static function remove_all_perms() {
        $query = "TRUNCATE role_perm";
        $stmt = $GLOBALS["DB"]->prepare($query);
        return $stmt->execute();
    }

    // get role_id by role name
    public static function get_role_id( $role_name ) {
        $query = "SELECT role_id FROM roles WHERE role_name = :role_name";
        $stmt = $GLOBALS['DB']->prepare( $query );
        $stmt->bindParam(':role_name', $role_name, PDO::PARAM_STR );
        $stmt->execute();
        $r = $stmt->fetch(PDO::FETCH_ASSOC);
        return $r['role_id'];
    }

    // get role_name by role_id
    public static function get_role_name ( $role_id ) {
        $query = "SELECT role_name FROM roles WHERE role_id = :role_id";
        $stmt = $GLOBALS['DB']->prepare( $query );
        $r = $stmt->execute( array( ":role_id" => $role_id ) );
        if ( !$r ) return false;
        $r = $stmt->fetch(PDO::FETCH_ASSOC);
        return $r['role_name'];
    }

    // get all perms
    public static function get_all_perms() {
        $stmt = $GLOBALS['DB']->query("SELECT * FROM permissions");
        return $stmt->fetchAll( PDO::FETCH_ASSOC );
    }

    // get all roles
    public static function get_all() {
        $stmt = $GLOBALS['DB']->query("SELECT * FROM roles");
        return $stmt->fetchAll( PDO::FETCH_ASSOC );
    }

    public static function getAlias($role_name){
        $roles = [
            "webmaster" => "Вебмастер",
            "advertiser" => "Рекламодатель",
            "admin" => "Администратор",
            "boss" => "Boss",
            "content_manager" => "Контент менеджер",
            "operator" => "Оператор",
            "support" => "Support"
        ];

        return $roles[$role_name];
    }

    // add new permission
    public static function add_perm( $perm_name, $perm_desc, $perm_group ){
        $query = "INSERT INTO permissions ( perm_name, perm_desc, perm_group )
                  VALUES ( :perm_name, :perm_desc, :perm_group )";
        $stmt = $GLOBALS['DB']->prepare( $query );
        $stmt->bindParam(':perm_name', $perm_name, PDO::PARAM_STR );
        $stmt->bindParam(':perm_desc', $perm_desc, PDO::PARAM_STR );
        $stmt->bindParam(':perm_group', $perm_group, PDO::PARAM_STR );
        $stmt->execute();
    }

    // remove perm by name
    public static function remove_perm( $perm_id ) {
        $query = "DELETE FROM role_perm WHERE perm_id = ?";
        $stmt = $GLOBALS["DB"]->prepare($query);
        $stmt->execute([
            $perm_id
        ]);

        $query = "DELETE FROM permissions WHERE perm_id = ?";
        $stmt = $GLOBALS["DB"]->prepare($query);
        $stmt->execute([
            $perm_id
        ]);
    }

    public static function deleteByPermName( $perm_name ) {
        $stmt = $GLOBALS['DB']->prepare("DELETE FROM permissions WHERE perm_name = :perm_name");
        $stmt->bindParam(":perm_name", $perm_name, PDO::PARAM_INT);
        $stmt->execute();
    }

    // get perm id by perm name
    public static function get_perm_id ( $perm_name ) {
        $query = "SELECT perm_id FROM permissions WHERE perm_name = :perm_name";
        $stmt = $GLOBALS['DB']->prepare( $query );
        $stmt->bindParam(':perm_name', $perm_name, PDO::PARAM_STR );
        $stmt->execute();
        $r = $stmt->fetch( PDO::FETCH_ASSOC );
        return $r['perm_id'];
    }

    public static function getUntouchable(){
        return [
            "view_spaces_new_context",
            "view_spaces_new_public",
            "view_spaces_new_doorway",
            "view_spaces_new_arbitrage",
            "view_spaces_new_other",
            "view_spaces_new_site",
            "view_spaces_validate",
            "view_audit",
        ];
    }


}

?>