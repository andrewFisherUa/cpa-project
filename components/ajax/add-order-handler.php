<?php

$action = $_POST["action"];
$response = array();

if ( $action == 'get-country-cats' ) {

	$query = "SELECT DISTINCT c.* FROM categories AS c, goods As g, goods2categories AS gc, goods2countries AS gco
              WHERE c.id = gc.c_id AND g.id = gco.g_id AND gco.country_code = :code";

	$stmt = $GLOBALS['DB']->prepare( $query );
	$stmt->bindParam(':code', $_POST['code'], PDO::PARAM_STR);
	$stmt->execute();

	$response['rows'] = "<option value='-1'>Выберите категорию</option>";

	while ( $row = $stmt->fetch( PDO::FETCH_ASSOC ) ) {
		$response['rows'] .= "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";
	}
}

if ( $action == 'get-cat-products' ) {

	$query = "SELECT DISTINCT g.* FROM categories AS c, goods As g, goods2categories AS gc, goods2countries AS gco
              WHERE c.id = gc.c_id AND g.id = gco.g_id AND gco.country_code = :code AND gc.c_id = :c_id";

	$stmt = $GLOBALS['DB']->prepare( $query );
	$stmt->bindParam(':code', $_POST['code'], PDO::PARAM_STR);
	$stmt->bindParam(':c_id', $_POST['с_id'], PDO::PARAM_STR);
	$stmt->execute();

	$response['rows'] = "<option value='-1'>Выберите товар</option>";

	while ( $row = $stmt->fetch( PDO::FETCH_ASSOC ) ) {
		$response['rows'] .= "<option value='" . $row['id'] . "'>" . $row['id'] . ": " . $row['name'] . "</option>";
	}
}

if ( $action == "add-product-to-order" ) {
	$product = Product::getInstance($_POST['id'], $_POST['code']);
	$price = $product->getPrice();

    $response['rows'] = "<tr id='item-{$product->getId()}' class='item'>
               <td class='num'>".$_POST['num']."</td>
               <td>
                <img class='product-img' src='{$product->getMainImagePath()}' alt=''>
                {$product->getName()}
                </td>
               <td class='price'><span>{$price->getValue()}</span>&nbsp;{$price->getCurrency()}</td>
               <td><input class='qty form-control' type='number' value='1' min='1' data-good='{$product->getId()}'></td>
               <td class='sum'><span>{$price->getValue()}</span>&nbsp;{$price->getCurrency()}</td>
               <td><span class='glyphicon glyphicon-trash remove-item' aria-hidden='true' data-good='{$product->getId()}'></span></td>
               </tr>";
}

echo json_encode( $response );

?>