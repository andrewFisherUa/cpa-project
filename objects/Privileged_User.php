<?php

class Privileged_User extends User {
    private $roles;

    // override User method
    public static function getByUsername($login) {
        $sql = "SELECT * FROM users WHERE login = :login";
        $sth = $GLOBALS["DB"]->prepare($sql);
        $sth->execute(array(":login" => $login));
        $result = $sth->fetchAll();

        if (!empty($result)) {
            $privUser = new Privileged_User();
            $privUser->id = $result[0]["user_id"];
            $privUser->login = $login;
            $privUser->password = $result[0]["password"];
            $privUser->initRoles();
            return $privUser;
        } else {
            return false;
        }
    }

    // override User method
    public static function getByEmail($email) {
        $sql = "SELECT * FROM users WHERE email = :email";
        $sth = $GLOBALS["DB"]->prepare($sql);
        $sth->execute(array(":email" => $email));
        $result = $sth->fetchAll();

        if (!empty($result)) {
            $privUser = new Privileged_User();
            $privUser->id = $result[0]["user_id"];
            $privUser->email = $email;
            $privUser->password = $result[0]["password"];
            $privUser->initRoles();
            return $privUser;
        } else {
            return false;
        }
    }

    public static function get_roles( $id ) {
        $sql = "SELECT t1.role_id, t2.role_name FROM user_role as t1
                JOIN roles as t2 ON t1.role_id = t2.role_id
                WHERE t1.user_id = :user_id";
        $sth = $GLOBALS["DB"]->prepare($sql);
        $sth->execute(array(":user_id" => $id));
        return $sth->fetchAll( PDO::FETCH_ASSOC );
    }

    public static function has_role( $id, $role_name ) {
        $roles = array_column( self::get_roles( $id ), "role_name" );
        return in_array( $role_name, $roles );
    }

    // populate roles with their associated permissions
    protected function initRoles() {
        $this->roles = array();
        $rows = self::get_roles($this->id);
        foreach ($rows as $row) {
            $this->roles[$row["role_name"]] = Role::getRolePerms($row["role_id"]);
        }
    }

    // check if user has a specific privilege
    public function hasPrivilege($perm) {
        $perm = preg_replace( "/_([0-9])+$/", "", $perm );
        foreach ($this->roles as $role) {
            if ($role->hasPerm($perm)) {
                return true;
            }
        }
        return false;
    }
}