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
		<main class="container-fluid">
			<div class="row">
				<div class="col-md-2 bg-light sidebar pe-0 pt-5">
					<div>
			            <ul class="nav flex-column">
			              <li class="nav-item" role="presentation">
			                <a class="nav-link active" data-bs-toggle="tab" href="#pb-battles" role="tab" aria-controls="pb-battles" aria-selected="true">
			                  <i class="fa fa-futbol"></i>
			                  Public Battles
			                </a>
			              </li>
			              <li class="nav-item">
			                <a class="nav-link" data-bs-toggle="tab" href="#dashboard" role="tab" aria-controls="dashboard" aria-selected="true">
			                  <i class="fa fa-home"></i>
			                  Dashboard
			                </a>
			              </li>
			            </ul>
			        </div>
				</div>
				<div class="col-md-10 col-sm-10">
					<div class="tab-content">
						<div class="tab-pane fade show active" id="pb-battles" role="tabpanel" aria-labelledby="pb-battles-tab">
							<div class="text-center py-5 px-3">
							<?php
						  		if(count($open_public_battles)>0)
						  		{
						  			?>
						  			<div class="row row-cols-1 row-cols-md-2 g-4">
						  				<?php
						  					$session = \Config\Services::session();
						  					foreach($open_public_battles as $key => $row)
						  					{
						  						$total_additional = $row['player1_additional'] + $row['player2_additional'];
						  						?>
						  							<div class="col">
						  								<div class="card border-primary">
						  									<div class="card-body">
						  										<h5 class="card-title"><?= $row['media_name']; ?></h5>
						  										<p class="card-text">Bet Amount: <?= $row['battle_amount'] + ($row['battle_amount'] * $total_additional);?></p>
						  										<p>Additional players: <?= $total_additional; ?></p>
						  										<div class="d-flex">
						  											<?php 
						  												if ($total_additional == 0) $total_additional++;
						  											?>
						  											<div class="player1-wrapper w-50" style="text-align: left">
						  												<h3><?= $row['player1_name']; ?></h3>
						  												<p><?= $row['player1_battle_description']; ?></p>
						  												<?php
						  													if(isset($row['bet_exists']) || $row['player1_id'] == $session->get('user_id') || $row['player2_id'] == $session->get('user_id'))
						  													{
						  														?>
						  														<p>With <?= $row['player1_name']; ?> : <?= round(($row['player1_additional']/$total_additional)*100, 2); ?>%</p>
						  														<?php
						  													}
						  												?>
						  											</div>
						  											<div class="player2-wrapper w-50" style="text-align:right">
						  												<h3><?= $row['player2_name']; ?></h3>
						  												<p><?= $row['player2_battle_description']; ?></p>
						  												<?php
						  													if(isset($row['bet_exists']) || $row['player1_id'] == $session->get('user_id') || $row['player2_id'] == $session->get('user_id'))
						  													{
						  														?>
						  														<p>With <?= $row['player2_name']; ?> : <?= round(($row['player2_additional']/$total_additional)*100, 2); ?>%</p>
						  														<?php
						  													}
						  												?>
						  											</div>
						  										</div>
						  									</div>
						  									<div class="card-footer">
						  										<?php
						  											if(!isset($row['bet_exists']) && $row['player1_id'] != $session->get('user_id') && $row['player2_id'] != $session->get('user_id'))
						  											{
						  												?>
						  												<a href="/user/participate-public-battle/<?=$row['battle_id'];?>" class="btn btn-danger" title="Participate in Battle">Participate</a>
						  												<?php
						  											}
						  										?>
						  									</div>
						  								</div>
						  							</div>
						  						<?php
						  					}
						  				?>
						  			</div>
						  			<?php
						  		}
						  		else echo '0 battles found!';
						  	?>
							</div>
						</div>
						<div class="tab-pane fade" id="dashboard" role="tabpanel" aria-labelledby="dashboard-tab">
							<div class="text-center py-5 px-3">
							  	<ul class="nav nav-tabs" id="exchange-tabs" role="tablist">
								  <li class="nav-item" role="presentation">
								    <a class="nav-link active" id="accuracy-tab" data-bs-toggle="tab" href="#accuracy" role="tab" aria-controls="accuracy" aria-selected="true">Bet Accuracy</a>
								  </li>
								  <li class="nav-item" role="presentation">
								    <a class="nav-link" id="battle-tab" data-bs-toggle="tab" href="#battle" role="tab" aria-controls="battle" aria-selected="false">Bet Battle</a>
								  </li>
								  <li class="nav-item" role="presentation">
								    <a class="nav-link" id="sequel-tab" data-bs-toggle="tab" href="#sequel" role="tab" aria-controls="sequel" aria-selected="false">Sequel Bet</a>
								  </li>
								  <li class="nav-item" role="presentation">
								    <a class="nav-link" id="exchange-tab" data-bs-toggle="tab" href="#exchange" role="tab" aria-controls="exchange" aria-selected="false">Bet Exchange</a>
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
									    <a class="nav-link active" id="user-private-battles-tab" data-bs-toggle="pill" href="#private-battles-tab" role="tab" aria-controls="private-battles-tab" aria-selected="true">Private Battles</a>
									  </li>
									  <li class="nav-item" role="presentation">
									    <a class="nav-link" id="user-public-battles-tab" data-bs-toggle="pill" href="#public-battles-tab" role="tab" aria-controls="public-battles-tab" aria-selected="false">Public Battles</a>
									  </li>
									  <li class="nav-item" role="presentation">
									    <a class="nav-link" id="participated-battles-tab" data-bs-toggle="pill" href="#participated-tab" role="tab" aria-controls="participated-battles-tab" aria-selected="false">Participated Battles</a>
									  </li>
									  <li class="nav-item" role="presentation">
									    <a class="nav-link" id="requested-battles-tab" data-bs-toggle="pill" href="#requested-tab" role="tab" aria-controls="requested-tab" aria-selected="false">Requested Battles</a>
									  </li>
									</ul>
									<div class="tab-content" id="pills-tabContent">
									  <div class="tab-pane fade show active" id="private-battles-tab" role="tabpanel" aria-labelledby="user-private-battles-tab">
									  	<table class="table table-responsive table-striped">
								    	<thead>
								    		<tr>
								    			<th>Media Name</th>
								    			<th>Bet amount</th>
								    			<th>Your Prediction</th>
								    			<th>Opponent Prediction</th>
								    			<th>Battle with</th>
								    			<th>Bet End Date</th>
								    			<th>Battle Status</th>
								    			<th>Actions</th>
								    		</tr>
								    	</thead>
								    	<tbody>
								    		<?php
								    			helper('general');
								    			foreach($private_battles as $key => $row) {
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
									  <div class="tab-pane fade" id="public-battles-tab" role="tabpanel" aria-labelledby="user-public-battles-tab">
									  	<table class="table table-responsive table-striped">
								    	<thead>
								    		<tr>
								    			<th>Media Name</th>
								    			<th>Bet amount</th>
								    			<th>Your Prediction</th>
								    			<th>Opponent Prediction</th>
								    			<th>Battle with</th>
								    			<th>Bet End Date</th>
								    			<th>Battle Status</th>
								    			<th>Additional Users</th>
								    			<th>Additional %</th>
								    			<th>Actions</th>
								    		</tr>
								    	</thead>
								    	<tbody>
								    		<?php
								    			helper('general');
								    			foreach($public_battles as $key => $row) {
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
								    					<td><?= $row['battle_end_date']; ?></td>
								    					<td><?= get_battle_status($row['battle_status']); ?></td>
								    					<td><?= (int)$row['player1_additional'] + (int)$row['player2_additional']; ?></td>
								    					<td>
								    						<?php
								    							$total_add = (int)$row['player1_additional'] + (int)$row['player2_additional'];
								    							if($total_add == 0) $total_add++;
								    							echo 'With '.$row['user1_name'].': '. round(((int)$row['player1_additional']/$total_add)*100, 2).'%';
								    							echo '<br>';
								    							echo 'With '.$row['user2_name'].': '. round(((int)$row['player2_additional']/$total_add)*100, 2).'%';
								    						?>
								    					</td>
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
									  <div class="tab-pane fade" id="participated-tab" role="tabpanel" aria-labelledby="participated-battles-tab">
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
								    			foreach($participated_battles as $key => $row) {
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
								  <div class="tab-pane fade" id="sequel" role="tabpanel" aria-labelledby="sequel-tab">
								  	<br>
								  	<a href="/user/new-sequel-bet" type="button" class="btn btn-success">Add new Bet</a>
								    <table class="table table-responsive table-striped">
								    	<thead>
								    		<tr>
								    			<th>Media Name</th>
								    			<th>Bet amount</th>
								    			<th>Predicted day</th>
								    			<th>Predicted month</th>
								    			<th>Predicted year</th>
								    			<th>Predicted Actors</th>
								    			<th>Predicted Actresses</th>
								    			<th>Predicted Directors</th>
								    		</tr>
								    	</thead>
								    	<tbody>
								    		<?php
								    			foreach($sequel_bets as $key => $row) {
								    				?>
								    				<tr>
								    					<td><?= $row['name']; ?></td>
								    					<td><?= $row['sequel_bet_amount']; ?></td>
								    					<td><?= $row['sequel_bet_day']; ?></td>
								    					<td><?= $row['sequel_bet_month']; ?></td>
								    					<td><?= $row['sequel_bet_year']; ?></td>
								    					<td>
								    						<?php
								    							$actors = json_decode($row['sequel_bet_actors'], TRUE);
								    							if(count($actors)>0)
								    							{
								    								echo '<ul>';
								    								foreach($actors as $key)
								    								{
								    									if($key != 'NA') echo '<li>'.$key.'</li>';
								    								}
								    								echo '</ul>';
								    							}

								    						?>
								    					</td>
								    					<td>
								    						<?php
								    							$actresses = json_decode($row['sequel_bet_actresses'], TRUE);
								    							if(count($actresses)>0)
								    							{
								    								echo '<ul>';
								    								foreach($actresses as $key)
								    								{
								    									if($key != 'NA') echo '<li>'.$key.'</li>';
								    								}
								    								echo '</ul>';
								    							}

								    						?>
								    					</td>
								    					<td>
								    						<?php
								    							$directors = json_decode($row['sequel_bet_directors'], TRUE);
								    							if(count($directors)>0)
								    							{
								    								echo '<ul>';
								    								foreach($directors as $key)
								    								{
								    									if($key != 'NA') echo '<li>'.$key.'</li>';
								    								}
								    								echo '</ul>';
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

								  <div class="tab-pane fade" id="exchange" role="tabpanel" aria-labelledby="exchange-tab">
								  	<br>
								  	<a href="/user/new-exchange-bet" type="button" class="btn btn-success">Add new Bet</a>
								    <table class="table table-responsive table-striped">
								    	<thead>
								    		<tr>
								    			<th>Media Name</th>
								    			<th>Bet Slip amount</th>
								    			<th>Bet Slip Type</th>
								    			<th>Exchange Type</th>
								    			<th>Slip selling price</th>
								    			<th>Status</th>
								    			<th>Created Date/Time</th>
								    		</tr>
								    	</thead>
								    	<tbody>
								    		<?php
								    			foreach($exchange_bets as $key => $row) {
								    				?>
								    				<tr>
								    					<td><?= $row['name']; ?></td>
								    					<td><?= $row['slip_bet_amount']; ?></td>
								    					<td><?= $row['slip_type']; ?></td>
								    					<td><?= $row['exchange_type']; ?></td>
								    					<td><?= $row['fixed_selling_price']; ?></td>
								    					<td><?= ($row['exchange_status'] == 0 ? 'Open' : 'Close')?></td>
								    					<td><?= $row['created_datetime']; ?></td>
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
					
				</div>
			</div>

			<!-- <nav class="col-md-2 d-md-block bg-light sidebar">
	          
	        </nav> -->

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
						   <input type="number" min="0" class="form-control" name="media_earning" id="media_earning" required>
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
			if(toastList[0]) toastList[0].show();

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