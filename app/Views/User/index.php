<html>
	<head>
		<title> User Bet </title>
	</head>

	<?php
		helper('general');
		$session = \Config\Services::session();
	?>
	<body>
		<?= $header; ?>
		<main class="container">

			<?php
				if (count($requested_battles)>0)
				{
					?>
					<div class="toast-container position-absolute p-3 top-0 start-50 translate-middle-x" id="toastPlacement">
					    <div class="toast">
					      <div class="toast-header">
					        <strong class="me-auto">Notification</strong>
					        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
					      </div>
					      <div class="toast-body">
					        You have <?= count($requested_battles); ?> new bet battle requests
					      </div>
					    </div>
					</div>
					<?php
				}
			?>

		  <div class="text-center py-5 px-3">
		  	<ul class="nav nav-tabs" id="exchange-tabs" role="tablist">
			  <li class="nav-item" role="presentation">
			    <a class="nav-link active" id="accuracy-tab" data-bs-toggle="tab" href="#accuracy" role="tab" aria-controls="accuracy" aria-selected="true">Bet Accuracy</a>
			  </li>
			  <li class="nav-item" role="presentation">
			    <a class="nav-link" id="battle-tab" data-bs-toggle="tab" href="#battle" role="tab" aria-controls="battle" aria-selected="false">Bet Battle</a>
			  </li>
			</ul>
			<div class="tab-content" id="myTabContent">
			  <div class="tab-pane fade show active" id="accuracy" role="tabpanel" aria-labelledby="accuracy-tab">
			  	<br>
			  	<a href="/user/new-accuracy-bet" type="button" class="btn btn-success">Add new Bet</a>
			    <table class="table table-responsive table-striped">
			    	<thead>
			    		<tr>
			    			<th>Media Name</th>
			    			<th>Bet amount</th>
			    			<th>Predicted amount</th>
			    			<th>Bet Date</th>
			    			<th>Actions</th>
			    		</tr>
			    	</thead>
			    	<tbody>
			    		<?php
			    			foreach($bets as $key => $row) {
			    				?>
			    				<tr>
			    					<td><?= $row['name']; ?></td>
			    					<td><?= $row['betting_amount']; ?></td>
			    					<td><?= $row['predicted_amount']; ?></td>
			    					<td><?= $row['betting_date']; ?></td>
			    					<td><a href="#" class="predict_accuracy" title="Predict Accuracy" data-bet-id="<?= $row['bet_id'];?>"><i class="fas fa-percentage"></i></a></td>
			    				</tr>
			    				<?php
			    			}
			    		?>
			    	</tbody>
			    </table>
			  </div>
			  <div class="tab-pane fade" id="battle" role="tabpanel" aria-labelledby="battle-tab">
			  	<br>
			  	<a href="/user/new-battle-bet" type="button" class="btn btn-success mt-2 mb-2">Add new Bet</a>
			  	<ul class="nav nav-pills mb-3" id="bet-battles-tab" role="tablist">
				  <li class="nav-item" role="presentation">
				    <a class="nav-link active" id="user-battles-tab" data-bs-toggle="pill" href="#battles-tab" role="tab" aria-controls="battles-tab" aria-selected="true">Battles</a>
				  </li>
				  <li class="nav-item" role="presentation">
				    <a class="nav-link" id="accepted-battles-tab" data-bs-toggle="pill" href="#accepted-tab" role="tab" aria-controls="accepted-tab" aria-selected="false">Public Battles</a>
				  </li>
				  <li class="nav-item" role="presentation">
				    <a class="nav-link" id="requested-battles-tab" data-bs-toggle="pill" href="#requested-tab" role="tab" aria-controls="requested-tab" aria-selected="false">Requested Battles</a>
				  </li>
				</ul>
				<div class="tab-content" id="pills-tabContent">
				  <div class="tab-pane fade show active" id="battles-tab" role="tabpanel" aria-labelledby="battles-tab">
				  	<table class="table table-responsive table-striped">
			    	<thead>
			    		<tr>
			    			<th>Media Name</th>
			    			<th>Bet amount</th>
			    			<th>Your Prediction</th>
			    			<th>Opponent Prediction</th>
			    			<th>Battle with</th>
			    			<th>Public/Private</th>
			    			<th>Bet End Date</th>
			    			<th>Battle Status</th>
			    			<th>Actions</th>
			    		</tr>
			    	</thead>
			    	<tbody>
			    		<?php
			    			helper('general');
			    			foreach($battles as $key => $row) {
			    				?>
			    				<tr>
			    					<td><?= $row['name']; ?></td>
			    					<td><?= $row['battle_amount']; ?></td>
			    					<?php
			    						if($row['user1_id'] == $session->get('user_id'))
			    						{
			    							?>
			    								<td><?= $row['player1_battle_description'];?></td>
			    								<td><?= $row['player2_battle_description'];?></td>
			    								<td><?= $row['user2_name'];?></td>
			    							<?php
			    						}
			    						else
			    						{
			    							?>
			    								<td><?= $row['player2_battle_description'];?></td>
			    								<td><?= $row['player1_battle_description'];?></td>
			    								<td><?= $row['user1_name'];?></td>
			    							<?php
			    						}
			    					?>
			    					<td><?= $row['battle_mode']; ?></td>
			    					<td><?= $row['battle_end_date']; ?></td>
			    					<td><?= get_battle_status($row['battle_status']); ?></td>
			    					<td>
			    						<?php
			    							if($row['battle_status'] == BATTLE_LIVE)
			    							{
			    								?>
			    								<a href="#" class="btn btn-primary claim-battle" data-battle-id="<?= $row['battle_id'];?>">Claim Battle</a>
			    								<?php
			    							}
			    						?>
			    					</td>
			    				</tr>
			    				<?php
			    			}
			    		?>
			    	</tbody>
			    	</table>
				  </div>
				  <div class="tab-pane fade" id="accepted-tab" role="tabpanel" aria-labelledby="accepted-tab">
				  	<table class="table table-responsive table-striped">
			    	<thead>
			    		<tr>
			    			<th>Media Name</th>
			    			<th>Bet amount</th>
			    			<th>Rooting with</th>
			    			<th>Battle Prediction</th>
			    			<th>Bet End Date</th>
			    			<th>Battle Status</th>
			    			<th>Registered Date/Time</th>
			    		</tr>
			    	</thead>
			    	<tbody>
			    		<?php
			    			foreach($public_battles as $key => $row) {
			    				?>
			    				<tr>
			    					<td><?= $row['name']; ?></td>
			    					<td><?= $row['bet_amount']; ?></td>
			    					<td><?= $row['username']; ?></td>
			    					<td><?= $row['battle_description']; ?></td>
			    					<td><?= $row['battle_end_date']; ?></td>
			    					<td><?= get_battle_status($row['battle_status']); ?></td>
			    					<td>
			    						<?php
			    							$d = date_create($row['created_datetime']);
			    							echo date_format($d, DATE_TIME_FORMAT_UI);
			    						?>
			    					</td>
			    				</tr>
			    				<?php
			    			}
			    		?>
			    	</tbody>
			    	</table>
				  </div>
				  <div class="tab-pane fade" id="requested-tab" role="tabpanel" aria-labelledby="requested-battles-tab">
				  	<table class="table table-responsive table-striped">
			    	<thead>
			    		<tr>
			    			<th>Media Name</th>
			    			<th>Bet amount</th>
			    			<th>Bet description</th>
			    			<th>Bet requested by</th>
			    			<th>Public/Private</th>
			    			<th>Bet End Date</th>
			    			<th>Actions</th>
			    		</tr>
			    	</thead>
			    	<tbody>
			    		<?php
			    			foreach($requested_battles as $key => $row) {
			    				?>
			    				<tr>
			    					<td><?= $row['name']; ?></td>
			    					<td><?= $row['battle_amount']; ?></td>
			    					<td>
			    						<?php
			    							if($row['battle_status'] == BATTLE_PENDING_PLAYER2)
			    							{
			    								echo $row['player1_battle_description'];
			    							}
			    							else
			    							{
			    								echo $row['player2_battle_description'];
			    							}
			    						?>
			    					</td>
			    					<td><?= $row['battle_with']; ?></td>
			    					<td><?= $row['battle_mode']; ?></td>
			    					<td><?= $row['battle_end_date']; ?></td>
			    					<td><?php
			    						if($row['battle_status'] == BATTLE_PENDING_PLAYER2)
			    						{
			    							?>
			    							<a href="#" data-battle-id="<?= $row['battle_id'];?>" class="btn btn-success ask-description" title="Accept"><i class="fas fa-check"></i></a>
			    							<?php
			    						}
			    						else
			    						{
			    							?>
			    							<a href="/user/accept-battle/<?= $row['battle_id'];?>" class="btn btn-success" title="Accept"><i class="fas fa-check"></i></a>
			    							<?php
			    						}
			    					?>&nbsp;&nbsp;
			    					<a href="/user/deny-battle/<?= $row['battle_id'];?>" class="btn btn-danger" title="Decline"><i class="fas fa-times"></i></a></td>
			    				</tr>
			    				<?php
			    			}
			    		?>
			    	</tbody>
			    </table>
				  </div>
				</div>
			  </div>
			</div>
		  </div>

		</main><!-- /.container -->

		<!-- Modal -->
		<div class="modal fade" id="predictionModal" tabindex="-1" aria-labelledby="predictionModalLabel" aria-hidden="true">
		  <div class="modal-dialog">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h5 class="modal-title" id="predictionModalLabel">Predict Bet Accuracy</h5>
		        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
		      </div>
		      <div class="modal-body">
		      	<div class="amount_form">
		      		<form action="/user/calc_prediction_accuracy" id="calcPredictionForm" method="post" class="form-horizontal" role="form">
		      			<div class="mb-3">
						   <label for="media_earning" class="form-label">Enter amount movie/tv earned:</label>
						   <input type="number" class="form-control" name="media_earning" id="media_earning" required>
						</div>
						<input type="hidden" name="bet_id" value=""/>
						<button type="submit" class="btn btn-danger">Submit</button>
		      		</form>
		      	</div>
		       	<div class="accuracy_section">
		       		<h2></h2>
		       	</div>
		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
		      </div>
		    </div>
		  </div>
		</div>

		<div class="modal fade" id="descriptionModal" tabindex="-1" aria-labelledby="descriptionModalLabel" aria-hidden="true">
		  <div class="modal-dialog">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h5 class="modal-title">Your Battle Prediction</h5>
		        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
		      </div>
		      <div class="modal-body">
		      	<form action="/user/player2_battle_approval" id="battle-approval-form" method="post" class="form-horizontal" role="form">
	      			<div class="mb-3">
					   <label for="battle_description" class="form-label">Enter Descrption:</label>
					   <textarea rows="10" cols="5" name="battle_description" class="form-control" id="battle_description" required></textarea>
					</div>
					<input type="hidden" name="bet_battle_id" value=""/>
					<button type="submit" class="btn btn-danger">Submit</button>
	      		</form>
		      </div>
		    </div>
		  </div>
		</div>

		<script>
			$(document).on('click', '.predict_accuracy', function() {
				let betId = $(this).attr('data-bet-id');
				$('.amount_form input[name=bet_id]').val(betId);
				$('#predictionModal').modal('show');
			});

			$(document).on('submit', '#calcPredictionForm', function(e) {
				e.preventDefault();
				$(this).find('button[type=submit]').attr('disabled', 'disabled');

				$.ajax({
					type: 'POST',
					dataType:'json',
		            url:$(this).attr('action'),
		            data:$(this).serialize(),
		            success: function(data){
		            	$('#calcPredictionForm button[type=submit]').removeAttr('disabled');
		            	if(data.status)
		            	{
		            		$('.amount_form').addClass('d-none');
		            		$('.accuracy_section h2').text('Accuracy: '+ data.accuracy);
		            		$('.accuracy_section').removeClass('d-none');
		            	}
		            	else
		            	{
		            		alert('Some error occured: '+ data.error);
		            	}
		            },
		            error: function(){
		            	$('#calcPredictionForm button[type=submit]').removeAttr('disabled');
		            	alert('Some error occured');
		            }
				});
			});

			var myModalEl = document.getElementById('predictionModal');
			myModalEl.addEventListener('hidden.bs.modal', function (event) {
				$('.amount_form').removeClass('d-none').find('#media_earning').val("");
				$('.accuracy_section').addClass('d-none').find('h2').text("");
			});

			var toastElList = [].slice.call(document.querySelectorAll('.toast'));
			var toastList = toastElList.map(function (toastEl) {
			  return new bootstrap.Toast(toastEl, {delay: 10000})
			});
			toastList[0].show();

			$(document).on('click', '.ask-description', function(e) {
				let battle_id = $(this).attr('data-battle-id');
				if(battle_id)
				{
					$('#descriptionModal input[name="bet_battle_id"]').val(battle_id);
					$('#descriptionModal').modal('show');
				}
			});
		</script>
		<!-- script for claiming battle -->
		<script>
			$(document).on('click', '.claim-battle', function(e) {
				let claimBtn = $(this);
				let ask_user = confirm('Claim battle?');
				if(ask_user)
				{
					claimBtn.attr('disabled', 'disabled').addClass('disabled');
					let battle_id = claimBtn.attr('data-battle-id');
					console.log(battle_id);
					$.ajax({
						type:"POST",
						dataType: 'json',
						url: "/user/claim_battle",
						data: {battle_id: battle_id},
						success: function(data)
						{
							claimBtn.removeAttr('disabled').removeClass('disabled');
							if(data.status)
							{
								alert("Claim successful!");
								window.location.reload();
							}
							else
							{
								alert(data.error);
							}
						},
						error: function(err)
						{
							claimBtn.removeAttr('disabled').removeClass('disabled');
							alert("Some error occured!");
						}
					});
				}
			});
		</script>
	</body>
</html>