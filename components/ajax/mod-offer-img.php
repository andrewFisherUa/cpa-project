<?php

    $action = $_POST['action'];
    $images = $_SESSION['offerImages']['images'];

    if ( $action == 'add' ) {
        $name = $_POST['name'];
        $images[] = array( 'id' => 0, 'name' => $name );
    }

    if ( $action == 'remove' ) {
        $id = $_POST['id'];
        $images[$id]['name'] = null;
    }

    $count = 0;

    foreach ( $images as &$im ) {
        if ( $action == 'set-main' ) unset($im['main']);
        if ( !is_null($im) ) $count++;
    }


    if ( $action == 'set-main' ) {
        $_SESSION['offerImages']["mainImage"] = array(
            "id" => $_POST["id"],
            "name" => $_POST["name"]);
        die();
    }


    $_SESSION['offerImages']['images'] = $images;

    $smarty->assign('offerImages', $_SESSION['offerImages']);
    $smarty->assign('imagesCount', $count);
    $smarty->display( 'admin' . DS . 'offers' . DS . 'ajax' . DS . 'images-table.tpl' );

    die();

?>
