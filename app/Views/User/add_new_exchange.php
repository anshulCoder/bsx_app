<html>
	<head>
		<title>Exchange bet slip</title>
	</head>
	<body class="exchange-page">
		
		<?= $header; ?>

		<main class="container">
		  <?php $validation = \Config\Services::validation();?>
		  <?= $validation->listErrors() ?>
		  <div class="py-5 px-3">
		  	<form action="/user/save_exchange_bet" id="save-exchange-form" method="post" class="form-horizontal" role="form">
		  	  <div class="mb-3">
		  	  	<ul class="list-inline my-mainMenuList">
                    <?php
                        foreach($bet_slips as $key => $row)
                        {
                        	$bet_type = "Bet Accuracy";
                        	$bet_type_id = 'accuracy';
                        	$slip_id = $row['bet_id'];
                        	if(isset($row['battle_id'])) {
                        		$bet_type = "Bet Battle";
                        		$slip_id = $row['battle_id'];
                        		$bet_type_id = 'battle';
                        	} elseif(isset($row['sequel_bet_id'])) {
                        		$bet_type = 'Bet Sequel';
                        		$slip_id = $row['sequel_bet_id'];
                        		$bet_type_id = 'sequel';
                        	}

                            ?>
                            <li class="list-inline-item">
                                <input type="radio" name="slip_id" onchange="showExchangeTypes('<?= $bet_type; ?>', this)" id="<?= $bet_type_id.'_'.$slip_id;?>" value="<?= $slip_id;?>" 
                                data-amount="<?= ($row['accuracy_betting_amount'] ?: $row['battle_amount']) ?>" />
                                <label for="<?= $bet_type_id.'_'.$slip_id;?>">
                                    <h2><?= $bet_type ?></h2>
                                    <span><?= $row['name']; ?></span>
                                    <br>
                                    <p class="text-muted"><strong>Description: </strong><?= $row['description'] ?></p>
                                    <span>Release Date: <?= $row['release_date'];?></span><br>
                                    <?php
                                    	if($bet_type == 'Bet Accuracy')
                                    	{
                                    		?>
                                    		<span>Stake: <?= $row['accuracy_betting_amount'] ?></span>
                                    		<?php
                                    	}
                                    	elseif($bet_type == 'Bet Sequel')
                                    	{
                                    		$actors = json_decode($row['sequel_bet_actors'], TRUE);
                                    		$actresses = json_decode($row['sequel_bet_actresses'], TRUE);
                                    		$directors = json_decode($row['sequel_bet_directors'], TRUE);
                                    		?>
                                    		<span>Stake: <?= $row['sequel_bet_amount'] ?></span>
                                    		<br>
                                    		<span>Sequel Bet Date: <?= (!empty($row['sequel_bet_day']) ? $row['sequel_bet_day'].'-' : '').(!empty($row['sequel_bet_month']) ? $row['sequel_bet_month'].'-' : '').$row['sequel_bet_year'] ?></span>
                                    		<br>
                                    			<?php
                                    				if(!empty($actors[0]))
                                    				{
                                    					?>
                                    					<span>Lead Actor: <?= $actors[0]; ?></span><br>
                                    					<?php
                                    				}
                                    				if(!empty($actresses[0]))
                                    				{
                                    					?>
                                    					<span>Lead Actress: <?= $actresses[0]; ?></span><br>
                                    					<?php
                                    				}
                                    				if(!empty($directors[0]))
                                    				{
                                    					?>
                                    					<span>Lead Director: <?= $directors[0]; ?></span>
                                    					<?php
                                    				}
                                    			?>
                                    		<?php
                                    	}
                                    	elseif($bet_type == 'Bet Battle')
                                    	{
                                    		?>
                                    		<span>Your Prediction: <?= $row['battle_description'] ?></span>
                                    		<br>
                                    		<span>Opp Prediction: <?= $row['battle_opponent_description'] ?></span>
                                    		<br>
                                    		<span>Battle With: <?= $row['battle_opponent'] ?></span>
                                    		<br>
                                    		<span>Battle Date: <?= $row['battle_end_date'] ?></span>
                                    		<br>
                                    		<span>Stake: <?= $row['battle_amount'] ?></span>
                                    		<br>
                                    		<span>Bet Status: <?= $row['battle_mode'] ?></span>
                                    		<?php
                                    	}
                                    ?>
                                </label>
                            </li>
                            <?php
                        }
                    ?>
                </ul>
                <div class="exchange-opts">
                </div>
			  <?php $session = \Config\Services::session(); ?>
			  <input type="hidden" name="user_id" value="<?= $session->get('user_id'); ?>"/>
			  <button type="submit" class="btn btn-primary">Submit</button>
			</form>
		  </div>

		</main><!-- /.container -->
	</body>
	<script>
		function showExchangeTypes($bet_type, event)
		{
			var type_html = '';
			if($bet_type == 'Bet Accuracy' || $bet_type == 'Bet Battle')
			{
				var amount = Number($(event).attr('data-amount'));
				var capping = Math.round((amount*25)/100);
				$('#save-exchange-form').append('<input type="hidden" name="exchange_type" value="fixed">')
				.append('<input type="hidden" name="slip_type" value="'+$bet_type+'"/>');
				$('.exchange-opts').html('<div class="mb-3"><label for="fixed_selling_price">Fixed Selling Price: </label><input type="number" class="form-control" name="fixed_selling_price" id="fixed_selling_price" min="1" max="'+(amount-capping)+'" required></div>').removeClass('d-none');
			}
			else
			{
				$('#save-exchange-form').append('<input type="hidden" name="exchange_type" value="auction">')
				.append('<input type="hidden" name="slip_type" value="'+$bet_type+'"/>');
				$('.exchange-opts').addClass('d-none');
			}

		}
	</script>
</html>