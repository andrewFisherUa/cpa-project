<?php

$action = $_POST['action'];
$response = array();

if ( $action == "edit" ) {
	$article_id = $_POST['article_id'];
	$article = ( $article_id > 0 ) ?  Article::get_by_id( $article_id ) : array( "article_id" => 0 );
	$smarty->assign('article', $article);
	$smarty->assign('status', Article::$status_list );
	$smarty->assign('rubrics', Article::get_rubrics() );

	$max_weight = $GLOBALS['DB']->query("SELECT max(weight) FROM articles")->fetchColumn();
	if ($max_weight < 10) {
		$max_weight = 10;
	} else {
		$max_weight += 1;
	}

	$smarty->assign('weight', range(0,$max_weight));
	$response['form'] = $smarty->fetch( 'admin' . DS . 'faq' . DS . "ajax" . DS . "article-form.tpl" );
}

if ( $action == "save" ) {
	$article = new Article( $_POST );
	$article->save();
}

if ( $action == "remove" ) {
	Article::remove( $_POST['article_id'] );
}

echo json_encode( $response );

?>