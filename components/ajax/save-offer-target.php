<?php

if ( isset($_POST) ) {

  $post = $_POST;
  $all = ( isset( $_SESSION['offerPrices'] ) ) ? $_SESSION['offerPrices'] : array();

  foreach ( $post as $country ) {
    $code = $country['country_code'];
    $t = $country['t_id'];
    $all[$code]['targets'][$t] = $country;
    $all[$code]['targets'][$t]['label'] = Offers::$target[$t];
    $all[$code]['targetsCount'] = count( $all[$code]['targets'] ) + 1;
  }

  $_SESSION['offerPrices'] = $all;
}

$smarty->assign( 'view', 'full' );
$smarty->assign( 'prices', $_SESSION['offerPrices']);
$smarty->display( 'admin' . DS . 'offers' . DS . 'ajax' . DS . 'target-table.tpl' );

die();

?>