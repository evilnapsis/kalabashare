<?php 
if(isset($_GET["cat"]) && $_GET["cat"]!=""){
$catx = CategoryData::getByPreffix($_GET["cat"]);
//print_r($catx);
$products = PostData::getPublicsByCategoryId($catx->id);
}else if(isset($_GET["opt"])){
if($_GET["opt"]=="news"){
$products = PostData::getNews();
}
else if($_GET["opt"]=="offers"){
$products = PostData::getOffers();
}

}else if(isset($_GET["act"]) && $_GET["act"]=="search"){
$products = PostData::getLike($_GET["q"]);

}else{
  $products = PostData::getAll();
}

 ?>

<section>
  <div class="container">

  <div class="row">
  <div class="col-md-3">




        <h1>Categorias</h1>

<?php
$cats = CategoryData::getPublics();
?>
<?php if(count($cats)>0):?>
<div class="list-group">
<?php foreach($cats as $cat):?>

  <a href="index.php?view=posts&cat=<?php echo $cat->short_name; ?>" class="list-group-item"><i class="fa fa-chevron-right"></i>  <?php echo $cat->name; ?></a>
<?php endforeach; ?>
</div>
<?php endif; ?>
  </div>
  <div class="col-md-9">

<h2 class="entry-title"><span><?php 
    if(isset($_GET["act"]) && $_GET["act"]!=""){ echo "Busqueda: ".$_GET["q"]; }else if(isset($_GET["cat"]) && $_GET["cat"]!="") { echo $catx->name; }else { echo "Publicaciones"; } ?></span></h2>
<br>
<?php

$nproducts = count($products);
$filas = $nproducts/4;
$extra = $nproducts%4;
if($filas>1&& $extra>0){ $filas++; }
$n=0;
?>
<?php if($nproducts>0):?>
<?php for($i=0;$i<$filas;$i++):?>
  <div class="row">
<?php for($j=0;$j<4;$j++):
$p=null;
if($n<$nproducts){
$p = $products[$n];
}
?>
<?php if($p!=null):
?>
  <div class="col-md-3">
  <h4 class="text-center"><?php echo $p->name; ?></h4>
<?php 
$in_cart=false;
if(isset($_SESSION["cart"])){
  foreach ($_SESSION["cart"] as $pc) {
    if($pc["product_id"]==$p->id){ $in_cart=true;  }
  }
  }

  ?>
<center>


<a href="index.php?view=post&product_id=<?php echo $p->id; ?>" class="btn btn-default">Ver mas</a>
                </center>

  </div>
<?php endif; ?>
<?php $n++; endfor; ?>
  </div>
<?php endfor; ?>
<?php else:?>
  <div class="jumbotron">
  <h2>No hay Publicaciones</h2>
  <p>No hay Publicaciones por mostrar</p>
  </div>
<?php endif;?>


  </div>
  </div>


  </div>
  </section>
  <br><br>
