<div class="collapse" id="navbarToggleExternalContent">
  <div class="bg-dark p-4">
    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
	    <li class="nav-item">
	      <a class="nav-link" aria-current="page" href="/admin">Admin</a>
	    </li>
	    <li class="nav-item">
	      <a class="nav-link" href="/user">User</a>
	    </li>
	    <li class="nav-item">
	    	<?php
	    		$session = \Config\Services::session();
	    		if(empty($session->get('user_id')))
	    		{
	    			?>
	    			<a class="nav-link" href="/user/login">Login</a>
	    			<?php
	    		}
	    		else
	    		{
	    			?>
	    			<a class="nav-link" href="user/logout">Logout</a>
	    			<?php
	    		}
	    	?>
	    </li>
	</ul>
  </div>
</div>
<nav class="navbar navbar-dark bg-dark">
  <div class="container-fluid">
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarToggleExternalContent" aria-controls="navbarToggleExternalContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
  </div>
</nav>