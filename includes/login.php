<?php
require_once 'classes/authorization.class.php';

$user  		= $_POST['username'];
$password	= $_POST['password'];

$login 		= new Authorization();
$login->set_username($user);
$login->set_password($password);

$check = $login->checklogin();

if ($check==1) {
    $login->ip();
	$login->savelogin();
	echo '<meta http-equiv=refresh content="0; URL=index.php">';
	
}else {
   
    
?>
			<html>
				<head>
					<meta charset="utf-8">
					<title>ავტორიზაცია</title>
					<link rel="stylesheet" type="text/css" href="media/css/login/style.css" />
				</head>
				<body>
					<div class="container">
						<section id="content">
							<form action="" method="post">
								<h1>ავტორიზაცია</h1>
								<div>
									<input name="username" type="text" placeholder="მომხმარებელი" required="" id="username" autocomplete="off"/>
								</div>
								<div>
									<input name="password" type="password" placeholder="პაროლი" required="" id="password" autocomplete="off"/>
								</div>
								<div>
									<input type="submit" value="შესვლა" />
								</div>
							</form>
						</section>
					</div>
				</body>
			</html>
<?php 
}
