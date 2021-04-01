<html>
	<head>
		<title> User Panel </title>
		<link href="/assets/css/bootstrap-datepicker.min.css" rel="stylesheet">
	</head>

	<?php
		helper('general');
		$session = \Config\Services::session();
	?>
	
	<body>
		<?= $header; ?>
		<main class="container-fluid">
			<?php
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
			                <a class="nav-link" data-bs-toggle="tab" href="#exchange-slips" role="tab" aria-controls="exchange-slips" aria-selected="true">
			                  <i class="fa fa-home"></i>
			                  Bets For Exchange
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
						  										<p><?= $row['battle_description']; ?></p>
						  										<div class="d-flex">
						  											<?php 
						  												if ($total_additional == 0) $total_additional++;
						  											?>
						  											<div class="player1-wrapper w-50" style="text-align: left">
						  												<h3><?= $row['player1_name']; ?></h3>
						  												<p><?= ($row['player_for'] == 1 ? 'With Prediction' : 'Against Prediction') ?></p>
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
						  												<p><?= ($row['player_for'] == 2 ? 'With Prediction' : 'Against Prediction') ?></p>
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
						<div class="tab-pane fade" id="exchange-slips" role="tabpanel" aria-labelledby="exchange-slips-tab">
							<?php
								if(isset($slips_for_exchange) && count($slips_for_exchange)>0)
								{
									?>
									<div class="row row-cols-1 row-cols-md-2 g-4 py-5 px-5">
										<?php
										foreach($slips_for_exchange['exchanges'] as $key => $row)
										{
											$slip_id_column = 'bet_id';
						    				if($row['slip_type'] == 'Bet Sequel') {
						    					$slip_id_column = 'sequel_bet_id';
						    				} elseif ($row['slip_type'] == 'Bet Battle') {
						    					$slip_id_column = 'battle_id';
						    				}
						    				$slips = $slips_for_exchange['slips'];
						    				array_walk($slips, 'search_array', $slip_id_column);
						    				$bet_detail_index = array_search($row['slip_id'], $slips);
						    				if($bet_detail_index !== false)
						    				{
						    					$bet_detail = $slips_for_exchange['slips'][$bet_detail_index];
						    					$bet_amount = !empty($row['fixed_selling_price']) ? $row['fixed_selling_price'] : $bet_detail['sequel_bet_amount'];
						    					?>
							    				<div class="col">
					  								<div class="card border-secondary">
					  									<div class="card-body">
					  										<h5 class="card-title"><?= $bet_detail['name']; ?></h5>
					  										<p class="card-text"><?= (!empty($row['fixed_selling_price']) ? '<strong>Selling Price: </strong>'.$row['fixed_selling_price'] : '<strong>For Auction</strong>') ?></p>
					  										<?php
					  											if($slip_id_column == 'bet_id')
					  											{
					  												?>
					  												<p><strong>Stake: </strong><?= $bet_detail['accuracy_betting_amount'] ?></p>
					  												<p><strong>End Date: </strong><?= $bet_detail['accuracy_betting_date'] ?></p> 
					  												<?php
					  											} elseif($slip_id_column == 'sequel_bet_id') {
					  												?>
					  												<p><strong>Stake: </strong><?= $bet_detail['sequel_bet_amount'] ?></p>
					  												<?php
					  													$sequel_day = !empty($bet_detail['sequel_bet_day']) ? $bet_detail['sequel_bet_day'].'-' : '';
					  													$sequel_month = !empty($bet_detail['sequel_bet_month']) ? $bet_detail['sequel_bet_month'].'-' : '';
					  													$actors = json_decode($bet_detail['sequel_bet_actors'], TRUE);
					  													$actresses = json_decode($bet_detail['sequel_bet_actresses'], TRUE);
					  													$directors = json_decode($bet_detail['sequel_bet_directors'], TRUE);
					  												?>
					  												<p><strong>Sequel Date: </strong><?= $sequel_day.$sequel_month.$bet_detail['sequel_bet_year'] ?> </p>
					  												<?php
					  													if(isset($actors) && count($actors)>0) {
					  														?>
					  														<p><strong>Actor: </strong><?= $actors[0]; ?></p>
					  														<?php
					  													}
					  													if(isset($actresses) && count($actresses)>0) {
					  														?>
					  														<p><strong>Actress: </strong><?= $actresses[0]; ?></p>
					  														<?php
					  													}
					  													if(isset($directors) && count($directors)>0) {
					  														?>
					  														<p><strong>Director: </strong><?= $directors[0]; ?></p>
					  														<?php
					  													}
					  													if(isset($bet_detail['max_bid_amount'])) {
					  														?>
					  														<p><strong>Max Bid: </strong><?= $bet_detail['max_bid_amount'] ?></p>
					  														<?php
					  													}
					  												?>
					  												<?php
					  											} elseif($slip_id_column == 'battle_id') {
					  												?>
					  												<p><strong>Battle prediction: </strong><?= $bet_detail['battle_description']; ?></p>
					  												<p><strong>With prediction: </strong><?= ($bet_detail['player_for'] == 1 ? $bet_detail['player1_name'] : $bet_detail['player2_name']); ?></p>
					  												<p><strong>Against Prediction: </strong><?= ($bet_detail['player_against'] == 1 ? $bet_detail['player1_name'] : $bet_detail['player2_name']); ?></p>
					  												<p><strong>Stake: </strong><?= $bet_detail['battle_amount'] ?></p>
					  												<p><strong>End Date: </strong><?= $bet_detail['battle_end_date'] ?></p>
					  												<p><strong>Battle status: </strong><?= $bet_detail['battle_mode'] ?></p>
					  												<?php
					  											}
					  										?>
					  									</div>
				  										<?php
				  											if($row['user_id'] != $session->get('user_id'))
				  											{
				  												?>
				  												<div class="card-footer">
				  												<?php
				  												if(isset($row['fixed_selling_price'])) {
				  													$offer_allowed = true;
				  													foreach($existing_offers as $offerKey => $offerRow) {
				  														if($offerRow['bet_exchange_id'] == $row['exchange_id'] )  {
				  															$offer_allowed = false;
				  															break;
				  														}
				  													}
				  													if($offer_allowed)
				  													{
				  														?>
				  														<button type="button" class="btn btn-success offer-btn" data-exchange-id="<?= $row['exchange_id'];?>" data-exchange-amount="<?= $bet_amount;?>">Make Offer</button>&nbsp;
				  														<?php
				  													}
					  												?>
					  												<button type="button" class="btn btn-danger buy-now" data-exchange-id="<?= $row['exchange_id']; ?>">Buy Now</button>
					  												<?php
					  											} else {
					  												?>
					  												<button type="button" class="btn btn-success make-bid-btn" data-exchange-id="<?= $row['exchange_id'];?>" data-auction-amount="<?= $bet_amount; ?>" >Place Bid</button>
					  												<?php
					  											}
					  											?>
					  											</div>
					  											<?php
				  											}
				  										?>
					  								</div>
					  							</div>
							    				<?php
						    				}
										}
										?>
									</div>
									<?php
								}
							?>
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
								    					<td>
								    						<?php
								    							if($row['bet_status'] == 0)
								    							{
								    								?>
								    								<a href="#" class="predict_accuracy" title="Predict Accuracy" data-bet-id="<?= $row['bet_id'];?>"><i class="fas fa-percentage"></i></a>
								    								<?php
								    							}
								    							elseif($row['bet_status'] == 1) {
								    								echo 'Won';
								    							}
								    							elseif($row['bet_status'] == 2) {
								    								echo 'Lost';
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
								    			<th>Battle Description</th>
								    			<th>With/Against Prediction</th>
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
								    					<td><?= $row['battle_description']; ?></td>
								    					<?php
								    						if($row['user1_id'] == $session->get('user_id'))
								    						{
								    							?>
								    								<td><?= ($row['player_for'] == 1 ? 'With' : 'Against');?></td>
								    								<td><?= $row['user2_name'];?></td>
								    							<?php
								    						}
								    						else
								    						{
								    							?>
								    								<td><?= ($row['player_for'] == 2 ? 'With' : 'Against');?></td>
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
								    			<th>Battle Description</th>
								    			<th>With/Against Prediction</th>
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
								    					<td><?= $row['battle_description'];?></td>
								    					<?php
								    						if($row['user1_id'] == $session->get('user_id'))
								    						{
								    							?>
								    								<td><?= ($row['player_for'] == 1 ? 'With' : 'Against');?></td>
								    								<td><?= $row['user2_name'];?></td>
								    							<?php
								    						}
								    						else
								    						{
								    							?>
								    								<td><?= ($row['player_for'] == 2 ? 'With' : 'Against');?></td>
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
								    					<td><?= $row['battle_description']; ?></td>
								    					<td><?= $row['battle_with']; ?></td>
								    					<td><?= $row['battle_mode']; ?></td>
								    					<td><?= $row['battle_end_date']; ?></td>
								    					<td><?php
								    						?>
							    							<a href="/user/accept-battle/<?= $row['battle_id'];?>" class="btn btn-success" title="Accept"><i class="fas fa-check"></i></a>
							    							<?php
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
								    			<th>Actions</th>
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
								    					<td>
								    						<?php
								    							if($row['bet_status'] == 0)
								    							{
								    								?>
								    								<a href="#" class="close_sequel_bet_btn" title="Close Bet" data-bet-id="<?= $row['sequel_bet_id'];?>"><i class="fa fa-times-circle"></i></a>
								    								<?php
								    							}
								    							elseif($row['bet_status'] == 1) {
								    								echo 'Won';
								    							}
								    							elseif($row['bet_status'] == 2) {
								    								echo 'Lost';
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
								  	<br>
								  	<ul class="nav nav-pills mb-3" id="bet-battles-tab" role="tablist">
								  	  <li class="nav-item" role="presentation">
									    <a class="nav-link active" id="user-exchange-slips-tab" data-bs-toggle="pill" href="#exchange-slips-tab" role="tab" aria-controls="exchange-slips-tab" aria-selected="true">Exchange Slips</a>
									  </li>
									  <li class="nav-item" role="presentation">
									    <a class="nav-link" id="user-offers-sent-tab" data-bs-toggle="pill" href="#offers-sent-tab" role="tab" aria-controls="offers-sent-tab" aria-selected="false">Offers Sent</a>
									  </li>
									  <li class="nav-item" role="presentation">
									    <a class="nav-link" id="user-bids-sent-tab" data-bs-toggle="pill" href="#bids-sent-tab" role="tab" aria-controls="bids-sent-tab" aria-selected="false">Bids Sent</a>
									  </li>
									</ul>
									<div class="tab-content" id="pills-tabContent">
									  <div class="tab-pane fade show active" id="exchange-slips-tab" role="tabpanel" aria-labelledby="user-exchange-slips-tab">
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
									    			<th>Offers</th>
									    		</tr>
									    	</thead>
									    	<tbody>
									    		<?php
									    			if(isset($exchange_bets['exchanges'])) {
									    				foreach($exchange_bets['exchanges'] as $key => $row) {
										    				$slip_id_column = 'bet_id';
										    				if($row['slip_type'] == 'Bet Sequel') {
										    					$slip_id_column = 'sequel_bet_id';
										    				} elseif ($row['slip_type'] == 'Bet Battle') {
										    					$slip_id_column = 'battle_id';
										    				}
										    				$bet_detail_index = array_search($row['slip_id'], array_column($exchange_bets['slips'], $slip_id_column));
										    				if($bet_detail_index !== false)
										    				{
										    					$bet_detail = $exchange_bets['slips'][$bet_detail_index];
										    					?>
										    					<tr>
											    					<td><?= $bet_detail['name']; ?></td>
											    					<td><?php 
											    						if($slip_id_column == 'bet_id') echo $bet_detail['accuracy_betting_amount'];
											    						elseif ($slip_id_column == 'sequel_bet_id') {
											    							echo $bet_detail['sequel_bet_amount'];
											    						} else echo $bet_detail['battle_amount'];
											    					?></td>
											    					<td><?= $row['slip_type']; ?></td>
											    					<td><?= ucfirst($row['exchange_type']); ?></td>
											    					<td><?= $row['fixed_selling_price'] ?: '-'; ?></td>
											    					<td><?= ($row['exchange_status'] == 0 ? 'Open' : 'Close')?></td>
											    					<td><?= $row['created_datetime']; ?></td>
											    					<td>
											    						<?php
											    							if(isset($row['fixed_selling_price']))
											    							{
											    								?>
											    								<a href="#" class="view-offers" data-exchange-id="<?= $row['exchange_id']; ?>">View Offers</a>
											    								<?php
											    							}
											    							elseif($row['exchange_type'] == 'auction') {
											    								?>
											    								<a href="#" class="view-bids" data-exchange-id="<?= $row['exchange_id']; ?>">View Bids</a>
											    								<?php
											    							}
											    						?>
											    					</td>
											    				</tr>
										    					<?php
										    				}
										    			}
									    			}
									    		?>
									    	</tbody>
									    </table>
									  </div>
									  <div class="tab-pane fade" id="offers-sent-tab" role="tabpanel" aria-labelledby="user-offers-sent-tab">
									  	<table class="table table-responsive table-striped">
									    	<thead>
									    		<tr>
									    			<th>Bet Slip Type</th>
									    			<th>Exchange Type</th>
									    			<th>Slip selling price</th>
									    			<th>Requested Price</th>
									    			<th>Status</th>
									    			<th>Offer sent Date/Time</th>
									    		</tr>
									    	</thead>
									    	<tbody>
									    		<?php
									    			if(isset($offers_sent) && count($offers_sent)>0)
									    			{
									    				foreach($offers_sent as $oKey => $oRow)
									    				{
									    					?>
									    					<tr>
									    						<td><?= $oRow['slip_type']; ?></td>
									    						<td><?= ucfirst($oRow['exchange_type']); ?></td>
									    						<td><?= $oRow['fixed_selling_price']; ?></td>
									    						<td><?= $oRow['requested_price']; ?></td>
									    						<td>
									    							<?php
									    								$status = 'Awaiting Acceptance';
									    								if($oRow['status'] == 1)
									    								{
									    									$status = 'Accepted';
 									    								}
 									    								elseif($oRow['status'] == 2)
 									    								{
 									    									$status = 'Rejected';
 									    								}
 									    								echo $status;
									    							?>
									    						</td>
									    						<td><?php $d = date_create($oRow['created_datetime']); echo date_format($d, DATE_TIME_FORMAT_UI) ?></td>
									    					<?php
									    				}
									    			}
									    		?>
									    	</tbody>
									    </table>
									  </div>
									  <div class="tab-pane fade" id="bids-sent-tab" role="tabpanel" aria-labelledby="user-bids-sent-tab">
									  	<table class="table table-responsive table-striped">
									    	<thead>
									    		<tr>
									    			<th>Bet Slip Type</th>
									    			<th>Bidding Amount</th>
									    			<th>Bid sent Date/Time</th>
									    		</tr>
									    	</thead>
									    	<tbody>
									    		<?php
									    			if(isset($bids_sent) && count($bids_sent)>0)
									    			{
									    				foreach($bids_sent as $oKey => $oRow)
									    				{
									    					?>
									    					<tr>
									    						<td><?= $oRow['slip_type']; ?></td>
									    						<td><?= $oRow['bid_amount']; ?></td>
									    						<td><?php $d = date_create($oRow['created_datetime']); echo date_format($d, DATE_TIME_FORMAT_UI) ?></td>
									    					<?php
									    				}
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

		<div class="modal fade" id="offerModal" tabindex="-1" aria-labelledby="offerModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
				    	<h5 class="modal-title">Make Offer</h5>
				        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				    </div>
				    <div class="modal-body">
				      	<form action="/user/send_bet_offer" id="bet-offer-form" method="post" class="form-horizontal" role="form">
			      			<div class="mb-3">
							   <label for="offer-amount" class="form-label">Enter Amount:</label>
							   <input type="number" min="1" name="offer_amount" id="offer-amount" class="form-control" required="">
							</div>
							<input type="hidden" name="exchange_id" id="exchange-id" value=""/>
							<button type="submit" class="btn btn-danger">Submit</button>
			      		</form>
				    </div>
				</div>
			</div>
		</div>

		<div class="modal fade" id="auctionModal" tabindex="-1" aria-labelledby="auctionModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
				    	<h5 class="modal-title">Place Bid</h5>
				        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				    </div>
				    <div class="modal-body">
				      	<form action="/user/send_auction_bid" id="bet-auction-form" method="post" class="form-horizontal" role="form">
			      			<div class="mb-3">
							   <label for="bid-amount" class="form-label">Enter Amount:</label>
							   <input type="number" name="bid_amount" id="bid-amount" class="form-control" required="">
							</div>
							<input type="hidden" name="exchange_id" id="exchange-id" value=""/>
							<button type="submit" class="btn btn-danger">Submit</button>
			      		</form>
				    </div>
				</div>
			</div>
		</div>

		<div class="modal fade" id="offers-show-modal" tabindex="-1" aria-labelledby="offers-show-modal-label" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title"></h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body">
					</div>
				</div>
			</div>
		</div>

		<div class="modal fade" id="closeSequelBetModal" tabindex="-1" aria-labelledby="closeSequelBetModalLabel" aria-hidden="true">
		  <div class="modal-dialog">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h5 class="modal-title" id="closeSequelBetModalLabel">Close Sequel Bet</h5>
		        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
		      </div>
		      <div class="modal-body">
		      	<div class="amount_form">
		      		<form action="/user/settle_sequel_bet" id="closeSequelBetForm" method="post" class="form-horizontal" role="form">
		      			<div class="mb-3">
						   <label for="movie_release_date" class="form-label">Movie Release Date: </label>
						   <input type="text" class="form-control" name="movie_release_date" id="movie_release_date" required>
						</div>
						<div class="mb-3">
							<label for="lead_actor" class="form-label">Lead Actor: </label>
							<input type="text" class="form-control" name="lead_actor" id="lead_actor" required>
						</div>
						<div class="mb-3">
							<label for="lead_actress" class="form-label">Lead Actress: </label>
							<input type="text" class="form-control" name="lead_actress" id="lead_actress" required>
						</div>
						<div class="mb-3">
							<label for="lead_director" class="form-label">Lead Director: </label>
							<input type="text" class="form-control" name="lead_director" id="lead_director" required>
						</div>
						<input type="hidden" name="sequel_bet_id" value=""/>
						<button type="submit" class="btn btn-danger">Submit</button>
		      		</form>
		      	</div>
		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
		      </div>
		    </div>
		  </div>
		</div>
	<script src="/assets/js/bootstrap-datepicker.min.js"></script>
		<script>
			$(document).on('click', '.predict_accuracy', function() {
				let betId = $(this).attr('data-bet-id');
				$('.amount_form input[name=bet_id]').val(betId);
				$('#predictionModal').modal('show');
			});

			$(document).on('submit', '#calcPredictionForm', function(e) {
				e.preventDefault();
				$(this).find('button[type=submit]').attr('disabled', 'disabled');
				var bet_id = $(this).find('input[name=bet_id]').val();
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
		            		$('.accuracy_section').append('<button type="button" class="btn btn-danger close-accuracy-bet" data-bet-id="'+bet_id+'" data-percentage="'+data.accuracy+'">Close Bet</button>');
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
				$('.accuracy_section').addClass('d-none').html('<h2></h2>');
			});

			var toastElList = [].slice.call(document.querySelectorAll('.toast'));
			var toastList = toastElList.map(function (toastEl) {
			  return new bootstrap.Toast(toastEl, {delay: 10000})
			});
			if(toastList[0]) toastList[0].show();
			if(toastList[1]) toastList[1].show();

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

			$(document).on('click', '.offer-btn', function(e) {
				var exchange_id = $(this).attr('data-exchange-id');
				var exchange_amt = Number($(this).attr('data-exchange-amount'));
				$('#offerModal #bet-offer-form #offer-amount').attr('max', exchange_amt);
				$('#offerModal #bet-offer-form #exchange-id').val(exchange_id);
				$('#offerModal').modal('show');
			});

			$(document).on('click', '.make-bid-btn', function() {
				var exchange_id = $(this).attr('data-exchange-id');
				var auction_amt = Number($(this).attr('data-auction-amount'));
				var percent_amt = Math.round((auction_amt*10)/100);
				$('#auctionModal #bet-auction-form #bid-amount').attr('min',(auction_amt - percent_amt)).attr('max', (auction_amt + percent_amt));
				$('#auctionModal #bet-auction-form #exchange-id').val(exchange_id);
				$('#auctionModal').modal('show');
			});

			$(document).on('click', '.buy-now', function() {
				var exchange_id = $(this).attr('data-exchange-id');
				var t = $(this);
				var conf = confirm("Purchase Bet Slip?");
				if(conf)
				{
					$(this).attr('disabled', 'disabled');
					$.ajax({
						type: 'POST',
						dataType: 'json',
						url: "/user/buy_bet_slip",
						data: {exchange_id: exchange_id},
						success: function(d) {
							if(d.status)
							{
								window.location.reload();
							}
							else
							{
								alert(d.error);
								t.removeAttr('disabled');
							}
						},
						error: function(err)
						{
							alert("some error occured");
							t.removeAttr('disabled');
						}
					});
				}
			});

			$(document).on('click', '.view-offers', function(e) {
				e.preventDefault();
				var t = this;
				$(this).attr('disabled', 'disabled');
				var exchange_id = $(this).attr('data-exchange-id');
				$.ajax({
					type: "GET",
					dataType: 'json',
					url: '/user/get_offers/'+exchange_id,
					success: function(data) {
						$(t).removeAttr("disabled");
						if(data.status) {
							var offersHtml = '<table class="table table-striped table-responsive table-bordered">';
							offersHtml += '<thead><tr><th>User name</th><th>Price Offered</th><th>Actions</th></tr></thead><tbody>';
							for(var i=0;i<data.offers.length;i++)
							{
								offersHtml += '<tr>';
								offersHtml += '<td>'+data.offers[i]['name']+'</td>';
								offersHtml += '<td>'+data.offers[i]['requested_price']+'</td>';
								if(data.offers[i]['status'] == '0')
								{
									offersHtml += '<td><a href="#" data-request-id="'+data.offers[i]['buyer_request_id']+'" class="approve-offer">Accept</a>&nbsp; <a href="/user/reject_offer/'+data.offers[i]['buyer_request_id']+'" class="reject-offer">Reject</a>';
								}
								else if(data.offers[i]['status'] == '1')
								{
									offersHtml += '<td>Accepted</td>';
								}
								else
								{
									offersHtml += '<td>Rejected</td>';
								}
								offersHtml += '</tr>';
							}
							$('#offers-show-modal .modal-body').html(offersHtml);
							$('#offers-show-modal').modal('show');
						}
						else
						{
							alert(data.error);
						}
					},
					error: function(err) {
						$(t).removeAttr("disabled");
						alert("Some Error Occured!");
					}
				});
			});

			$(document).on('click', '.approve-offer', function(e) {
				e.preventDefault();
				var t = $(this);
				var request_id = $(this).attr('data-request-id');
				$(this).attr('disabled', 'disabled');

				$.ajax({
					type: 'GET',
					dataType: 'json',
					url: "/user/approve_offer/"+request_id,
					success: function(data) {
						if(data.status) {
							window.location.reload();
						}
						else {
							alert(data.error);
						}
					},
					error: function(err) {
						t.removeAttr('disabled');
						alert("Some error occured!");
					}
				});
			});

			$(document).on('click', '.view-bids', function(e) {
				e.preventDefault();
				var t = $(this);
				$(t).attr('disabled', 'disabled');
				var exchange_id = $(this).attr('data-exchange-id');
				$.ajax({
					type: "GET",
					dataType: 'json',
					url: '/user/get_bids/'+exchange_id,
					success: function(data) {
						$(t).removeAttr("disabled");
						if(data.status) {
							var offersHtml = '<table class="table table-striped table-responsive table-bordered">';
							offersHtml += '<thead><tr><th>User name</th><th>Bid Amount</th><th>Bidding Date/Time</th></tr></thead><tbody>';
							for(var i=0;i<data.bids.length;i++)
							{
								offersHtml += '<tr>';
								offersHtml += '<td>'+data.bids[i]['name']+'</td>';
								offersHtml += '<td>'+data.bids[i]['bid_amount']+'</td>';
								offersHtml += '<td>'+data.bids[i]['created_datetime']+'</td>';
								offersHtml += '</tr>';
							}
							$('#offers-show-modal .modal-body').html(offersHtml);
							$('#offers-show-modal').modal('show');
						}
						else
						{
							alert(data.error);
						}
					},
					error: function(err) {
						$(t).removeAttr("disabled");
						alert("Some Error Occured!");
					}
				});
			});

			$(document).on('click', '.close-accuracy-bet', function(e) {
				var bet_id = $(this).attr('data-bet-id');
				var percentage = $(this).attr('data-percentage');
				$(this).prop('disabled', true);
				var t = $(this);
				$.ajax({
					type: "POST",
					dataType: 'json',
					url: '/user/settle_bet_accuracy',
					data: {bet_id: bet_id, accuracy: percentage},
					success: function(data) {
						t.prop("disabled", false);
						if(data.status) 
						{
							window.location.reload();
						}
						else
						{
							alert(data.error);
						}
					},
					error: function(err) {
						t.prop("disabled", false);
						alert("Some Error Occured!");
					}
				});
			});

			$(document).on('click', '.close_sequel_bet_btn', function() {
				var sequel_bet_id = $(this).attr('data-bet-id');
				$('#closeSequelBetModal #closeSequelBetForm input[name=sequel_bet_id]').val(sequel_bet_id);
				$('#closeSequelBetModal #closeSequelBetForm input[name=movie_release_date]').datepicker({
					format: 'yyyy-mm-dd'
				});
				$('#closeSequelBetModal').modal('show');
			});

			$(document).on('submit',"#closeSequelBetForm", function(e) {
				e.preventDefault();

				var t = $(this);
				t.find("button[type=submit]").prop('disabled', true);

				$.ajax({
					type: 'POST',
					dataType: 'json',
					url: t.attr('action'),
					data: t.serialize(),
					success: function(data) {
						t.prop("disabled", false);
						if(data.status)
						{
							alert(data.msg);
							window.location.reload();
						}
						else
						{
							alert(data.error);
						}
					},
					error: function(err) {
						t.prop("disabled", false);
						alert("Some Error Occured!");
					}
				});
			});
		</script>
	</body>
</html>