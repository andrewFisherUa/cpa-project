<?php
  /*
   * Paging
   */

  $isSupport = User::isAdmin() || User::isSupport();
  $uid = 0;

  $raw_data = $_REQUEST['f'];

  $filter = new Filter;

  $c = [];

  if (!empty($raw_data['id'])) {
    $c[] = "t.ticket_id = " . $filter->sanitize($raw_data['id'], "int");
  }

  if (($raw_data['user_id'] > 0) && $isSupport) {
    $c[] = "t.user_id = " .  $filter->sanitize($raw_data['user_id'], "int");
  }

  if (!$isSupport) {
    $uid = User::get_current_user_id();
    $c[] = "t.user_id = " . $uid;
  }  

  if (!empty($raw_data['subject'])) {
    $c[] = "t.subject LIKE '%" .  $filter->sanitize($raw_data['subject'], ["string", "striptags"]) . "%'";
  }

  if (isset($raw_data['closed']) && $raw_data['closed'] != '-1') {
    $c[] = "t.closed = " . $filter->sanitize($raw_data['closed'], "int!");
  }

  if (!empty($raw_data['created_from'])){
    $raw_data['created_from'] = $filter->sanitize($raw_data['created_from'], ["string", "striptags"]);
    $data = explode("/", $raw_data['created_from']);
    $c[] = "t.created > " . mktime(0, 0, 0, $data[1], $data[0], $data[2]);
  }

  if ( !empty($raw_data['created_to'])){
    $raw_data['created_to'] = $filter->sanitize($raw_data['created_to'], ["string", "striptags"]);
    $data = explode("/", $raw_data['created_to']);
    $c[] = "t.created < " . mktime(0, 0, 0, $data[1], $data[0], $data[2]);
  }

  $query = "SELECT t.*, t.ticket_id AS id, u.login, count(tm.message_id) as m_count, count(ta.tm_id) as a_count
            FROM tickets as t inner join tickets_messages as tm on t.ticket_id = tm.ticket_id
                              inner join users as u on t.user_id = u.user_id
                              left join tickets_attachments as ta on ta.tm_id = tm.message_id ";

  if (!empty($c)) {
    $query .= " WHERE " . implode(" AND ", $c);
  }

  $query .= " GROUP BY t.ticket_id
              ORDER BY t.changed DESC";

  $stmt0 = $GLOBALS['DB']->query($query);
  $iTotalRecords = $stmt0->rowCount();
  $iDisplayLength = intval($_REQUEST['length']);
  $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
  $iDisplayStart = intval($_REQUEST['start']);
  $sEcho = intval($_REQUEST['draw']);

  $stmt = $GLOBALS['DB']->query($query . " LIMIT " . $iDisplayStart . ", " . $iDisplayLength);


  $query = "SELECT DISTINCT ticket_id
            FROM tickets_messages
            WHERE to_uid = {$uid} AND seen = 0";
  $stmt1 = $GLOBALS['DB']->query($query);
  $unread = $stmt1->fetchAll(PDO::FETCH_COLUMN);

  $records = [
    "data" => []
  ];

  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

    $priority = ( $row['urgent'] == 1 ) ? ' <span class="label label-sm label-danger">Срочно</span> ' : '';
    $status = ( $row['closed'] == 0 ) ? '<span class="label label-sm label-info">Открыт</span>' : '<span class="label label-sm label-warning">Закрыт</span>';
    $attachment = ( $row['a_count'] > 0 ) ? '<i class="fa fa-paperclip"></i>' : '';
    $is_unread = ( in_array($row['id'], $unread) ) ? 'unread' : '';

    $temp = [
     "<span class='{$is_unread}'>" . $row['id'] . "</span>",
     '<a href="/admin/tickets/' . $row['id'] . '">' . $attachment . " " . $row['subject'] .'</a>' . $priority,
     $row['m_count'],
     date("d.m.Y H:i", $row['created'])];

    if ($isSupport) {
      $temp[] = $row['login'];
    }

    if ($row['last_reply'] == 0) {
      $a = "Служба поддержки";
      $c = ($isSupport) ? "warning" : "success";
    } else {
      $a = ($row['last_reply'] == User::get_current_user_id()) ? "Вы" : $row['login'];
      $c = ($isSupport) ? "success" : "warning";
    }

    $temp[] = date("d.m.Y H:i", $row['changed']) . ' <span class="label label-' . $c . ' label-sm">'. $a . '</span> ';;
    $temp[] = $status;

    $records["data"][] = $temp;
  }

  $records["draw"] = $sEcho;
  $records["recordsTotal"] = $iTotalRecords;
  $records["recordsFiltered"] = $iTotalRecords;

  echo json_encode($records);
?>