<?php

require_once( PATH_ROOT . DS . 'objects'. DS . 'Uploader.php' );

$path = ( isset($_REQUEST['path']) ) ? $_REQUEST['path'] : 'misc/uploads/';
$upload_dir = $_SERVER['DOCUMENT_ROOT'] . $_REQUEST['path'];

$uploader = new FileUpload($upload_dir);

// Handle the upload
$result = $uploader->handleUpload($upload_dir);

if (!$result) {
  exit(json_encode(array('success' => false, 'msg' => $uploader->getErrorMsg(), 'file' => $uploader->getFileName())));
}

echo json_encode(array('success' => true, 'file' => $uploader->getFileName(), 'path' => "/" .$path ));
