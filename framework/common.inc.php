<?php
/** Fichier cree le 4 mai 07 par quinton
 * Modifie le 9/8/11 : mise en session de la classe smarty
 *
 *UTF-8
 *
 * inclusions de base, ouverture de session
 *
 */

/**
 * Lecture des parametres de l'application
 */
include_once ("param/param.default.inc.php");
include_once ("param/param.inc.php");

/**
 * Protection contre les IFRAMES
 */
header ( "X-Frame-Options: SAMEORIGIN" );

/*
 * protection de la session
 */
ini_set ( "session.use_strict_mode", true );
ini_set ( 'session.gc_probability', 1 );
ini_set ( 'session.gc_maxlifetime', $APPLI_session_ttl );

/**
 * Integration de SMARTY
 */
include_once ('plugins/smarty-3.1.24/libs/Smarty.class.php');

/**
 * integration de la classe ObjetBDD et des scripts associes
 */
include_once ('plugins/objetBDD-3.0/ObjetBDD.php');
include_once ('plugins/objetBDD-3.0/ObjetBDD_functions.php');
if ($APPLI_utf8 == true)
	$ObjetBDDParam ["UTF8"] = true;
$ObjetBDDParam ["codageHtml"] = false;
/**
 * Integration de la classe gerant la navigation dans les modules
 */
include_once ("framework/navigation/navigation.class.php");

/**
 * Preparation de l'identification
 */
include_once ("framework/identification/identification.class.php");
if ($ident_type == "CAS")
	include_once ($CAS_plugin);

/**
 * Initialisation des parametres generaux
 */
ini_set ( "register_globals", false );
ini_set ( "magic_quotes_gpc", true );
error_reporting ( $ERROR_level );
ini_set ( "display_errors", $ERROR_display );
/*
 * Appel des initialisations specifiques de l'application
 */
include_once "modules/beforesession.inc.php";
/**
 * Demarrage de la session
 */
@session_start ();
/*
 * Verification du cookie de session, et destruction le cas echeant
 */
if (isset ( $_SESSION ['LAST_ACTIVITY'] ) && (time () - $_SESSION ['LAST_ACTIVITY'] > $APPLI_session_ttl)) {
	// last request was more than 30 minutes ago
	session_unset (); // unset $_SESSION variable for the run-time
	session_destroy (); // destroy session data in storage
}
$_SESSION ['LAST_ACTIVITY'] = time (); // update last activity time stamp
if (! isset ( $_SESSION ['CREATED'] )) {
	$_SESSION ['CREATED'] = time ();
} else if (time () - $_SESSION ['CREATED'] > $APPLI_session_ttl) {
	/*
	 * La session a demarre depuis plus du temps de la session : cookie regenere
	 */
	session_regenerate_id ( true ); // change session ID for the current session and invalidate old session ID
	$_SESSION ['CREATED'] = time (); // update creation time
}
/*
 * Regeneration du cookie de session
 */
$cookieParam = session_get_cookie_params ();
$cookieParam ["lifetime"] = $APPLI_session_ttl;
if ($APPLI_modeDeveloppement == false)
	$cookieParam ["secure"] = true;
$cookieParam ["httponly"] = true;
setcookie ( session_name (), session_id (), time () + $APPLI_session_ttl, $cookieParam ["path"], $cookieParam ["domain"], $cookieParam ["secure"], $cookieParam ["httponly"] );

/*
 * Lancement de l'identification
 */

$identification = new Identification ();

$identification->setidenttype ( $ident_type );
if ($ident_type == "CAS") {
	$identification->init_CAS ( $CAS_address, $CAS_port, $CAS_uri );
} elseif ($ident_type == "LDAP" || $ident_type == "LDAP-BDD") {
	$identification->init_LDAP ( $LDAP_address, $LDAP_port, $LDAP_basedn, $LDAP_user_attrib, $LDAP_v3, $LDAP_tls );
}
/*
 * Chargement des fonction generiques
 */
include_once 'framework/fonctions.php';
/*
 * Gestion de la langue a afficher
 */
if (isset ( $_SESSION ["LANG"] ) && $APPLI_modeDeveloppement == false) {
	$LANG = $_SESSION ["LANG"];
} else {
	/*
	 * Recuperation le cas echeant du cookie
	 */
	if (isset ( $_COOKIE ["langue"] )) {
		$langue = $_COOKIE ["langue"];
	} else {
		/*
		 * Recuperation de la langue du navigateur
		 */
		$langue = explode ( ';', $_SERVER ['HTTP_ACCEPT_LANGUAGE'] );
		$langue = substr ( $langue [0], 0, 2 );
	}
	/*
	 * Mise a niveau du langage
	 */
	setlanguage ( $langue );
}
/**
 * Verification du couple session/adresse IP
 */
if (isset ( $_SESSION ["remoteIP"] )) {
	if ($_SESSION ["remoteIP"] != $_SERVER ['REMOTE_ADDR']) {
		// Tentative d'usurpation de session - on ferme la session
		if ($identification->disconnect ( $APPLI_address ) == 1) {
			$message = $LANG ["message"] [7];
		} else {
			$message = $LANG ["message"] [8];
		}
	}
} else {
	$_SESSION ["remoteIP"] = $_SERVER ['REMOTE_ADDR'];
}
/*
 * Connexion a la base de donnees
 */
if (! isset ( $bdd )) {
	$etaconn = true;
	if ($APPLI_modeDeveloppement == true) {
		try {
			$bdd = new PDO ( $BDDDEV_dsn, $BDDDEV_login, $BDDDEV_passwd );
		} catch ( PDOException $e ) {
			print $e->getMessage () . "<br>";
			$etaconn = false;
		}
	} else {
		try {
			$bdd = new PDO ( $BDD_dsn, $BDD_login, $BDD_passwd );
		} catch ( PDOException $e ) {
			$etaconn = false;
		}
	}
	if ($etaconn == true) {
		/*
		 * Mise en place du schema par defaut
		 */
		$APPLI_modeDeveloppement == true ? $schema = $BDDDEV_schema : $schema = $BDD_schema;
		if (strlen ( $schema ) > 0) {
			$bdd->exec ( "set search_path = " . $schema );
		}
		/*
		 * Connexion a la base de gestion des droits
		 */
		try {
			$bdd_gacl = new PDO ( $GACL_dsn, $GACL_dblogin, $GACL_dbpasswd );
		} catch ( PDOException $e ) {
			print $e->getMessage () . "<br>";
			$etaconn = false;
		}
		if ($etaconn == true) {
			/*
			 * Mise en place du schema par defaut
			 */
			if (strlen ( $GACL_schema ) > 0)
				$bdd_gacl->exec ( "set search_path = " . $GACL_schema );
		} else {
			echo ($LANG ["message"] [29]);
		}
	} else
		echo $LANG ["message"] [22];
}
/*
 * Activation de SMARTY
 */
$smarty = new Smarty ();
$smarty->template_dir = $SMARTY_template;
$smarty->compile_dir = $SMARTY_template_c;
$smarty->config_dir = $SMARTY_config;
$smarty->cache_dir = $SMARTY_cache_dir;
$smarty->caching = $SMARTY_cache;
if (! isset ( $message ))
	$message = "";
	/*
 * Assignation des variables "standard"
 */
$smarty->assign ( "melappli", $APPLI_mail );
$smarty->assign ( "fds", $path_inc . $APPLI_fds );
$smarty->assign ( "entete", $SMARTY_entete );
$smarty->assign ( "enpied", $SMARTY_enpied );
$smarty->assign ( "corps", $SMARTY_corps );
$smarty->assign ( "LANG", $LANG );
$smarty->assign ( "ident_type", $ident_type );

/*
 * Prepositionnement de idFocus, qui permet de positionner le focus automatiquement a l'ouverture d'une page web
 */
$smarty->assign ( "idFocus", "" );
/*
 * Preparation du module de gestion de la navigation
 */
if (isset ( $_SESSION ["navigation"] ) && $APPLI_modeDeveloppement == false) {
	$navigation = $_SESSION ['navigation'];
} else {
	$navigation = new Navigation ( $navigationxml );
	$_SESSION ['navigation'] = $navigation;
}
/*
 * Activation de la classe d'enregistrement des traces
 */
$log = new Log ( $bdd_gacl, $ObjetBDDParam );
/*
 * Preparation de la gestion des droits
 */
if (isset ( $_SESSION ["droits"] ) /*&& $APPLI_modeDeveloppement == false*/) {
	$smarty->assign ( "droits", $_SESSION ["droits"] );
} else {
	include "framework/identification/setDroits.php";
}

/*
 * Chargement des fonctions specifiques
 */
include_once 'modules/fonctions.php';

include_once 'framework/functionsDebug.php';
/*
 * Preparation du menu
 */
if (! isset ( $_SESSION ["menu"] ) || $APPLI_modeDeveloppement == true) {
	include_once 'framework/navigation/menu.class.php';
	$menu = new Menu ( $APPLI_menufile, $LANG );
	$_SESSION ["menu"] = $menu->generateMenu ();
}
/*
 * Chargement des traitements communs specifiques a l'application
 */
include_once ("modules/common.inc.php");
?>