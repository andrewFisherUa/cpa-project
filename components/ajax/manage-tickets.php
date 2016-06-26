<?php

$filter = new Filter;

$isSupport = User::isAdmin() || User::isSupport();

$response = [
    "errors" => [],
    "success" => ""
];

$action = $filter->sanitize($_POST['action'], ["string", "striptags"]);
$user_id = User::get_current_user_id();

if (!$isSupport && $action == "add-ticket") {

    $post = [
        "subject" => $filter->sanitize($_POST['subject'], ["string", "striptags"]),
        "urgent" => $filter->sanitize($_POST['urgent'], ["int"]),
        "message" => $filter->sanitize($_POST['message'], ["string", "striptags"]),
    ];

    if (empty($post["subject"])) {
        $response["errors"][] = "Введите тему тикета.";
    }

    if (empty($post["message"])) {
        $response["errors"][] = "Введите сообщение.";
    }

    if (empty($response["errors"])) {
        $ticket = new Ticket([
            "subject" => $post['subject'],
            "user_id" => $user_id,
            "urgent" => $post['urgent'],
        ]);

        $message = new TicketMessage([
            "from_uid" => $user_id,
            "to" => 0,
            "message" => $post['message'],
        ]);

        $ticket->addMessage($message);

        if ($ticket->save()) {

            Ticket::sendEmail($ticket, "new_message");

            Audit::addRecord([
                "gruop" => "ticket",
                "subgroup" => "create",
                "action" => "Создание тикета",
                "details" => [
                    "ticket_id" => $ticket->getId(),
                    "user_id" => $user_id,
                    "subject" => $post['subject'],
                    "message" => $post['message']
                ]
            ]);

            $response["success"] = "Тикет успешно создан.";
        }
    }
}

// Add message
if ($action == "add-message") {

    $data = [
        "from_uid" => $filter->sanitize($_POST['from_uid'], "int"),
        "to_uid" => $filter->sanitize($_POST['to_uid'], "int"),
        "message" => $filter->sanitize($_POST['message'], ["string", "striptags"]),
        "ticket_id" => $filter->sanitize($_POST['ticket_id'], "int"),
    ];

    if (empty($data["message"])) {
        $response["errors"][] = "Введите сообщение.";
    }

    if (empty($data["ticket_id"])) {
        $response["errors"][] = "Неверный ID тикета.";
    }  else {
        $ticket = Ticket::getInstance($data["ticket_id"]);

        if (!$ticket->isAvailableToUser($user_id)) {
            $response["errors"][] = "Неверный ID тикета.";
        }
    }

    if (empty($response["errors"])) {
        $message = new TicketMessage($data);

        $audit_record = [
            "group" => "ticket",
            "subgroup" => "add_message",
            "action" => "Создание нового сообщения к тикету.",
            "details" => [
                "user_id" => $data["from_uid"],
                "ticket_id" => $data["ticket_id"],
                "message" => $data["message"]
            ]
        ];

        if ($message->save()) {

            Ticket::sendEmail($ticket, "new_message");

            if ($isSupport ) {
                Ticket::setViewed($ticket->getId(), $user_id);
            }

            $response["success"] = "Сообщение отправлено";
            $response["message"] = '
<div class="general-item-list">
    <div class="item item-user">
        <div class="item-head">
            <div class="item-details">
                <span class="item-label pull-right">' . date("d.m.Y H:i") . '</span>
                <span class="item-name">Вы</span>
            </div>
        </div>
        <div class="item-body">' . $data["message"] . '</div>
    </div>
</div>';


        } else {
            $audit_record["action"] .= " Ошибка.";
            $response["success"] = "Ошибка при отправке сообщения";
        }

        Audit::addRecord($audit_record);
    }

    
}

// Close ticket
if ( $action == "close-ticket" ) {

    $ticket_id = $filter->sanitize($_POST['ticket_id'], "int");

    if ($ticket_id > 0) {

        $ticket = Ticket::getInstance($ticket_id);

        if (!$ticket->isAvailableToUser($user_id)) {
            $response["errors"][] = "Неверный ID тикета.";
        } 

        if (empty($response["errors"])) {
            if ($isSupport ){
                Ticket::setViewed($ticket_id, $user_id);
            } 

            Ticket::close($ticket_id);  

            Audit::addRecord([
                "group" => "ticket",
                "subgroup" => "close",
                "action" => "Закрытие тикета",
                "details" => [
                    "ticket_id" => $ticket_id
                ]
            ]);

            $response["success"] = "Тикет успешно закрыт.";
        }
    } else {
        $response["errors"][] = "Неверный ID тикета.";
    }

    Ticket::sendEmail($ticket, "close_ticket");
}

// Reopen ticket
if ( $action == "open-ticket" ) {

    $ticket_id = $filter->sanitize($_POST['ticket_id'], "int");

    if ($ticket_id > 0) {

        $ticket = Ticket::getInstance($ticket_id);

        if (!$ticket->isAvailableToUser($user_id)) {
            $response["errors"][] = "Неверный ID тикета.";
        } 

        Ticket::open($ticket_id);
        
        Audit::addRecord([
            "group" => "ticket",
            "subgroup" => "close",
            "action" => "Повторное открытие тикета",
            "details" => [
                "ticket_id" => $ticket_id
            ]
        ]);

        $response["success"] = "Тикет успешно открыт.";
    } else {
        $response["errors"][] = "Неверный ID тикета.";
    }
}

echo json_encode($response);

?>