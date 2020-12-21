<html>
	<head>
		<title>Add new Media</title>
		<link href="/assets/css/bootstrap-datepicker.min.css" rel="stylesheet">
	</head>
	<body>
		
		<?= $header; ?>

		<main class="container">

		  <div class="py-5 px-3">
		  	<form action="/admin/save_new_media" id="save-media-form" method="post" class="form-horizontal" role="form">
			  <div class="mb-3">
			    <label for="media_name" class="form-label">Name</label>
			    <input type="text" class="form-control" name="media_name" id="media_name" required>
			  </div>
			  <div class="mb-3">
			    <label for="media_description" class="form-label">Description</label>
			    <textarea cols="10" rows="5" name="media_description" id="media_description" class="form-control" required></textarea>
			  </div>
			  <div class="mb-3">
			    <label for="media_release_date" class="form-label">Release Date</label>
			    <input type="text" class="form-control" name="media_release_date" id="media_release_date" required>
			  </div>
			  <button type="submit" class="btn btn-primary">Submit</button>
			</form>
		  </div>

		</main><!-- /.container -->
	</body>
	<script src="/assets/js/bootstrap-datepicker.min.js"></script>
	<script>
		$(document).ready(function() {
			$('#media_release_date').datepicker({
				format: 'yyyy-mm-dd',
				startDate: new Date()
			});
		});
	</script>
</html>