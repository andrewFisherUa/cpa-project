<?php

  $params = $_REQUEST['params'];

  $query = "SELECT * FROM content as c";

  $a = [
    "c.type = 'landing'"
  ];

  if (array_key_exists('id', $params)) {
    $a[] = "c.c_id = " . $params['id'];
  }

  if (array_key_exists('link', $params)) {
    $a[] = "c.link = '" . $params['link'] . "'";
  }

  if (array_key_exists('name', $params)) {
    $a[] = "c.name = '" . $params['name'] . "'";
  }

  if (array_key_exists('group', $params)) {
    $a[] = "g.g_id = " . $params['group'];
    $query .= " INNER JOIN content_group as g ON c.c_id = g.c_id ";
  }

  if (!empty($a)) {
    $query .= " WHERE " . implode(" AND ", $a);
  }

  $iTotalRecords = $GLOBALS['DB']->query($query)->rowCount();

  $query .= " ORDER BY c.created DESC ";
  $query .= " LIMIT " . $_REQUEST['start'] . ", " . $_REQUEST['length'];

  $records = ["data" => []];


  $stmt = $GLOBALS['DB']->query($query);

  while ($item = $stmt->fetch(PDO::FETCH_ASSOC)) {

    $query = "SELECT g.name 
              FROM groups AS g INNER JOIN content_group AS cg ON g.g_id = cg.g_id
              WHERE cg.c_id = " . $item['c_id'];
    $groups = $GLOBALS['DB']->query($query)->fetchAll(PDO::FETCH_COLUMN);

    $records["data"][] = array(
        $item['c_id'],
        $item['name'],
        '/landings/' . $item['link'],
        implode(", ", $groups),
        "<a href='".Content::get_preview_link($item['c_id'])."' target='_blank'>Просмотр</a>",
        '<a href="/admin/landings/edit/'.$item['c_id'].'" class="btn btn-sm btn-outline green" target="_blank"><i class="fa fa-edit"></i></a>
        <a href="#" class="btn btn-sm btn-outline red remove-item" data-id="'.$item['c_id'].'" data-content-type="landing"><i class="fa fa-trash"></i></a>'
    );
  }  

  $records["draw"] = $_REQUEST['draw'];
  $records["recordsTotal"] = $iTotalRecords;
  $records["recordsFiltered"] = $iTotalRecords;

  echo json_encode($records);
?>