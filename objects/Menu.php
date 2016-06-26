<?php

class Menu {
    private $m_id;
    private $title;
    private $description;
    private $parent = 0; // 0 - has no parent link
    private $weight = 0; // menu links with smaller weights are displayed before links with larger weights
    private $link;
    private $css;

    static $menu_html = '';

    public function __construct( $data ) {
        $this->m_id = ( isset($data['m_id']) ) ? $data['m_id'] : 0;
        $this->title = ( isset($data['title']) ) ? $data['title'] : 'Без названия';
        $this->description = ( isset($data['description']) ) ? $data['description'] : '';
        $this->parent = ( isset($data['parent']) ) ? $data['parent'] : 0;
        $this->weight = ( isset($data['weight']) ) ? $data['weight'] : 1;
        $this->link = ( isset($data['link']) ) ? $data['link'] : '';
        $this->css = ( isset($data['css']) ) ? $data['css'] : '';
        $this->has_children = ( isset($data['has_children']) ) ? $data['has_children'] : 0;
    }

    public static function getByLink($link) {
        $query = "SELECT * FROM menus WHERE link = :link";
        $stmt = $GLOBALS['DB']->prepare($query);
        $stmt->bindParam(":link", $link, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function deleteByLink($link) {
        $stmt = $GLOBALS['DB']->prepare("DELETE FROM menus WHERE link = :link");
        $stmt->bindParam(":link", $link, PDO::PARAM_STR);
        $stmt->execute();
    }

    public function save() {
        $query = "";
        if ( $this->parent != 0 ) {
            $query = "UPDATE menus SET has_children = 1 WHERE m_id = :parent; ";
        }
        if ( $this->m_id == 0 ) {
            $query .= "INSERT INTO menus(title, description, parent, weight, link, css, has_children )
                      VALUES ( :title, :description, :parent, :weight, :link, :css, :has_children )";
        } else {
            $query .= "UPDATE menus SET title = :title, description = :description, parent = :parent, weight = :weight, link = :link, css = :css, has_children = :has_children WHERE m_id = :m_id";
        }

        $stmt = $GLOBALS['DB']->prepare($query);
        if ( $this->m_id != 0 ) {
            $stmt->bindParam(':m_id', $this->m_id, PDO::PARAM_INT );
        }
        $stmt->bindParam(':title', $this->title, PDO::PARAM_STR );
        $stmt->bindParam(':description', $this->description, PDO::PARAM_STR );
        $stmt->bindParam(':parent', $this->parent, PDO::PARAM_INT );
        $stmt->bindParam(':weight', $this->weight, PDO::PARAM_INT );
        $stmt->bindParam(':link', $this->link, PDO::PARAM_STR );
        $stmt->bindParam(':css', $this->css, PDO::PARAM_STR );
        $stmt->bindParam(':has_children', $this->has_children, PDO::PARAM_INT );
        return $stmt->execute();
    }

    public static function get_item( $link_id ) {
        $query = "SELECT * FROM menus WHERE m_id = :m_id;";
        $stmt = $GLOBALS['DB']->prepare($query);
        $stmt->execute( array(':m_id' => $link_id) );
        return $stmt->fetch( PDO::FETCH_ASSOC );
    }

    public static function remove_item( $link_id ) {
        $query = "DELETE FROM menus WHERE m_id = :m_id; UPDATE menus SET parent = 0 WHERE parent = :m_id";
        $stmt = $GLOBALS['DB']->prepare($query);
        return $stmt->execute( array(':m_id' => $link_id) );
    }

    public static function get_child_links( $parent ){
        $query = "SELECT * FROM menus WHERE parent = :parent";
        $stmt = $GLOBALS['DB']->prepare($query);
        $stmt->execute( array(':parent' => $parent) );
        return $stmt->fetchAll( PDO::FETCH_ASSOC );
    }

    public static function get_all_links(){
        $stmt = $GLOBALS['DB']->query( "SELECT * FROM menus ORDER BY weight" );
        return $stmt->fetchAll( PDO::FETCH_ASSOC );
    }

    public static function build_menu() {
        $tree = array(); $dataset = array();
        $links = self::get_all_links();

        foreach ( $links as $item ) {
           $id = $item['m_id'];
           $dataset[$id] = $item;
        }

        foreach ($dataset as $id=>&$node) {
            if ($node['parent'] == 0) {
              $tree[$id] = &$node;
            } else {
              if (!isset($dataset[$node['parent']]['children'])) $dataset[$node['parent']]['children'] = array();
              $dataset[$node['parent']]['children'][$id] = &$node;
            }
        }

        return $tree;
    }

    public static function get_max_weight(){
        $stmt = $GLOBALS['DB']->query("SELECT MAX( weight ) FROM menus");
        return $stmt->fetchColumn();
    }


    public static function get_html($nodes = null, $indent=0) {
        static $html = '';
        if ( is_null($nodes)) $nodes = self::build_menu();

        if ($indent >= 20) return;  // Stop at 20 sub levels

        $roles = Role::get_all();

        foreach ( $roles as $role ) {
            $role_perms[$role['role_name']] = Role::getRolePerms( $role['role_id'], 'view_pages' );
        }

        $roles = $role_perms;

        foreach ($nodes as $node) {
            $html .= '<tr>';
            $p = str_repeat('&nbsp;',$indent*4);

            $html .= '<td>'.$p.'<a href="' . $node['link'] . '" target="_blank" title="'.$node['description'].'" class="item-link">' . $node['title'] . '<a></td>';
            $html .= '<td>'.$node['weight'].'</td>';

            $perm = 'view_' . str_replace('/', '_', $node['link']);
            $perm_id = Role::get_perm_id( $perm );

            foreach ( $roles as $label=>$role ) {
                $checked = $role->hasPerm( $perm ) ? "checked" : '';
                $html .= '<td class="text-center">';
                $html .= '<input type="checkbox" name="roles['.$label.'][perms][]" value="'.$perm_id.'" ' . $checked . '></td>';
            }

            $html .= '<td><a href="javascript:;" class="btn btn-xs default btn-editable edit-item" data-action="edit" data-id="'.$node['m_id'].'"><i class="fa fa-edit"></i> Редактировать</a>
                      <a href="javascript:;" class="btn btn-xs default btn-editable remove-item" data-action="remove" data-id="'.$node['m_id'].'"><i class="fa fa-trash"></i> Удалить</a></td>';
            $html .= '</tr>';
            if (isset($node['children'])) {
                self::get_html($node['children'], $indent+1);
            }

        }
        return $html;
    }

    public static function link_is_available ( $link ) {
        $u = Privileged_User::getByUsername( $_SESSION["user"]["login"] );
        $perm = "view_" . str_replace('/', '_', $link);
        return $u->hasPrivilege( $perm );
    }

    public static function get_nav_links ($nodes = null, $indent=0) {
        static $html = '';
        if ( is_null($nodes)) $nodes = self::build_menu();

        if ($indent >= 20) return;  // Stop at 20 sub levels

        foreach ($nodes as $node) {
            $class = "";

            // Проверяем есть ли доступные дочерние страницы
            if (empty($_GET['r']) && $node['link'] == "home") {
                $class = "active open";
            }

            if (empty($class)) {
                if (!empty($node['link']) && $_SERVER['REQUEST_URI'] == '/admin/' . $node['link']) {
                    $class = "active open";
                } else if (isset($node['children'])) {
                    $class = "";
                    foreach ( $node['children'] as $ch ) {
                        if ( $_SERVER['REQUEST_URI'] == '/admin/' . $ch['link'] ) {
                            $class = "active open";
                            break;
                        }
                    }
                }
            }

            // Если страница не доступна пользователю, не показываем ее в меню
            if ( !self::link_is_available($node['link']) ) continue;

            if (isset($node['children'])) {

              $html .= '<li class="'.$class.'"><a href="/admin/'.$node['link'].'">';
              if ( $node['css'] ) $html .= '<i class="'.$node['css'].'"></i>';

              $html .=  '<span class="title">'.$node['title'].'</span>';

              if ( $_SERVER['REQUEST_URI'] == '/admin/' . $node['link'] ) {
                $html .= '<span class="selected"></span>';
              }
              $html .=  '<span class="arrow open"></span>
                         </a>
                         <ul class="sub-menu">';
           } else {
               $class = ( $_SERVER['REQUEST_URI'] == '/admin/' . $node['link'] ) ? "active" : "";
               $html .= '<li class="'.$class.'"><a href="/admin/' . $node['link'] .'" title="' . $node['description'] . '">';
               $html .= '<i class="' . $node['css'] . '"></i>';
               $html .= '<span class="title">' . $node['title'] . '</span>';
               if ( $_SERVER['REQUEST_URI'] == '/admin/' . $node['link'] ) {
                 $html .= '<span class="selected"></span>';
               }
               $html .= '</a></li>';
           }

            if (isset($node['children'])) {
                self::get_nav_links($node['children'], $indent+1);
                $html .= '</ul>';
            }

        }
        return $html;
    }

}