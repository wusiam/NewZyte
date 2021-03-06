<?php
	session_start();
	require 'database.php';
	require 'login.php';

	//prepare query to get story
	$id = isset($_GET['id'])? $_GET['id'] : null;
	$usr = isset($_SESSION['user'])? $_SESSION['user'] : null;
	$stmt = $sqli->prepare("select title, stories, owner, time, hidd, id from stories where id = ?");
	$stmt->bind_param('s', $id);
	if(!$stmt){
		printf("Query Prep Failed: %s\n", $sqli->error);
		exit;
	}
	$stmt->execute();
	$stmt->bind_result($title, $story, $owner, $time, $hidd, $storyid);
	$link = 'view.php';
	$outlink = 'logout.php';
	if (isset($_GET['id'])){
		$link .= '?id='.$id;
		$outlink .= '?id='.$id;
	}

	// if story is hidden, redirect to index.php
	if(!($hidd == 0)){
		header("Location: index.php");
		exit;
	}

?>

<!doctype html>

<html lang = 'en'>
	<head>
		<title>View Story</title>
		<link href = 'stylesheet.css' rel = "stylesheet" type = 'text/css'/>
	</head>
	<body>
		<div class = 'top'>
			<div class = 'title'>
				NewZyte
			</div>

			<div class = 'rightside'>
				<?php
					if (isset($_SESSION['user'])){
						echo "Welcome, ";
						echo htmlspecialchars($_SESSION['user']);

						echo "&nbsp <a href = '$outlink' class = 'button'> Log Out</a>&nbsp";
						echo "<a href = 'insertstory.php' class = 'button'>Add Story</a>&nbsp<a href = 'index.php' class = 'button'>Home</a>&nbsp<a href ='dashboard.php' class = 'button'>Dashboard</a>";

					}
					else{
						if ($flag){
							echo "<span class = 'wrong'>Invalid. Please try again</span> &nbsp";
						}
						echo "<form action = '$link' method = 'POST'><label class = 'label'>Username:   </label><input type = 'text' name = 'user_name' class = 'input'/>&nbsp
						<label class = 'label'>Password:   </label><input type = 'Password' name = 'password' class = 'input'/><input type = 'submit' class = 'submitbutton' value = 'Login'/></form>";
						echo "<a href = 'register.php' class = 'button'> Register new User</a>&nbsp<a href = 'index.php' class = 'button'>Home</a>";
					}
				?>
			</div>
		</div>
		<div class = 'middle2'>
		<?php
			if ($stmt->fetch()){
				// .largetext and .smalltext needs css
				echo "<span class = 'largetext'>"; echo htmlspecialchars($title);
				echo "</span> <br><span class = 'smalltext'>Written by ";
				echo htmlspecialchars($owner);
				echo ". Posted: $time </span>";
				if (isset($_SESSION['user']) && $owner === $_SESSION['user']){
					echo "<br><a href = 'updatestory.php?id=$storyid'>Edit</a>&nbsp<a href = 'deletestory.php?id=$storyid'>Delete</a>";
				}

				echo "<br><br><p>$story</p>";
				$stmt->close();
				$comments = $sqli->prepare("select owner, comment, id, time from comments where story = ? order by time asc");
				$comments->bind_param('s', $id);
				$comments->execute();
				$comments->bind_result($user, $comment, $commid, $time);

				echo "</div><div class = 'middle'>";
				while($comments->fetch()){
					// .comment needs css
					echo "<div class = 'comment'>";
					echo htmlspecialchars($user); echo " writes:<br><span class = 'commtext'>";
					echo htmlspecialchars($comment); echo"</span>";
					echo "<br><span class = 'smalltext'> Posted: $time. </span>";
					if (isset($_SESSION['user']) && $user === $_SESSION['user']) {
						echo "&nbsp <a href = 'updatecomment.php?id=$commid'>Edit</a>&nbsp<a href = 'deletecomment.php?id=$commid'>Delete</a>";
					}
					echo "</div>";
				}
				echo "</div><div class = 'middle'>";

				echo "<a href = 'insertcomment.php?id="; echo $id;
				echo "' class = 'button'>Add Comment</a>";
				echo "</div>";

			}
			else{
				echo "<h1> Invalid. Story not found</h1>";
			}
		?>
		</div>
	</body>
</html>
