<?php

  $params = $_REQUEST['params'];

  $query = "SELECT * FROM content as c";

  $a = [
    "c.type = 'blog'"
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

  if (array_key_exists('landing', $params)) {
    $a[] = "l.landing_id = " . $params['landing'];
    $query .= " INNER JOIN landing_blog as l ON c.c_id = l.blog_id ";
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

    $landings = "";
    $query = $query = "SELECT c.c_id, c.name 
                       FROM landing_blog as lb INNER JOIN content as c ON c.c_id = lb.landing_id 
                       WHERE lb.blog_id = " . $item['c_id'];
    $stmt2 = $GLOBALS['DB']->query($query);
    while ($b = $stmt2->fetch(PDO::FETCH_ASSOC)) {
      $landings .= "<a href='" . Content::get_preview_link($b['c_id']) . "'>" . $b['name'] . "</a><br/>";
    }

    $records["data"][] = array(
        $item['c_id'],
        $item['name'],
        $landings,
        '/blogs/' . $item['link'],
        "<a href='".Content::get_preview_link($item['c_id'])."' target='_blank'>Просмотр</a>",
        '<a href="/admin/blogs/edit/'.$item['c_id'].'" class="btn btn-sm btn-outline green" target="_blank"><i class="fa fa-edit"></i></a>
        <a href="#" class="btn btn-sm btn-outline red remove-item" data-id="'.$item['c_id'].'" data-content-type="landing"><i class="fa fa-trash"></i></a>'
    );
  }  

  $records["draw"] = $_REQUEST['draw'];
  $records["recordsTotal"] = $iTotalRecords;
  $records["recordsFiltered"] = $iTotalRecords;

  echo json_encode($records);
?>