<html>
	<head>
		<title>Start New Battle</title>
		<link href="/assets/css/bootstrap-datepicker.min.css" rel="stylesheet">
	</head>
	<body>
		
		<?= $header; ?>

		<main class="container">
		  <?php $validation = \Config\Services::validation();?>
		  <?= $validation->listErrors() ?>
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
		  	<form action="/user/save_battle_bet" id="save-battle-form" method="post" class="form-horizontal" role="form">
		  	  <div class="mb-3">
			    <label for="media_selected_id" class="form-label">Select Movie/TV</label>
			    <!-- select code -->
			    <?php
			    	if (!empty($medias))
			    	{
			    		?>
			    		<select id="media_selected_id" name="media_selected_id" class="form-control" onchange="changeBetDateSelection()" required>
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
			    <label for="player2_id" class="form-label">Select User:</label>
			    <!-- select code -->
			    <?php
			    	if (!empty($users))
			    	{
			    		?>
			    		<select id="player2_id" name="player2_id" class="form-control" required>
			    			<option value="">Select</option>
			    		<?php
			    		foreach($users as $key => $row)
			    		{
			    			?>
			    			<option value="<?= $row['id'];?>"><?=$row['username'];?></option>
			    			<?php
			    		}
			    		?>
			    		</select>
			    		<?php
			    	}
			    ?>
			  </div>
			  <div class="mb-3">
			    <label for="battle_description" class="form-label">Battle Description:</label>
			    <textarea cols="5" rows="10" name="battle_description" id="battle_description" class="form-control" required></textarea>
			  </div>
			  <div class="form-check">
			  	<input class="form-check-input" type="radio" name="prediction_type" id="player_for" value="1" checked>
				<label class="form-check-label" for="player_for">
				  With Prediction
				</label>
			  </div>
			  <div class="form-check">
			  	<input class="form-check-input" type="radio" name="prediction_type" id="player_against" value="2">
				<label class="form-check-label" for="player_against">
				  Against Prediction
				</label>
			  </div>
			  <div class="mb-3">
			    <label for="battle_amount" class="form-label">Bet Amount</label>
			    <input type="number" min="0" class="form-control" name="battle_amount" id="battle_amount" required>
			  </div>
			  <div class="mb-3">
			    <label for="battle_mode" class="form-label">Battle Type:</label>
			    <select class="form-control" name="battle_mode" id="battle_mode" required>
			    	<option value="private">Private</option>
			    	<option value="public">Public</option>
			    </select>
			  </div>
			  <div class="mb-3 additiona-bet-wrapper d-none">
			  	<label for="additional_bet_amount" class="form-label">Fixed amount for additional Bets:</label>
			  	<input type="number" min="0" name="additional_bet_amount" id="additional_bet_amount" class="form-control"/>
			  </div>
			  <div class="mb-3">
			    <label for="battle_end_date" class="form-label">Bet Date</label>
			    <input type="text" class="form-control" name="battle_end_date" id="battle_end_date" required>
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
			if($('#media_selected_id').val() !== '')
			{
				let new_date = $('#media_selected_id option:selected').attr('data-date');
				$('#battle_end_date').datepicker('destroy');
				$('#battle_end_date').datepicker({
					format: 'yyyy-mm-dd',
					startDate: new_date
				});
				$('#battle_end_date').removeAttr('disabled');
			}
			else
			{
				$('#battle_end_date').attr('disabled', 'disabled');
			}
		}

		$(document).on('change', '#battle_mode', function() {
			let mode = $(this).val();
			if(mode == 'public')
			{
				$('.additiona-bet-wrapper').removeClass('d-none');
				$('#additional_bet_amount').attr('required', 'required');
			}
			else
			{
				$('.additiona-bet-wrapper').addClass('d-none');
				$('#additional_bet_amount').removeAttr('required');
			}
		});
	</script>
</html>