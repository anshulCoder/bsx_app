<html>
	<head>
		<title>Add new Bet</title>
		<link href="/assets/css/bootstrap-datepicker.min.css" rel="stylesheet">
	</head>
	<body>
		
		<?= $header; ?>

		<main class="container">
		  <?php
		  	$session = \Config\Services::session();
		  	$wallet_error = $session->getFlashData('wallet_error');
		  	if(isset($wallet_error))
		  	{
		  		?>
		  		<div class="toast-container position-absolute p-3 top-0 start-50 translate-middle-x" id="toastPlacement">
				    <div class="toast">
				      <div class="toast-header">
				        <strong class="me-auto">Error!</strong>
				        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
				      </div>
				      <div class="toast-body">
				        <?= $wallet_error; ?>
				      </div>
				    </div>
				</div>
		  		<?php
		  	}
		  ?>
		  <div class="py-5 px-3">
		  	<form action="/user/save_accuracy_bet" id="save-media-form" method="post" class="form-horizontal" role="form">
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
			    <label for="predicted_amount" class="form-label">Predicted Amount</label>
			    <input type="number" min="0" class="form-control" name="predicted_amount" id="predicted_amount" required>
			  </div>
			  <div class="mb-3">
			    <label for="bet_date" class="form-label">Bet Date</label>
			    <input type="text" class="form-control" name="bet_date" id="bet_date" required>
			  </div>
			  <input type="hidden" name="user_id" value="<?= $session->get('user_id'); ?>"/>
			  <button type="submit" class="btn btn-primary">Submit</button>
			</form>
		  </div>

		</main><!-- /.container -->
	</body>
	<script src="/assets/js/bootstrap-datepicker.min.js"></script>
	<script>
		var toastElList = [].slice.call(document.querySelectorAll('.toast'));
		var toastList = toastElList.map(function (toastEl) {
		  return new bootstrap.Toast(toastEl, {delay: 10000})
		});
		if(toastList[0]) toastList[0].show();
		$(document).ready(function() {
			changeBetDateSelection();
		});

		function changeBetDateSelection()
		{
			if($('#media_name').val() !== '')
			{
				let new_date = $('#media_name option:selected').attr('data-date');
				$('#bet_date').datepicker('destroy');
				$('#bet_date').datepicker({
					format: 'yyyy-mm-dd',
					startDate: new_date
				});
				$('#bet_date').removeAttr('disabled');
			}
			else
			{
				$('#bet_date').attr('disabled', 'disabled');
			}
		}
	</script>
</html>