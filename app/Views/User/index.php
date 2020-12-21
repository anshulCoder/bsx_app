<html>
	<head>
		<title> User Bet </title>
	</head>

	<body>
		<?= $header; ?>
		<main class="container">

		  <div class="text-center py-5 px-3">
		  	<a href="user/add" type="button" class="btn btn-success">Add new Bet</a>
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
		</script>
	</body>
</html>