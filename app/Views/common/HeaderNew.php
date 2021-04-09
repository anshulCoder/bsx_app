<?php $session = \Config\Services::session(); ?>
<nav class="navbar navbar-expand-md navbar-dark bg-success">
    <div class="container-fluid">
      <a class="navbar-brand" href="#" style="font-weight: 900; font-size: xx-large;">THE MOVIE PLAY</a>
      <div class="collapse navbar-collapse justify-content-end" id="navbarNavAltMarkup">
        <div class="navbar-nav main-menu" style="display:none; font-size: 20px;">
          <a class="linx nav-link active" aria-current="page" href="#">Home</a>
          <a class="linx nav-link" href="../Synopsis/index.html">Movies</a>
          <a class="linx nav-link" href="#">Bet Battle</a>
          <a class="linx nav-link" href="../Exchange Bet/index.html">Bet Exchange</a>
          <a class="linx nav-link" href="../Bet Arena/index.html">Bet Arena</a>
          <a class="linx nav-link" href="#">About Us</a>
        </div>
        <div class="navbar-nav">
          <a id="mainLink" style="margin: auto; font-size: 20px;" class="nav-link active" aria-current="page" href="#">Home</a>
          <?php
          	if(!empty($session->get('user_id')))
          	{
          		?>
          			<li class="nav-item dropdown" style="align-self: center">
	          			<a class="nav-link dropdown-toggle" style="font-size: 20px;" href="#" id="userPanel" role="button" data-bs-toggle="dropdown" aria-expanded="false">
			            <i class="bi bi-person"></i> <?= ucfirst($session->get('user_name')) ?>
			          	</a>
			        	<ul class="dropdown-menu" aria-labelledby="userPanel">
				            <li><a class="dropdown-item">W: <?= $session->get('user_wallet_balance');?> <br> P: <?= $session->get('user_total_points'); ?></a></li>
				            <li><a class="dropdown-item" href="../User Page/Self/index.html">Profile</a></li>
				            <li><a class="dropdown-item" href="/login/logout">Logout</a></li>
			        	</ul>
			        </li>
          		<?php
          	}
          	else
          	{
          		?>
          		<a style="font-size: 20px;margin: auto;" class="nav-link" href="/login">Login</a>
          		<?php
          	}
          ?>
          <a id="collapseLinks" class="nav-link justify-self-right" href="#">
            <span><img style="width: 40px;" src="/assets/icons/menu_dots.jpg"></span>
          </a>
        </div>
      </div>
    </div>
</nav>
<div class="loader d-none">
	<div class="spinner-border loader-icon" role="status">
	  <span class="visually-hidden">Loading...</span>
	</div>
</div>

<script>
	$(document).on('click', '#collapseLinks', function() {
		$('#mainLink').toggle();
		$('.main-menu').toggle();
	});
</script>