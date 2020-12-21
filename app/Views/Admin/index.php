<html>
	<head>
		<title> Admin Settings </title>
	</head>

	<body>
		
		<?= $header; ?>
		<main class="container">

		  <div class="text-center py-5 px-3">
		  	<a href="admin/add" type="button" class="btn btn-primary" >Add new Media</a>
		    <table class="table table-responsive table-striped">
		    	<thead>
		    		<tr>
		    			<th>ID</th>
		    			<th>Name</th>
		    			<th>Description</th>
		    			<th>Release Date</th>
		    		</tr>
		    	</thead>
		    	<tbody>
		    		<?php
		    			foreach($medias as $key => $row) {
		    				?>
		    				<tr>
		    					<td><?= $row['id']; ?></td>
		    					<td><?= $row['name']; ?></td>
		    					<td><?= $row['description']; ?></td>
		    					<td><?= $row['release_date']; ?></td>
		    				</tr>
		    				<?php
		    			}
		    		?>
		    	</tbody>
		    </table>
		  </div>

		</main><!-- /.container -->
	</body>
</html>