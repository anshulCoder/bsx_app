<html>
	<head>
		<title>User::Login</title>
	</head>
	<body>
		<?= $header; ?>
		<main class="container">

		  <div class="py-5 px-3">

		  	<?php
		  		$local_session = \Config\Services::session();
		  		if(!empty($local_session->getFlashdata('error')))
		  		{
		  			?>
		  			<div class="alert alert-warning alert-dismissible fade show" role="alert">
					  <?= $local_session->getFlashdata('error');?>
					  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
					</div>
		  			<?php
		  		}
		  	?>
		  	<form action="/user/check_user_login" id="user-login-form" method="post" class="form-horizontal" role="form">
		  	  <div class="mb-3">
			    <label for="username" class="form-label">Username: </label>
			    <input type="text" class="form-control" name="username" id="username" required />
			  </div>
			  <div class="mb-3">
			    <label for="password" class="form-label">Password: </label>
			    <input type="password" class="form-control" name="password" id="password" required />
			  </div>
			  <button type="submit" class="btn btn-primary">Submit</button>
			</form>
		  </div>

		</main><!-- /.container -->
	</body>
</html>