<?php

    if ( $_SESSION['partner'] || $_SESSION['admin'] ) {
        header('Location: ' . "http://$_SERVER[HTTP_HOST]/admin$_SERVER[REQUEST_URI]" );
    }

    $cats = DataBase::getAssoc("SELECT DISTINCT c.id, c.name FROM categories AS c INNER JOIN goods AS g ON g.id_menu = c.id ORDER BY c.name ASC");
    $country = DataBase::getAssoc("SELECT * FROM country");

    $smarty->assign( 'country' , $country );
    $smarty->assign( 'cats' , $cats );

    if ( $_REQUEST['a'] == 'view') {
        $offer = Offers::getInstance( $_REQUEST['b'] );

        if ( $offer == false ) {
            echo "<script>window.location = '/offers/' </script>";
        }

        $offer->categories = $offer->getCategories();
        $offer->trafic_sources = $offer->getTraficSources();
        $offer->countries = $offer->getCountries();

        $trafic = Offers::getAllTraficSources();
        foreach ( $trafic as &$t ) {
            $t['checked'] = in_array( $t['id'], array_column( $offer->trafic_sources, 't_id' ) );
        }

        $offer->prices = Offers::getPrices($offer->id);
        foreach ( $offer->prices as &$single ) {
            $single['targets'] = Offers::getTargets( $offer->id, $single['country_code'] );
            $single['targetsCount'] = count( $single['targets'] ) + 1;
        }

        $smarty->assign_by_ref( 'offer' , $offer );
        $smarty->assign('trafic' , $trafic );
        $smarty->assign('logo', Offers::getLogo( $offer->id ) );
        $smarty->assign('targetPrices', Offers::getTargetPrices( $offer->id ) );
        $smarty->assign('offerPrices', $offer->prices );
    }


    if ( $_REQUEST['a'] == 'view' ) {
        $smarty->display( 'single-offer.tpl' );
    } else {
        $smarty->display('offers.tpl');
    }


    die();

?>