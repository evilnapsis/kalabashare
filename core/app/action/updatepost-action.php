<?php
// print_r($_POST);
$product =  new PostData();
foreach ($_POST as $k => $v) {
	$product->$k = $v;
	# code...
}

////////////////////////////////////// / / / / / / / / / / / / / / / / / / / / / / / / /
$handle = new Upload($_FILES['file']);
if ($handle->uploaded) {
	$url="admin/storage/products/";
	$handle->Process($url);

    $product->file = $handle->file_dst_name;
//    $product->update_image();
}
////////////////////////////////////// / / / / / / / / / / / / / / / / / / / / / / / / /

//if(isset($_POST["is_public"])) { $product->is_public=1; }else{ $product->is_public=0; }
//if(isset($_POST["is_featured"])) { $product->is_featured=1; }else{ $product->is_featured=0; }

// $product->name = $_POST["name"];

 $product->update2();
$_SESSION["product_updated"]= 1;
Core::alert("Actualizado!");
Core::redir("index.php?view=client");
?>