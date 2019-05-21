<?php 
$p = PostData::getById($_GET["product_id"]);

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

              <h2 class="entry-title"><span><?php echo $p->name; ?></span></h2>

<!--
              <div class="breadcrumb">
                <span></span>
                <a href="./">Inicio</a>
                <span class="crumbs-spacer"><i class="fa fa-angle-double-right"></i></span>
                <span class="current"><?php echo $p->name; ?></span>
              </div>
              -->
<?php if($p!=null):
$img = "admin/storage/products/".$p->file;

?>

  <div class="row">
  <div class="col-md-12">
  <hr>
  <p><?php echo $p->description; ?></p>
  <a href="<?php echo $img; ?>" target="_blank" class="btn btn-default"  >Descargar</a>
  <?php if ($p->client_id!=null):?>
  <p>Autor:
<?php
$client= ClientData::getById($p->client_id);
echo $client->name." ".$client->lastname;
?>
  </p>
<?php endif; ?>
  <p class="muted">Creado <?php echo $p->created_at; ?></p>



<?php endif; ?>



  </div>
  </div>


  </div>
  </section>
<br>