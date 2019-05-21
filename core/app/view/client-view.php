<?php if(isset($_SESSION["client_id"])):
$client = ClientData::getById($_SESSION["client_id"]);

?>
<div class="container">
<div class="row">

<div class="col-md-12">
<h3>Bienvenido, <?php echo $client->name." ".$client->lastname; ?></h3>
<a href="./?view=newpost" class="btn btn-default">Nuevo Archivo</a>

</div>

</div>
</div>
<?php

$buys = PostData::getAllByClient($_SESSION["client_id"]);


?>
<div class="container">
<div class="row">
	<div class="col-md-12">
	<h2>Mis Archivos</h2>
<?php if(count($buys)>0):?>
<table class="table table-bordered">
	<thead>
		<th></th>
		<th>Titulo</th>
		<th>Fecha</th>
		<th></th>
	</thead>
	<?php foreach($buys as $buy):
	$discount = 0;
	?>
	<tr>
			<td>#<?php echo $buy->id; ?></td>

		<td><?php echo $buy->name; ?></td>

		<td><?php echo $buy->created_at; ?></td>


                        <td style="width:185px;">
                        <a href="./index.php?view=post&product_id=<?php echo $buy->id; ?>" target="_blank" class="btn btn-default btn-xs"><i class="fa fa-link"></i></a> 

                        <a href="index.php?view=editpost&product_id=<?php echo $buy->id; ?>" class="btn btn-warning btn-xs"><i class="fa fa-edit"></i></a> 
                        <a href="index.php?action=delpost&product_id=<?php echo $buy->id; ?>" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i></a> 
                        </td>

	</tr>

	<?php endforeach; ?>
</table>
<?php else:?>
	<div class="jumbotron">
	<h2>No hay Archivos</h2>

	</div>
<?php endif; ?>
	</div>
</div>
</div>



<?php endif; ?>
