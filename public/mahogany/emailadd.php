<?php
	include 'config.php';

	$mysqli = new mysqli(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

	if (mysqli_connect_errno()) {
    	printf("Connect failed: %s\n", mysqli_connect_error());
	exit();
	}


	if (isset($_GET['email'])){
		$email = $_GET['email'];

		$sql = "INSERT INTO wca_newsletter (email) VALUES ('$email')";
		
		$result = $mysqli->query($sql);

		echo $email.' adicionado com sucesso!';
	}
?>

