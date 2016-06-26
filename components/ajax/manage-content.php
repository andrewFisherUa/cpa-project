<?php

  $filter = new Filter;

  $respond = [];
  $action = $filter->sanitize($_POST['action'], ["string", "striptags"]);

  // Remove content
  if ( $action == "remove" ) {
    $type = $filter->sanitize($_POST['type'], ["string", "striptags"]);
    $id = $filter->sanitize($_POST['id'], "int");

    if ( $type == "landing" )  Landing::delete($id);
    if ( $type == "blog" )  Blog::delete($id);

    Audit::addRecord([
      "group" => "content",
      "subgroup" => "edit",
      "action" => "Удаление контента `{$id}`",
    ]); 
  }

  // Save content
  if ( $action == "save" ) {
     $error = false;

     $data = [
      "type" => $filter->sanitize($_POST['type'], ["string", "striptags"]),
      "id" => $filter->sanitize($_POST['id'], "int"),
      "name" => $filter->sanitize($_POST['name'], ["string", "striptags"]),
      "link" => $filter->sanitize($_POST['link'], ["string", "striptags"]),
      "groups" => $filter->sanitize($_POST['groups'], "int"),
      "group" => $filter->sanitize($_POST['group'], "int"),
      "landings" => $filter->sanitize($_POST['landings'], "int"),
     ];

     var_dump($data);
     die();

     if ($data['type'] == "landing") {
        $content = new Landing($data);
     }

     if ($data['type'] == "blog" ) {
       $content = new Blog($data);
     }

     if ( $content->check_field( 'name', $content->name ) != false ) {
        $respond['error'] .= "Контент `{$content->name}` уже существует. <br>";
        $error = true;
     }

     if ( $content->check_field( 'link', $content->link ) != false ) {
        $respond['error'] .= "Папка `{$content->link}` уже занята. <br>";
        $error = true;
     }

      $respond['content'] = $data;

      if ( !$error ) {
        $content->save();

        $data['id'] = $content->id;

        Audit::addRecord([
          "group" => "content",
          "subgroup" => "edit",
          "action" => "Создание / Редактирование контента `{$content->id}`: `{$content->name}`",          
        ]); 
      }
  }

  // Загрузка формы редактирования группы
  if ( $action == "get-groups-form" ) {
    $id = $filter->sanitize($_POST['id'], "int");

    $smarty->assign('group', Content::get_group($id));
    $respond['form'] = $smarty->fetch( 'admin' . DS . 'content' . DS . 'ajax' . DS . 'edit-group.tpl' );
  }

  // Remove content group
  if ( $action == "remove-group" ) {
    $id = $filter->sanitize($_POST['id'], "int");
    Content::delete_group($id);

    Audit::addRecord([
      "group" => "content",
      "subgroup" => "delete",
      "action" => "Удаление группы контента `{$id}`"
    ]);
  }

  // Add new content group
  if ( $action == "save-group" ) {
    $id = $filter->sanitize($_POST['id'], "int");
    $name = $filter->sanitize($_POST['name'], ["string", "striptags"]);

    if ( $id > 0 ) {
      $r = Content::upd_group( $id, $name );
    } else {
      $r = Content::add_group( $name );
    }

    if ( $r == false ) {
      $respond['error'] = "Группа `{$name}` уже существует";
    } else {
      Audit::addRecord([
        "group" => "content",
        "subgroup" => "edit",
        "action" => "Создание / Редактирование группы контента `{$name}`"
      ]);
    }
  }


  echo json_encode( $respond );
  die();

?>