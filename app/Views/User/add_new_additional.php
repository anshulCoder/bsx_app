<html>
	<head>
		<title>Add Additional bet</title>
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
		  <?php $validation = \Config\Services::validation();?>
		  <?= $validation->listErrors() ?>
		  <div class="py-5 px-3">
		  	<form action="/user/save_additional_bet" id="save-additional-form" method="post" class="form-horizontal" role="form">
		  		<input type="hidden" name="bet_battle_id" value="<?= $battle_info['battle_id'];?>"/>
		  		<input type="hidden" name="bet_amount" value="<?= $battle_info['additional_bet_amount'];?>"/>
		  	  <div class="mb-3">
			    <label class="form-label">Bet Title: <?= $battle_info['media_name'];?></label><br>
			    <label class="form-label">Bet Amount: <?= $battle_info['additional_bet_amount']; ?></label><br>
			    <label class="form-label">Bet Description: <span class="player1-description"><?= $battle_info['player1_battle_description'];?></span><span class="player2-description d-none"><?= $battle_info['player2_battle_description'];?></span></label><br>
			  </div>
			  <div class="mb-3">
			    <label for="rooting_for_user" class="form-label">Rooting For User:</label>
			    <!-- select code -->
			    <select id="rooting_for_user" name="rooting_for_user" class="form-control" required>
			    	<option value="<?= $battle_info['player1_id']?>" data-player="1"><?= $battle_info['player1_name']; ?></option>
			    	<option value="<?= $battle_info['player2_id']?>" data-player="2"><?= $battle_info['player2_name']; ?></option>
			    </select>
			  </div>
			  <input type="hidden" name="user_id" value="<?= $session->get('user_id'); ?>"/>
			  <button type="submit" class="btn btn-primary">Submit</button>
			</form>
		  </div>

		</main><!-- /.container -->
	</body>
	<script>
		var toastElList = [].slice.call(document.querySelectorAll('.toast'));
		var toastList = toastElList.map(function (toastEl) {
		  return new bootstrap.Toast(toastEl, {delay: 10000})
		});
		if(toastList[0]) toastList[0].show();
		$(document).on('change', '#rooting_for_user', function() {
			let player = $(this).find('option:selected').attr('data-player');
			console.log(player);
			if(player == "1")
			{
				$('.player1-description').removeClass('d-none');
				$('.player2-description').addClass('d-none');
			}
			else
			{
				$('.player1-description').addClass('d-none');
				$('.player2-description').removeClass('d-none');
			}
		});
	</script>
</html>