<?php 
function smarty_function_component($params, &$smarty) { 
    /*$adodb = &$smarty->get_registered_object('adodb'); 
    $vars = &$smarty->get_registered_object('vars'); 
    $errors = &$smarty->get_registered_object('errors'); 
    $security = &$smarty->get_registered_object('security'); 
    */
    if (empty($params['name'])) { 
        $params['name'] = 'main'; 
    } 
    if (is_file(PATH_COMPONENTS . DS . $params['name'] . '_comp.php')) { 
        require(PATH_COMPONENTS . DS . $params['name'] . '_comp.php'); 
    } else { 
        echo 'Component <strong>' . $params['name'] . '</strong> not found';
        echo '<br />'.PATH_COMPONENTS . DS . $params['name'] . '_comp.php'; 
    } 
    //unset($adodb, $errors, $security, $vars); 
} 
?>