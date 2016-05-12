<?php
include_once '../framework/identification/token.class.php';
$token = new Token ();
if (! isset ( $_GET ["token"] )) {
	try {
		$token->createToken ( "eric.quinton", 3600 );
		//echo $token->token . "<br>";
		
		/*
		 * Preparation du formulaire de retour du test
		 */
		$contenu = json_decode ( $token->token, true );
		echo '<html><form method="get" action="createToken.php">';
		echo '<input name="token" value="'.$contenu["token"].'"><br>';
		echo '<input type="submit"></form></html>';
	} catch ( Exception $e ) {
		echo $e->getMessage ();
	}
} else {
	/*
	 * Traitement du token pour lire le contenu
	 */
	try {
		$token->openToken ( $_GET ["token"] );
		echo "login : " . $token->login;
	} catch ( Exception $e ) {
		echo $e->getMessage ();
	}
}
?>