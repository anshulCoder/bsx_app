<html>
	<head>
		<title>Add new Sequel Bet</title>
		<link href="/assets/css/bootstrap-datepicker.min.css" rel="stylesheet">
	</head>
	<body>
		
		<?= $header; ?>

		<main class="container">

		  <div class="py-5 px-3">
		  	<form action="/user/save_sequel_bet" id="save-media-form" method="post" class="form-horizontal" role="form">
		  	  <div class="mb-3">
			    <label for="media_name" class="form-label">Select Movie/TV</label>
			    <!-- select code -->
			    <?php
			    	if (!empty($medias))
			    	{
			    		?>
			    		<select id="media_name" name="media_name" class="form-control" onchange="changeBetDateSelection()" required>
			    			<option value="">Select</option>
			    		<?php
			    		foreach($medias as $key => $row)
			    		{
			    			?>
			    			<option value="<?= $row['id'];?>" data-date="<?= $row['release_date'];?>"><?=$row['name'];?></option>
			    			<?php
			    		}
			    		?>
			    		</select>
			    		<?php
			    	}
			    ?>
			  </div>
			  <div class="mb-3">
			    <label for="bet_amount" class="form-label">Bet Amount</label>
			    <input type="number" min="0" class="form-control" name="bet_amount" id="bet_amount" required>
			  </div>
			  <div class="mb-3">
			    <label for="bet_day" class="form-label">Sequel Day:</label>
			    <input type="text" class="form-control" name="bet_day" id="bet_day">
			  </div>
			  <div class="mb-3">
			    <label for="bet_month" class="form-label">Sequel Month:</label>
			    <input type="text" class="form-control" name="bet_month" id="bet_month">
			  </div>
			  <div class="mb-3">
			    <label for="bet_year" class="form-label">Sequel Year:</label>
			    <input type="text" class="form-control" name="bet_year" id="bet_year" required>
			  </div>

			  <?php
			  	if(isset($row['']))
			  ?>

			  <?php $session = \Config\Services::session(); ?>
			  <input type="hidden" name="user_id" value="<?= $session->get('user_id'); ?>"/>
			  <button type="submit" class="btn btn-primary">Submit</button>
			</form>
		  </div>

		</main><!-- /.container -->
	</body>
	<script src="/assets/js/bootstrap-datepicker.min.js"></script>
	<script>
		$(document).ready(function() {
			changeBetDateSelection();
		});

		function changeBetDateSelection()
		{
			if($('#media_name').val() !== '')
			{
				let new_date = $('#media_name option:selected').attr('data-date');
				$('#bet_date').datepicker('destroy');
				$('#bet_day').datepicker({
					format: 'dd',
					startDate: new_date
				});
				$('#bet_month').datepicker({
					format: 'mm',
					minViewMode: 'months',
					startDate: new_date
				});
				$('#bet_year').datepicker({
					format: 'yyyy',
					minViewMode: 'years',
					startDate: new_date
				});
				$('#bet_day, #bet_month, #bet_year').removeAttr('disabled');
			}
			else
			{
				$('#bet_day, #bet_month, #bet_year').attr('disabled', 'disabled');
			}
		}
	</script>
</html>