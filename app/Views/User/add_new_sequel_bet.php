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
			    <div class="input-group input-daterange">
				    <input type="text" class="form-control" name="bet_day_start" id="bet_day_start" required>
				    &nbsp;<div class="input-group-icon">to</div>&nbsp;
				    <input type="text" class="form-control" name="bet_day_end" id="bet_day_end" required>
				</div>
			  </div>
			  <div class="mb-3">
			    <label for="bet_month" class="form-label">Sequel Month:</label>
			    <input type="text" class="form-control" name="bet_month" id="bet_month" required>
			  </div>
			  <div class="mb-3">
			    <label for="bet_year" class="form-label">Sequel Year:</label>
			    <input type="text" class="form-control" name="bet_year" id="bet_year" required>
			  </div>
			  <div class="mb-3 casting-container d-none">

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
				let new_date = new Date($('#media_name option:selected').attr('data-date'));
				$('#bet_day_start, #bet_day_end, #bet_month, #bet_year').val("").datepicker('destroy');
				$('#bet_day_start').datepicker({
					format: 'dd',
					autoclose: true,
					startDate: new_date
				})
				.on('changeDate', function(e) {
					var startDate = new Date(e.date.valueOf());
					var endDate = new Date(e.date.valueOf());
					startDate.setDate(startDate.getDate()+1);
					endDate.setDate(endDate.getDate() + 4);
					$('#bet_day_end').datepicker('setStartDate', startDate);
					$('#bet_day_end').datepicker('setEndDate', endDate);
					$('#bet_day_end').removeAttr('disabled');
				});
				$('#bet_day_end').datepicker({
					format: 'dd',
					autoclose: true
				});
				$('#bet_month').datepicker({
					format: 'mm',
					minViewMode: 'months',
					autoclose: true,
					startDate: new_date
				});
				$('#bet_year').datepicker({
					format: 'yyyy',
					minViewMode: 'years',
					autoclose: true,
					startDate: new_date
				});
				$('#bet_day_start, #bet_month, #bet_year').removeAttr('disabled');
				// ajax for casting info
				$('.loader').removeClass('d-none');
				$.ajax({
					type: 'GET',
					dataType: 'json',
					url: '/user/fetch_casting/'+$('#media_name').val(),
					success: function(data) {
						$('.loader').addClass('d-none');
						if(data.status) 
						{
							let media_data = data.media_data;
							let casting_data = data.casting_data;
							let casting_html = "";
							let actors = JSON.parse(media_data.actors);
							if(actors)
							{
								casting_html += '<legend>Actor(s): </legend><br>';
								for(var i=0;i<actors.length;i++)
								{
									casting_html += '<div class="mb-3"><label for="actor'+i+'">'+(i+1)+'. '+actors[i]+'</label>';
									casting_html += '<select class="form-control cast-select" id="actor'+i+'" name="actors[]">';
									for(var c=0;c<casting_data.actors_list.length;c++)
									{
										var n = casting_data.actors_list[c]['cast_name'];
										casting_html += '<option value="'+n+'">'+n+'</option>';
									}
									casting_html += '</select></div>';
								}
							}
							let actresses = JSON.parse(media_data.actresses);
							if(actresses)
							{
								casting_html += '<legend>Actress(s): </legend><br>';
								for(var i=0;i<actresses.length;i++)
								{
									casting_html += '<div class="mb-3"><label for="actress'+i+'">'+(i+1)+'. '+actresses[i]+'</label>';
									casting_html += '<select class="form-control cast-select" id="actress'+i+'" name="actresses[]">';
									for(var c=0;c<casting_data.actresses_list.length;c++)
									{
										var n = casting_data.actresses_list[c]['cast_name'];
										casting_html += '<option value="'+n+'">'+n+'</option>';
									}
									casting_html += '</select></div>';
								}
							}
							let directors = JSON.parse(media_data.directors);
							if(directors)
							{
								casting_html += '<legend>Director(s): </legend><br>';
								for(var i=0;i<directors.length;i++)
								{
									casting_html += '<div class="mb-3"><label for="director'+i+'">'+(i+1)+'. '+directors[i]+'</label>';
									casting_html += '<select class="form-control cast-select" id="director'+i+'" name="directors[]">';
									for(var c=0;c<casting_data.directors_list.length;c++)
									{
										var n = casting_data.directors_list[c]['cast_name'];
										casting_html += '<option value="'+n+'">'+n+'</option>';
									}
									casting_html += '</select></div>';
								}
							}
							$('.casting-container').html(casting_html).removeClass('d-none');
							$('.cast-select').select2({
								placeholder: 'Select an option'
							});
						}
						else
						{
							alert(data.error);
						}
					},
					error: function(err) {
						$('.loader').addClass('d-none');
						alert("error: ", err);
					}
				});
			}
			else
			{
				$('#bet_day_start, #bet_day_end, #bet_month, #bet_year').attr('disabled', 'disabled').val("");
			}
		}

	</script>
</html>