	
	<nav class="navbar navbar-default navbar-fixed-top">
  <div class="container-fluid">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span> 
      </button>
      <a class="navbar-brand" href="index.php">Admin</a>
    </div>
    <div class="collapse navbar-collapse" id="myNavbar">
      <ul class="nav navbar-nav">
       <li><a href="index.php" >Dashboard</a></li>
			<li><a href="brands.php" >Brands</a></li>
			<li><a href="categories.php" >Categories</a></li>
				<li class="dropdown">
					<a href="" class="dropdown-toggle" data-toggle="dropdown">Products<span class="caret"></span></a>
					<ul class="dropdown-menu" role="menu">
					<li><a href="products.php" >Products</a></li>
					<li><a href="archive.php" >Archived Products</a></li>
					</ul>
				</li>
			<?php if(has_permission('admin')) : ?>
			<li><a href="users.php" >Users</a></li>
			<?php endif; ?>
      </ul>
      <ul class="nav navbar-nav navbar-right">
       				<li class="dropdown">
					<a href="" class="dropdown-toggle" data-toggle="dropdown">Hello <?=$user_data['first'];?>!<span class="caret"></span></a>
					<ul class="dropdown-menu" role="menu">
					<li><a href="change_password.php" >Change Password</a></li>
					<li><a href="logout.php" >Logout</a></li>
					</ul>
				</li>
      </ul>
    </div>
  </div>
</nav>