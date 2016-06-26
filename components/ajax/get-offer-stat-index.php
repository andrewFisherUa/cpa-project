<?php  

  function get_field($name, $value, $checked = FALSE){

    $c = ($checked == TRUE) ? "checked" : "";

    if ($name == "stat_cr" || $name == "specific_cr") {
      $mode_name = "cr_mode";
    }

    $mode_val = ($name == "stat_cr" || $name == "stat_epc") ?  "stat" : "specific";
    $mode_name = ($name == "stat_cr" || $name == "specific_cr") ?  "cr_mode" : "epc_mode";

    return "<div class='input-group'>
              <span class='input-group-addon'>
                <input type='checkbox' name='{$mode_name}' value='{$mode_val}' {$c} >
                <span></span>
              </span>
              <input type='text' class='form-control' value='{$value}' name='{$name}'>
            </div>";
  }

  $query = "SELECT t1.name, t1.id, t2.*
            FROM goods AS t1 LEFT JOIN offer_stat AS t2 ON t1.id = t2.offer_id
            WHERE t1.offer_status IN ('active', 'disabled')";

  $filter = new Filter;

  $offer_id = $filter->sanitize($_POST["params"]["id"], "int");
  if ($offer_id > 0) {
    $query .= " AND t1.id = {$offer_id} ";
  }

  $iTotalRecords = $GLOBALS["DB"]->query($query)->rowCount();
  $iDisplayLength = intval($_REQUEST['length']);
  $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
  $iDisplayStart = intval($_REQUEST['start']);  

  $data = [];
  $query .= " ORDER BY t1.id DESC LIMIT {$iDisplayStart}, {$iDisplayLength} ";
  $stmt = $GLOBALS['DB']->query($query);

  $i = 0;
  while ($a = $stmt->fetch(PDO::FETCH_ASSOC)){

    $id = $a["id"];

    $data[$i][] = '<a href="/admin/offers/view/' . $a["id"] . '">'.$a["id"] . ': ' .  $a["name"] . '</a>';

    // epc
    $data[$i][] = get_field("stat_epc", $a["stat_epc"], $a["epc_mode"] == "stat");
    $data[$i][] = get_field("specific_epc", $a["specific_epc"], $a["epc_mode"] == "specific");

    $c = ($a["epc_mode"] == "no_data") ? "checked" : "";
    $data[$i][] = "<div class='text-center'><input type='checkbox' value='no_data' name='epc_mode' {$c}></div>";

    //cr
    $data[$i][] = get_field("stat_cr", $a["stat_cr"], $a["cr_mode"] == "stat");
    $data[$i][] = get_field("specific_cr", $a["specific_cr"], $a["cr_mode"] == "specific");    

    $c = ($a["cr_mode"] == "no_data") ? "checked" : "";
    $data[$i][] = "<div class='text-center'><input type='checkbox' value='no_data' name='cr_mode' {$c}></div>";

    $data[$i][] = "<span class='btn btn-sm blue btn-save' data-id='{$id}'>Сохранить</span>";

    $i++;
  }

  $records = [
    "data" => $data,
    "recordsTotal" => $iTotalRecords,
    "recordsFiltered" => $iTotalRecords,
    "draw" => intval($_REQUEST['draw'])
  ];

  echo json_encode($records);
?>