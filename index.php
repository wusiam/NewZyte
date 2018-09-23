<?php
	session_start();
	require 'database.php';

	if (isset($_POST['user_name']) && isset($_POST['password'])){
		$pass = password_hash($_POST['password'], PASSWORD_BCRYPT);
		$validation = $sqli->prepare('select * from table users where user_name = ? and password_hash = ?');
		$validation->bind_param('ss', $_POST['user_name'], $pass);
		$flag = false;
		if ($validation->fetch()){
			$_SESSION['user'] = $_POST['user_name']);
		}

		else{
			$flag = true;
		}
	}
?>


<!doctype html>


<html lang = 'en'>
	<head>
		<title>NewsSite</title>

	</head>

	<body>
		<div>

			<?php
				if (isset($_SESSION['user'])){
					echo htmlspecialchars($_SESSION['user']);

					echo "<a herf = \"logout.php\"> Log Out</a>";
				}
				else{
					if ($flag){
						echo "Invalid. Please try again<br>";
					}
					echo "<form action = '"; echo htmlentities($_SERVER['PHP_SELF']); 
					ehco "' method = 'POST'><label>Username:</label><input type = 'text' name = 'user_name'/><br>
					<label>Password</label><input type = 'Password' name = 'password'/><input type = 'submit'/></form>";
					echo "<a> Register new User</a>";
				}
			?>
		</div>

		<div>
			<!-- List all stories -->
			<?php
				$stmt = $sqli->prepare("select title from stories");
				if (!$stmt){
					printf("Query Prep Failed: %s\n", $sqli->error);
					exit;
				}

				$stmt->execute();

				$stmt->bind_result($title);

				while($stmt->fetch()){
					echo "htmlspecialchars($title)<br>";
				}
			?>
		</div>
	</body>
</html>
