<?php


	$response = array();

	if (!isset($_POST['name']) and trim($_POST['name']) == '') {
		$response['result'] = 'Champ "nom" vide';
		$response['success'] = 0;
		exit(json_encode($response));
	}
	if (!isset($_POST['email']) and trim($_POST['email']) == '') {
		$response['result'] = 'Champ "email" vide';
		$response['success'] = 0;
		exit(json_encode($response));
	}
	if (!isset($_POST['message']) and trim($_POST['message']) == '') {
		$response['result'] = 'Champ "message" vide';
		$response['success'] = 0;
		exit(json_encode($response));
	}

	// $to = "louisthomas.pro@gmail.com";
	$to = "louisthomas.pro@gmail.com";
	$subject = '[horcholle.fr] - '.$_POST['name'];

	$ip ='';
	// $ip = '<div>Adresse IP : '.$_SERVER["REMOTE_ADDR"].'<br>DLSAM : '.gethostbyaddr($_SERVER["REMOTE_ADDR"]).'</div>';

	$message = '
		<html>
			<head>
				<style>
				</style>
			</head>
			<body>
				<p>From : '.$_POST['name'].'<br>Email : '.$_POST['email'].'
				<p>'.$_POST['message'].'</p>
				<br>
				'.$ip.'
			</body>
		</html>
	';


	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'From: sidiomaralami.com' . "\r\n";

	$success = mail($to, $subject, $message, $headers);

	$response['result'] = '';
	$response['success'] = $success;
	exit(json_encode($response));

?>
