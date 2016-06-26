<?php

$records = array(); $node = array();

if ( isset( $_POST['l_id'] ) ) {
  $l_id = $_POST['l_id'];
  $l_name = $_POST['name'];

  $node["name"] = $name;
  $blogs = Blog::get_by_landing( $l_id );
  if ( $blogs ) {
      for ( $i=0; $i<count($blogs); $i++ ) {
          $b_id = $blogs[$i]["c_id"];
          $node["blogs"][$i] = array(
              "id" => $b_id,
              "name" => $blogs[$i]["name"],
              "state" => array( "selected" => false ) );
      }
  }
}

if ( isset( $_POST['offer_id'] ) && $_POST['offer_id'] > 0 ) {
  $raw_content = Offer::getContent( $_POST['offer_id'] );

  if ($raw_content) {
    foreach ( $raw_content as $c) {
      $offer_content[$c['landing_id']]["blogs"][$c["blog_id"]] = $c["blog_id"];
    }

    $n=0;
    foreach ($offer_content as $i=>$c) {
      $landing = Content::get_by_id($i);
      $records[$n]["text"] = $landing["name"] . " <em class='remove-node' data-node='landing-{$i}'>Удалить</em>";
	    $records[$n]["id"] = "landing-" . $i;
      $records[$n]["state"] = array("selected" => true);
      $j = 0;
      foreach (array_keys($c['blogs']) as $blog_id) {
        if ($blog_id > 0) {
          $blog = Content::get_by_id($blog_id);
          $records[$n]["children"][$j] = array(
            "id" => "landing-" . $i . "-blog-" . $blog_id,
            "text" => $blog["name"] . " <em class='remove-node' data-node='landing-{$i}-blog-{$blog_id}'>Удалить</em>",
            "icon" => "fa fa-file-code-o",
            "state" => array("selected" => true));
          $j++;
        }
      }

      $n++;
    }
  }

}

echo json_encode( array( "tree" => $records, "node" => $node));
?>
