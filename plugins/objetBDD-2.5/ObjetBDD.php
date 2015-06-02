<?php
/**
 * ObjetBDD - instanciation objet d'une table de base de donnees
 *
 * Classe modele des classes orientees BDD
 *
 * For questions, help, comments, discussion, etc., please join the
 * ObjetBDD mailing list. http://sourceforge.net/mail/?group_id=152347
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 *
 * @author Eric Quinton, Franck Huby
 * @copyright (C) Eric Quinton 2006-2015
 * @version 2.4.2.1 du 30/03/2015
 * @package ObjetBDD
 *
 * Utilisation :
 *  class Test inherits ObjetBDD {
 * //constructeur
 * function __construct ($bdd,$param=NULL) {
 *       $this->table="maTable";
 * // Definition des formats des colonnes, et des controles a leur appliquer
 *               $this->colonnes = array(
 *               "id"=>array("type"=>1,"requis"=>1,"key"=>1,"defaultValue"=>0),
 *               "datemodif"=>array("types"=>2, "defaultValue"=>"getDateJour"),
 *               "field1"=>array("longueur"=>10,"pattern"=>"#^[a-zA-Z]+$#","requis"=>1),
 *               "field2"=>array("longueur"=>5),
 *               "mel"=>array("pattern"=>"#^.+@.+\.[a-zA-Z]{2,6}$#")
 *               );
 * // Si toutes les colonnes de la table sont decrites :
 *              $this->fullDescription = 1;
 * // Appel du constructeur de la classe ObjetBDD
 *
 *               parent::__construct($link,$param);
 *       }
 */
class ObjetBDD {
	/**
	 * Attributs
	 */
	
	/**
	 * @public $connection
	 * instance adodb pass by reference
	 */
	public $connection;
	/**
	 * @public $table : nom de la table
	 */
	public $table;
	/**
	 * @public $champs : liste des champs de la table
	 */
	public $champs;
	/**
	 * @public $cle : nom du champ utilise comme cle primaire de la table
	 */
	public $cle;
	/**
	 * @public $keys : tableau des cles, si cles multiples
	 */
	public $keys;
	public $cleMultiple;
	/**
	 * @public $id_auto : booleen definissant le type d'id de la table (0=non auto, 1=auto, auto gere par valeur max())
	 */
	public $id_auto;
	/**
	 * @public $types
	 * Collection "Types" Stocke la structure des champs de la table (0 pour
	 * non numerique, 1 pour numerique, 2 pour date, 3 pour un datetime, 4 pour un champ geographique postgis)
	 * =>type"
	 */
	public $types;
	/**
	 * @public $longueurs
	 * Collection "Longueurs" stocke la longueur maximale des colonnes de type texte
	 * Utilise lors de la verification des donnees entrees
	 * Ne doit etre renseigne que pour les champs dont on veut verifier la longueur
	 */
	public $longueurs;
	/**
	 * @public $auto_date
	 * int definissant la gestion automatique de la date
	 * 0 : pas de gestion de la date
	 * 1 : gestion de la date "classique"
	 * (0|1)
	 */
	public $auto_date;
	/**
	 * publiciables de conversion des dates
	 */
	/**
	 * @public $separateurDB
	 * char separateur du SGBD
	 */
	public $separateurDB; // char separateur du serveur de donnees
	/**
	 * @public $sepValide
	 * array of char
	 * separateurs utilisables en local (en saisie)
	 */
	public $sepValide;
	/**
	 * @public $separateurLocal
	 * char
	 * Separateur local par defaut
	 */
	public $separateurLocal;
	/**
	 * @public $formatDate
	 * int Format de date en affichage
	 * 0 : amj
	 * 1 : jma
	 * 2 : mja
	 */
	public $formatDate;
	/**
	 * @public $dateMini
	 * int annee minimale sur 2 chiffres
	 * Exemple :
	 * 29 pour 1929,
	 * les annees sur 2 chiffres seront traduites entre
	 * 1929 et 2030.
	 */
	public $dateMini;
	/**
	 * @public $debug_mode;
	 * 0 : pas de mode debug
	 * 1 : mode debug sur les erreurs
	 * 2 : mode debug permanent
	 */
	public $debug_mode;
	/**
	 * @public $verifData
	 * 0 : pas de vérification des donnees avant les operations d'ecriture
	 * 1 : activation de la verification des donnees (par defaut)
	 */
	public $verifData;
	/**
	 * @public $errorData ;
	 * Contient la liste des erreurs lors de la verification des donnees
	 * $errorData[]["code"] :
	 * code d'erreur :
	 * 1 : champ non numerique
	 * 2 : champ texte trop grand
	 * 3 : masque (pattern) non conforme
	 * 4 : champ obligatoire vide
	 * $errorData[]["colonne"] : champ concerne
	 * $errorData[]["valeur"] : valeur initiale
	 */
	public $errorData;
	/**
	 * @public $codageHtml
	 * Indique si les informations recuperees en base de donnees ou injectees doivent etre
	 * passees par les instructions htmlspecialchars et htmlspecialchars_decode
	 * Par defaut, a true
	 */
	public $codageHtml;
	/**
	 * @public $pattern ;
	 * Tableau contenant le pattern a verifier pour chaque champ
	 * exemple :
	 * $pattern = array ( "Nom"=>"#^[a-zA-Z]+$#",
	 * "mail"=>"#^.+@.+\.[a-zA-Z]{2,6}$#");
	 */
	public $pattern;
	/**
	 * @public $champs_nonvides
	 * Tableau contenant la liste des champs obligatoires
	 * Exemple :
	 * $champs_nonvides = array("Nom", "Prenom");
	 */
	public $champs_nonvides;
	/**
	 * @public $colonnes
	 * Tableau contenant la liste des colonnes avec leurs caractéristiques
	 * C'est la concatenation en un seul tableau des tableaux $types, $longueurs, $pattern,
	 * $champs_nonvides, $defaultValue
	 * Exemple : $colonnes = array ( "Id"=> array ("type"=>1,"requis"=>1, "defaultValue"=>0),
	 * "Nom" => array("longueur"=>20, "pattern"=>"#^[a-zA-Z]+$#", "requis"=>1),
	 * "attributPere" => array("type"=>1, "parentAttrib"=>1, "requis"=>1);
	 */
	public $colonnes;
	/**
	 *
	 *
	 * Variable permettant d'indiquer si la table
	 * est totalement decrite dans la classe
	 * $fullDescription = 1 : tous les champs sont decrits
	 * Defaut : 0 (compatibilite ascendante)
	 *
	 * @var int
	 */
	public $fullDescription;
	/**
	 *
	 * @var array Tableau contenant les parametres passes lors de la construction de la classe
	 *      Utilise si la classe a besoin d'instancier une autre classe utilisant ObjetBDD
	 */
	public $param;
	/**
	 * tableau utilise pour stocker la valeur de $param avant d'y injecter les donnees specifiques
	 * a la table.
	 * Utilise lors de l'instanciation d'autres classes a l'interieur d'une classe
	 *
	 * @var array
	 */
	public $paramori;
	/**
	 * Type de base de donnees
	 *
	 * @var string
	 */
	private $typeDatabase;
	/**
	 * Indique si la base de donnees est codee en UTF8 ou non
	 *
	 * @var boolean
	 */
	public $UTF8;
	/**
	 * Indique s'il faut ou non transformer les valeurs en UTF8
	 * (application codée en UTF8, base de données en autre codage)
	 * @var boolean
	 */
	public $toUTF8 = false;
	/**
	 * Tableau contenant les valeurs par defaut
	 *
	 * @var Array
	 */
	public $defaultValue;
	
	/**
	 * Nom de l'attribut utilise dans une relation pere-fils
	 * (attribut pointant vers le pere)
	 *
	 * @var string
	 */
	public $parentAttrib;
	/**
	 * Valeur du SRID pour les imports de donnees geographiques postgis
	 * Vaut -1 si non fourni
	 *
	 * @var integer
	 */
	public $srid;
	/**
	 * Caractere utilise pour entourer les noms des colonnes
	 *
	 * @var string
	 */
	public $quoteIdentifier;
	/**
	 * Transforme les virgules en points, pour les champs numeriques
	 *
	 * @var integer
	 */
	public $transformComma;
	/**
	 * methodes
	 */
	/**
	 * ObjetBDD
	 * Fonction d'initialisation de la classe
	 * Modifier les parametres generaux de la classe si necessaire
	 * Dans la classe heritee, renseigner systematiquement les valeurs suivantes :
	 * $table : nom de la table en base de donnees
	 * $types : id de tableau : nom de la colonne, valeur : type de champ. A ne renseigner que pour les
	 * champs numerique (1), date(2), ou datetime(3)
	 *
	 * @param
	 *        	instance ADODB
	 */
	function __construct(&$p_connection, $param = NULL) {
		$this->connection = $p_connection;
		$this->param = $param;
		/**
		 * valeurs par defaut / Defaults values *
		 */
		$this->auto_date = 1; // verification automatique des dates par defaut
		$this->separateurDB = "-";
		$this->formatDate = 1;
		$this->sepValide = array (
				"/",
				"-",
				".",
				" " 
		);
		$this->separateurLocal = "/";
		$this->dateMini = 49;
		$this->id_auto = 1;
		$this->verifData = 1;
		$this->debug_mode = 1;
		$this->codageHtml = true;
		$this->cleMultiple = 0;
		$this->fullDescription = 0;
		$this->typeDatabase = substr ( strtolower ( $this->connection->databaseType ), 0, 7 );
		$this->connection->SetFetchMode ( ADODB_FETCH_ASSOC );
		$this->UTF8 = false;
		$this->srid = - 1;
		$this->transformComma = 1;
		/*
		 * Preparation des tableaux intermediaires a partir du tableau $colonnes
		 */
		if (is_array ( $this->colonnes )) {
			$this->types = array ();
			$this->pattern = array ();
			$this->longueurs = array ();
			$this->champs_nonvides = array ();
			$this->keys = array ();
			$this->defaultVal = array ();
			$nbcle = 0;
			foreach ( $this->colonnes as $key => $value ) {
				// Traitement de chaque variable
				foreach ( $value as $key1 => $value1 ) {
					switch ($key1) {
						case "type" :
							$this->types [$key] = $value1;
							break;
						case "longueur" :
							$this->longueurs [$key] = $value1;
							break;
						case "pattern" :
							$this->pattern [$key] = $value1;
							break;
						case "requis" :
							if ($value1 == 1)
								$this->champs_nonvides [] = $key;
							break;
						case "defaultValue" :
							$this->defaultValue [$key] = $value1;
							break;
						case "parentAttrib" :
							$this->parentAttrib = $key;
							break;
						case "key" :
						case "cle" :
							if ($value1 == 1) {
								$this->keys [] = $key;
								$nbcle ++;
								$this->cle = $key;
							}
							break;
					}
				}
			}
			/*
			 * Analyse de la cle
			 */
			if ($nbcle < 2) {
				$this->cleMultiple = 0;
			} else {
				$this->cleMultiple = 1;
				$this->cle = "";
			}
		}
		/*
		 * Integration des parametres utilisateur
		 */
		$this->setParam ( $param );
		/*
		 * Integration du codage UTF8
		 */
		
		// if ($this->typeDatabase=="mysql" && $this->UTF8==true) {
		if ($this->UTF8 == true) {
			if ($this->typeDatabase == "mysql") {
				$this->connection->EXECUTE ( "set names 'utf8'" );
			} else {
				$this->connection->EXECUTE ( "SET CLIENT_ENCODING TO UTF8" );
			}
		}
		/*
		 * Forcage des parametres de date
		 */
		if ($this->formatDate == "fr")
			$this->formatDate = 1;
		if ($this->formatDate == "en")
			$this->formatDate = 2;
			/*
		 * Ajout du identifier quote character
		 */
		if ($this->typeDatabase == 'postgre')
			$this->quoteIdentifier = '"';
		elseif ($this->typeDatabase == 'mysql')
			$this->quoteIdentifier = '`';
	}
	function ObjetBDD($bdd, $param = NULL) {
		self::__construct ( $bdd, $param );
	}
	/**
	 *
	 * @param array $param        	
	 * @return void Fonction permettant de forcer les parametres a utiliser
	 */
	function setParam($param) {
		if (is_array ( $param ) == true) {
			foreach ( $param as $key => $value ) {
				$this->$key = $value;
			}
		}
	}
	
	/**
	 * Fonction executant les requetes SQL
	 *
	 * @param
	 *        	$sql
	 * @return ADORecordSet
	 */
	private function execute($sql) {
		$rs = $this->connection->Execute ( $sql );
		// echo $this->connection->ErrorMsg();
		if ((! $rs && $this->debug_mode == 1) || $this->debug_mode == 2)
			print ($this->connection->ErrorMsg () . "<br>" . $sql) ;
			// if(($rs==-1 || isnull($rs)) && $this->debug_mode==1) print ($this->connection->ErrorMsg());
		return $rs;
	}
	/**
	 * Lit un enregistrement en base de donnees
	 *
	 * @param int|array $id        	
	 * @param boolean $getDefault        	
	 * @param int $parentValue        	
	 * @return boolean Ambigous $data, string>
	 */
	function lire($id, $getDefault = false, $parentValue = 0) {
		// Integration cles multiples
		if ($this->cleMultiple == 1) {
			// Verification de la structure de la cle
			if ($this->verifData) {
				if ($this->verifDonnees ( $id ) == false)
					return false;
			}
			$where = "";
			foreach ( $id as $key => $value ) {
				if ($where != "")
					$where .= " and ";
				if (strlen ( preg_replace ( "#[^A-Z]+#", "", $key ) > 0 ))
					$cle = $this->quoteIdentifier . $key . $this->quoteIdentifier;
				else
					$cle = $key;
				$where .= $cle . ' = ' . $value;
			}
		} else {
			/*
			 * Verification de la cle unique
			 */
			if ($this->verifData) {
				if ($this->verifDonnees ( $this->cle ) == false)
					return false;
			}
			if (strlen ( preg_replace ( "#[^A-Z]+#", "", $this->cle ) > 0 ))
				$cle = $this->quoteIdentifier . $this->cle . $this->quoteIdentifier;
			else
				$cle = $this->cle;
			$where = $cle . ' = ' . $id;
		}
		/*
		 * Generation de la liste des colonnes, et integration du type POSTGIS Ne fonctionne que si la description est complete
		 */
		if ($this->fullDescription == 1) {
			$select = "";
			$i = 0;
			foreach ( $this->colonnes as $key => $value ) {
				/*
				 * Rajout des doubles quotes sur le nom des colonnes en cas de présence de majuscules
				 */
				if (strlen ( preg_replace ( "#[^A-Z]+#", "", $key ) > 0 ))
					$cle = $this->quoteIdentifier . $key . $this->quoteIdentifier;
				else
					$cle = $key;
				if ($i == 1)
					$select .= ", ";
				else
					$i = 1;
				if ($value ["type"] == 4)
					/*
					 * Traitement des champs geographiques
					*/
					$select .= "ST_AsText(" . $cle . ")";
				else
					$select .= $cle;
			}
			$sql = "select " . $select . " from " . $this->table . " where " . $where;
		} else {
			$sql = "select * from " . $this->table . " where " . $where;
		}
		$rs = $this->execute ( $sql );
		if (! $rs) {
			if ($getDefault == true) {
				$collection = $this->getDefaultValue ( $parentValue );
			} else {
				$collection = false;
			}
		} else {
			$collection = array ();
			$collection = $rs->fields;
			if ($this->auto_date == 1) {
				$dates = array ();
				$dates [0] = $collection;
				$dates = $this->utilDatesDBVersLocale ( $this->types, $dates );
				$collection = $dates [0];
			}
			if ($this->codageHtml == true)
				$collection = $this->htmlEncode ( $collection );
			if ($this->toUTF8 == true)
				$collection = $this->utf8Encode ( $collection );
			$rs->close ();
		}
		return $collection;
	}
	/**
	 * function read
	 * Synonyme de lire()
	 *
	 * @param
	 *        	$id
	 * @param boolean $getDefault        	
	 * @param int $parentValue        	
	 * @return unknown_type
	 */
	function read($id, $getDefault = false, $parentValue = 0) {
		return $this->lire ( $id, $getDefault, $parentValue );
	}
	/**
	 * function lireParam
	 * Lit un enregistrement a partir d'une commande sql passee en parametre
	 *
	 * @param
	 *        	string - commande sql a executer
	 * @return array : liste des colonnes et des valeurs associees (id fonction lire)
	 */
	function lireParam($sql) {
		$rs = $this->execute ( $sql );
		if (! $rs) {
			$collection = false;
		} else {
			$collection = array ();
			$collection = $rs->fields;
		}
		if ($this->auto_date == 1) {
			$dates = array ();
			$dates [0] = $collection;
			$dates = $this->utilDatesDBVersLocale ( $this->types, $dates );
			$collection = $dates [0];
		}
		if ($this->codageHtml == true)
			$collection = $this->htmlEncode ( $collection );
		if ($this->toUTF8 == true)
			$collection = $this->utf8Encode ( $collection );
		return $collection;
	}
	/**
	 * Synonyme de lireParam()
	 *
	 * @param
	 *        	$sql
	 * @return unknown_type
	 */
	function readSQL($sql) {
		return $this->lireParam ( $sql );
	}
	
	/**
	 * function supprimer
	 * supprime un enregistrement
	 *
	 * @param
	 *        	:integer - cle de l'enregistrement a supprimer
	 * @return :int retourne la valeur adodb de l'execute
	 */
	function supprimer($id) {
		// Integration cles multiples
		if ($this->cleMultiple == 1) {
			// Verification de la structure de la cle
			if ($this->verifData) {
				if ($this->verifDonnees ( $id ) == false)
					return false;
			}
			$where = "";
			foreach ( $id as $key => $value ) {
				if ($where != "")
					$where .= " and ";
				if (strlen ( preg_replace ( "#[^A-Z]+#", "", $key ) > 0 ))
					$cle = $this->quoteIdentifier . $key . $this->quoteIdentifier;
				else
					$cle = $key;
				$where .= $cle . ' = ' . $value;
			}
		} else {
			/*
			 * Verification de la cle unique
			 */
			if ($this->verifData) {
				if ($this->verifDonnees ( $this->cle ) == false)
					return false;
			}
			if (strlen ( preg_replace ( "#[^A-Z]+#", "", $this->cle ) > 0 ))
				$cle = $this->quoteIdentifier . $this->cle . $this->quoteIdentifier;
			else
				$cle = $this->cle;
			$where = $cle . ' = ' . $id;
		}
		return $this->execute ( "delete from " . $this->table . " where " . $where );
	}
	/**
	 * Synonyme de supprimer()
	 *
	 * @param
	 *        	$id
	 * @return unknown_type
	 */
	function delete($id) {
		return $this->supprimer ( $id );
	}
	
	/**
	 * function supprimerChamp
	 * Permet de supprimer un enregistrement identifie par une colonne autre que la cle
	 *
	 * @param
	 *        	integer - identifiant concerne
	 * @param
	 *        	string - nom du champ sur lequel porte la requete
	 *        	
	 */
	function supprimerChamp($id, $champ/*:int*/)/* :int */
{
		if (! is_numeric ( $id ))
			return - 1;
		if ($id > 0) {
			if (strlen ( preg_replace ( "#[^A-Z]+#", "", $champ ) > 0 ))
				$cle = $this->quoteIdentifier . $key . $this->quoteIdentifier;
			else
				$cle = $champ;
			return $this->execute ( "delete from " . $this->table . " where " . $cle . "=" . $id );
		}
	}
	/**
	 * synonyme de supprimerChamp
	 *
	 * @param
	 *        	$id
	 * @param
	 *        	$champ
	 * @return unknown_type
	 */
	function deleteFromField($id, $champ) {
		return $this->supprimerChamp ( $id, $champ );
	}
	
	/**
	 * Function ecrire
	 *
	 * @param
	 *        	array with the name of the columns as identifiers of items
	 * @return Identifier of item, or error code
	 */
	function ecrire($dataBrute) {
		/*
		 * Mise en forme des donnees selon le mode de fonctionnement
		 */
		if ($this->fullDescription == 1) {
			$data = array ();
			foreach ( $this->colonnes as $key => $value ) {
				if (isset ( $dataBrute [$key] )) {
					$data [$key] = $dataBrute [$key];
				}
			}
		} else {
			$data = $dataBrute;
		}
		/*
		 * Decodage HTML (retour de saisie avec codage prealable)
		 */
		// if ($this->codageHtml == true)
		$data = $this->htmlDecode ( $data );
		
		/*
		 * Verification des donnees entrees
		 */
		/*
		 * Forcage a zero de la cle si cle simple et cle automatique
		 */
		if ($this->cleMultiple == 0 && $data [$this->cle] == "" && $this->id_auto > 0)
			$data [$this->cle] = 0;
			/*
		 * Rajout des slashes devant les quotes et autres caracteres concernes pour eviter les attaques par injection de code, et accessoirement autoriser la saisie de guillemets doubles
		 */
		if (get_magic_quotes_gpc () == 0) {
			$data = $this->encodeData ( $data );
		}
		/*
		 * Traitement des dates
		 */
		if ($this->auto_date == 1) {
			$data = $this->utilDatesLocaleVersDB ( $this->types, $data );
		}
		/*
		 * Traitement pour determiner le type de traitement (insert, update)
		 */
		$mode = "";
		if ($this->cleMultiple == 0 && $data [$this->cle] < 1) {
			$mode = "ajout";
		} else {
			/**
			 * id est connu (cle simple) ou cle multiple - on verifie que
			 * l'enregistrement existe en base de donnees
			 */
			$where = "";
			if ($this->cleMultiple == 1) {
				foreach ( $this->keys as $key => $value ) {
					/*
					 * Verification que la cle soit numerique
					 */
					if (is_numeric ( $data [$value] ) == false) {
						$this->errorData [] = array (
								"code" => 1,
								"colonne" => $key,
								"valeur" => $value 
						);
						return - 1;
					}
					
					if ($where != "")
						$where .= " and ";
					if (strlen ( preg_replace ( "#[^A-Z]+#", "", $value ) > 0 ))
						$cle = $this->quoteIdentifier . $value . $this->quoteIdentifier;
					else
						$cle = $value;
					$where .= $cle . ' = ' . $data [$value];
				}
			} else {
				/*
				 * cle unique Verification que la cle soit numerique
				 */
				if (is_numeric ( $data [$this->cle] ) == false) {
					$this->errorData [] = array (
							"code" => 1,
							"colonne" => $this->cle,
							"valeur" => $data [$this->cle] 
					);
					return - 1;
				}
				if (strlen ( preg_replace ( "#[^A-Z]+#", "", $this->cle ) > 0 ))
					$cle = $this->quoteIdentifier . $this->cle . $this->quoteIdentifier;
				else
					$cle = $this->cle;
				$where = $cle . "=" . $data [$this->cle];
			}
			$sql = "select * from " . $this->table . " where " . $where;
			$rs = $this->execute ( $sql );
			if (! $rs->RecordCount () || $rs->RecordCount () == 0) {
				/**
				 * nouveau avec id passe
				 */
				$mode = "ajout";
			} else {
				/**
				 * modif
				 */
				$mode = "modif";
			}
		}
		
		/*
		 * Transformation des virgules en points, si demande
		 */
		if ($this->transformComma) {
			foreach ( $data as $key => $value ) {
				if (@$this->types [$key] == 1) {
					$data [$key] = str_replace ( ",", ".", $value );
				}
			}
		}
		
		if ($this->verifData) {
			if ($this->verifDonnees ( $data, $mode ) == false)
				return false;
		}
		/*
		 * Traitement de la mise en fichier
		 */
		$total = count ( $data );
		if ($mode == "ajout") {
			$sql = "insert into " . $this->table . "(";
			$i = 0;
			$valeur = ") values (";
			foreach ( $data as $key => $value ) {
				// Traitement de la cle automatique. Uniquement sur cle unique !
				if ($this->id_auto == 1 && $key == $this->cle) {
					// on utilise la cle automatique, et le champ courant est la cle... On ne fait rien !
				} elseif ($this->id_auto == 2 && $key == $this->cle) {
					if ($i > 0) {
						$sql .= ", ";
						$valeur .= ", ";
					}
					// On traite la clé automatique gérée par le max()
					if (strlen ( preg_replace ( "#[^A-Z]+#", "", $this->cle ) > 0 ))
						$cle = $this->quoteIdentifier . $this->cle . $this->quoteIdentifier;
					else
						$cle = $this->cle;
					$sqlmax = 'select max(' . $cle . ') from ' . $this->table;
					$rs = $this->execute ( $sqlmax );
					$temp = $rs->fields;
					$cle = $temp [$this->cle] + 1;
					if ($i > 0) {
						$sql .= ", ";
						$valeur .= ", ";
					}
					$i ++;
					if (strlen ( preg_replace ( "#[^A-Z]+#", "", $key ) > 0 ))
						$key = $this->quoteIdentifier . $key . $this->quoteIdentifier;
					$sql .= $key;
					$valeur .= $cle;
				} else {
					if ($i > 0) {
						$sql .= ", ";
						$valeur .= ", ";
					}
					$i ++;
					if (strlen ( preg_replace ( "#[^A-Z]+#", "", $key ) > 0 ))
						$key = $this->quoteIdentifier . $key . $this->quoteIdentifier;
					$sql .= $key;
					if ($value == '' || is_null ( $value )) {
						$valeur .= "NULL";
					} else {
						if (@$this->types [$key] == 1) {
							$valeur .= $value;
						} elseif ($this->types [$key] == 4) {
							/*
							 * Traitement de l'import d'un champ geographique
							 */
							$valeur .= "ST_GeomFromText('" . $value . "'," . $this->srid . ")";
						} else {
							// $valeur .= "'".addslashes($value)."'";
							$valeur .= "'" . $value . "'";
						}
					}
				}
			}
			$sql .= $valeur . ")";
			/*
			 * On rajoute la recuperation de la cle avec postgresql
			 */
			if ($this->typeDatabase == 'postgre' && $this->id_auto == 1) {
				$sql .= ' RETURNING ' . $this->cle;
			}
		}
		if ($mode == "modif") {
			$sql = "update " . $this->table . " set ";
			$i = 0;
			foreach ( $data as $key => $value ) {
				if ($i > 0)
					$sql .= ",";
				$i ++;
				$sql .= " ";
				if (strlen ( preg_replace ( "#[^A-Z]+#", "", $key ) > 0 ))
					$cle = $this->quoteIdentifier . $key . $this->quoteIdentifier;
				else
					$cle = $key;
				if ($value == '' || is_null ( $value )) {
					// Traitement des null
					$sql .= $cle . "=null";
				} else {
					if (@$this->types [$key] == 1) {
						$sql .= $cle . " = " . $value;
					} elseif ($this->types [$key] == 4) {
						/*
						 * Traitement de l'import d'un champ geographique
						 */
						$sql .= $cle . " = ST_GeomFromText('" . $value . "'," . $this->srid . ")";
					} else {
						// $sql .= $key." = '".addslashes($value)."'";
						$sql .= $cle . " = '" . $value . "'";
					}
				}
			}
			$sql .= " where " . $where;
		}
		// printr($sql);
		// die;
		$rs = $this->execute ( $sql );
		if ($mode == "ajout" && $rs != FALSE && $this->id_auto == 1) {
			if (substr ( strtolower ( $this->connection->databaseType ), 0, 7 ) == 'postgre') {
				$ret = $rs->fields [$this->cle];
			} else {
				$ret = $this->connection->Insert_ID ();
			}
		} else {
			$test = $this->connection->Affected_Rows ();
			if ($test > 0) {
				if ($this->cleMultiple == 1) {
					$ret = 1;
				} else {
					$ret = $data [$this->cle];
				}
			} else
				$ret = $test;
		}
		return $ret;
	}
	/**
	 * Synonyme de ecrire()
	 *
	 * @param
	 *        	$data
	 * @return unknown_type
	 */
	function write($data) {
		return $this->ecrire ( $data );
	}
	/**
	 * Function gestListeParam
	 *
	 * @param
	 *        	string - code de la requete SQL
	 * @return tableau contenant la liste des lignes concernees (identique a getListe)
	 */
	function getListeParam($sql) {
		$rs = $this->execute ( $sql );
		if (! $rs) {
			$collection = false;
		} else {
			$collection = array ();
			$collection = $rs->GetRows ( $rs->RecordCount () );
			if ($this->auto_date == 1) {
				$collection = $this->utilDatesDBVersLocale ( $this->types, $collection );
			}
		}
		if ($this->codageHtml == true)
			$collection = $this->htmlEncode ( $collection );
		if ($this->toUTF8 == true)
			$collection = $this->utf8Encode ( $collection );
		return $collection;
	}
	/**
	 * Synonyme de getListeParam()
	 *
	 * @param
	 *        	$sql
	 * @return unknown_type
	 */
	function getListParam($sql) {
		return $this->getListeParam ( $sql );
	}
	/**
	 * function getliste
	 *
	 * @return le contenu de la table
	 */
	function getListe($order = 0) {
		$sql = "select * from " . $this->table;
		if ($order > 0)
			$sql .= " order by " . $order;
		$rs = $this->execute ( $sql );
		if (! $rs) {
			$collection = false;
		} else {
			$collection = array ();
			$collection = $rs->GetRows ( $rs->RecordCount () );
			if ($this->auto_date == 1) {
				$collection = $this->utilDatesDBVersLocale ( $this->types, $collection );
			}
		}
		if ($this->codageHtml == true)
			$collection = $this->htmlEncode ( $collection );
		if ($this->toUTF8 == true)
			$collection = $this->utf8Encode ( $collection );
		return $collection;
	}
	/**
	 * synonyme de getListe()
	 *
	 * @return unknown_type
	 */
	function getList() {
		return $this->getListe ();
	}
	/**
	 * function utilDatesDBVersLocale
	 * transforme les dates d'un tableau (format SGBD) en date locale pour l'affichage
	 *
	 * @param
	 *        	collection equivalente a $this->types
	 * @param
	 *        	array contenant les valeurs a traiter (l'enregistrement en cours)
	 * @return array contenant toutes les valeurs, modifiees ou non
	 */
	private function utilDatesDBVersLocale($types/*:collection*/, $dates/*:collection*/)/* :collection */
{
		foreach ( $types as $key => $value ) {
			if (($value == 2 || $value == 3)) {
				foreach ( $dates as $key1 => $value1 ) {
					
					if (isset ( $dates [$key1] [$key] )) {
						
						if ($dates [$key1] [$key] != "") {
							// Suppression des espaces, tabulations et autres caracteres indesirables presents en debut et fin de chaine
							$date = ltrim ( $dates [$key1] [$key] ); // supprime les caracteres indesirables en debut de chaine
							$date = rtrim ( $date ); // Idem mais en fin de chaine
							                         // suppression de la partie "time" du format "datetime"
							$temp = @explode ( " ", $date );
							$date = $temp [0]; // ne conserve que la partie "date"
							$heure = $temp [1];
							// conversion de format
							// les "@" servent a bloquer d'eventuels messages d'erreurs
							$temp = @explode ( $this->separateurDB, $date );
							
							/*
							 * Reformatage de la date
							 */
							switch ($this->formatDate) {
								case 0 :
									$date = $temp [0] . $this->separateurLocal . $temp [1] . $this->separateurLocal . $temp [2];
									break;
								case 1 :
									$date = $temp [2] . $this->separateurLocal . $temp [1] . $this->separateurLocal . $temp [0];
									break;
								case 2 :
									$date = $temp [1] . $this->separateurLocal . $temp [2] . $this->separateurLocal . $temp [0];
									break;
							}
							if ($value == 3) {
								/*
								 * Reincorporation de l'heure
								 */
								$date .= " " . $heure;
							}
							// Reintegration de la date dans la collection
							$dates [$key1] [$key] = $date;
						}
					}
				}
			}
			
			next ( $types );
		}
		return $dates;
	}
	/**
	 * function utilDatesLocaleVersDB
	 * transforme les dates du tableau (selon le type $this->types, en format utilisable par le SGBD
	 *
	 * @param
	 *        	collection equivalente a $this->types
	 * @param
	 *        	array contenant les valeurs a traiter (l'enregistrement en cours)
	 * @return array contenant toutes les valeurs, modifiees ou non
	 */
	function utilDatesLocaleVersDB($types/*:collection*/, $dates/*:collection*/)/* :collection */
{
		foreach ( $types as $key => $value ) {
			if (($value == 2 || $value == 3) && isset ( $dates [$key] )) {
				if ($dates [$key] != "") {
					$dates [$key] = $this->formatDateLocaleVersDB ( $dates [$key], $value );
				}
			}
			next ( $types );
		}
		return $dates;
	}
	
	/**
	 * function formatDateLocaleVersDB
	 * Formate la date passee en parametre depuis le format de saisie
	 * vers le format utilisable en base de donnees.
	 *
	 * @param string $date        	
	 * @param int $type
	 *        	; 2 : date, 3 : datetime
	 * @return string
	 */
	function formatDateLocaleVersDB($date, $type = 2) {
		// Suppression des espaces, tabulations et autres caracteres indesirables presents en debut et fin de chaine
		$date = ltrim ( $date ); // supprime les caracteres indesirables en debut de chaine
		$date = rtrim ( $date ); // Idem mais en fin de chaine
		                         // separation de la partie "time" du format "datetime"
		$temp = @explode ( " ", $date );
		$date = $temp [0]; // ne conserve que la partie "date"
		$heure = $temp [1]; // stocke l'heure
		                    // conversion de format
		                    // les "@" servent a bloquer d'eventuels messages d'erreurs
		                    // recherche du separateur utilise dans la chaine
		$j = 0;
		do {
			$test = @strpos ( $date, $this->sepValide [$j] );
			if ($test === false)
				$j ++; // !\Important le triple egal verifie que c'est bien la valeur false qui est retournee
		} while ( $j < count ( $this->sepValide ) and ($test === false) );
		$separateurLocal = $this->sepValide [$j]; // separateur trouve dans la chaine de date
		$temp = @explode ( $separateurLocal, $date );
		/*
		 * Assignation des champs
		 */
		switch ($this->formatDate) {
			case 0 :
				$annee = $temp [0];
				$mois = $temp [1];
				$jour = $temp [2];
				break;
			case 1 :
				$annee = $temp [2];
				$mois = $temp [1];
				$jour = $temp [0];
				break;
			case 2 :
				$annee = $temp [2];
				$mois = $temp [0];
				$jour = $temp [1];
				break;
		}
		/*
		 * Prise en compte de l'annee par defaut
		 */
		if ($annee == "")
			$annee = date ( "Y" );
		
		if ($annee < 100) {
			if ($annee <= $this->dateMini) {
				$annee = "20" . $annee;
			} else {
				$annee = "19" . $annee;
			}
		}
		
		$date = $annee . $this->separateurDB . $mois . $this->separateurDB . $jour;
		// Reintegration de l'heure le cas echeant
		if ($type == 3)
			$date .= " " . $heure;
		return $date;
	}
	/**
	 * Fonction permettant de transformer une date au format DB vers le format local
	 *
	 * @param string $date        	
	 * @param integer $type        	
	 * @return string
	 */
	function formatDateDBversLocal($date, $type = 2) {
		/*
		 * Suppression des espaces, tabulations et autres caracteres indesirables presents en debut et fin de chaine
		 */
		$date = ltrim ( $date ); // supprime les caracteres indesirables en debut de chaine
		$date = rtrim ( $date ); // Idem mais en fin de chaine
		                         // suppression de la partie "time" du format "datetime"
		$temp = @explode ( " ", $date );
		$date = $temp [0]; // ne conserve que la partie "date"
		$heure = $temp [1];
		// conversion de format
		// les "@" servent a bloquer d'eventuels messages d'erreurs
		$temp = @explode ( $this->separateurDB, $date );
		
		/*
		 * Reformatage de la date
		 */
		switch ($this->formatDate) {
			case 0 :
				$date = $temp [0] . $this->separateurLocal . $temp [1] . $this->separateurLocal . $temp [2];
				break;
			case 1 :
				$date = $temp [2] . $this->separateurLocal . $temp [1] . $this->separateurLocal . $temp [0];
				break;
			case 2 :
				$date = $temp [1] . $this->separateurLocal . $temp [2] . $this->separateurLocal . $temp [0];
				break;
		}
		if ($type == 3) {
			/*
			 * Reincorporation de l'heure
			 */
			$date .= " " . $heure;
		}
		return ($date);
	}
	/**
	 * function executeSQL
	 *
	 * @param
	 *        	string
	 * @return code de retour de ADODB
	 *         Utilitaire :
	 *         Utilise les fonctions de connexion a la base de donnees pour executer un code SQL quelconque
	 */
	function executeSQL($ls_sql) {
		return $this->execute ( $ls_sql );
	}
	/**
	 * Vidage brutal de la table
	 *
	 * @return codeerreur
	 */
	function vidageTable() {
		return $this->execute ( 'delete from ' . $this->table );
	}
	/**
	 * Synonyme de vidageTable()
	 *
	 * @return unknown_type
	 */
	function clearTable() {
		return $this->vidageTable ();
	}
	/**
	 * function verifDonnees
	 *
	 * @param $data :
	 *        	collection
	 * @return boolean Fonction permettant de verifier les donnees avant ecriture en base de donnees.
	 */
	private function verifDonnees($data, $mode = "") {
		$testok = true;
		foreach ( $data as $key => $value ) {
			/*
			 * Verification des cles
			 */
			if (@$this->types [$key] == 1) {
				if (strlen ( $value ) > 0 && is_numeric ( $value ) == false) {
					$testok = false;
					$this->errorData [] = array (
							"code" => 1,
							"colonne" => $key,
							"valeur" => $value 
					);
				}
			}
			/*
			 * Verification des longueurs des champs textes
			 */
			if ($this->longueurs [$key] > 0) {
				if (strlen ( $value ) > $this->longueurs [$key]) {
					$testok = false;
					$this->errorData [] = array (
							"code" => 2,
							"colonne" => $key,
							"valeur" => $value,
							"demande" => $this->longueurs [$key] 
					);
				}
			}
			
			/*
			 * Verification des masques (patterns)
			 */
			if (strlen ( $this->pattern [$key] ) > 0) {
				if (strlen ( $value ) > 0 && preg_match ( $this->pattern [$key], $value ) == 0) {
					$testok = false;
					$this->errorData [] = array (
							"code" => 3,
							"colonne" => $key,
							"valeur" => $value,
							"demande" => $this->pattern [$key] 
					);
				}
			}
			
			/*
			 * Verification des champs obligatoires
			 */
			if ($this->champs_nonvides [$key] == 1 && strlen ( $data ) == 0) {
				$this->errorData [] = array (
						"code" => 4,
						"colonne" => $value 
				);
				$testok = false;
			}
		}
		
		/*
		 * Verification que tous les champs obligatoires soient renseignes, en mode ajout
		 */
		if ($mode == "ajout") {
			foreach ( $this->champs_nonvides as $key => $value ) {
				if (strlen ( $data [$value] ) == 0) {
					$this->errorData [] = array (
							"code" => 4,
							"colonne" => $value 
					);
					$testok = false;
				}
			}
		}
		
		return $testok;
	}
	/**
	 * Fonction retournant la liste des erreurs relevees lors de l'operation verifData.
	 *
	 * @param $format :
	 *        	si vaut 1, le resultat est retourne sous forme de texte, avec saut de ligne
	 *        	entre chaque erreur. Sinon, le tableau est retourne "brut"
	 * @return unknown_type
	 */
	function getErrorData($format = 0) {
		if ($format == 1) {
			// Formatage du tableau
			$res = "";
			foreach ( $this->errorData as $key => $value ) {
				$data[$key]["valeur"] = htmlentities($data[$key]["valeur"]);
				if ($this->errorData [$key] ["code"] == 0) {
					$res .= $this->errorData [$key] ["message"] . "<br>";
				} elseif ($this->errorData [$key] ["code"] == 1) {
					$res .= "le champ " . $this->errorData [$key] ["colonne"] . " n'est pas numerique. Valeur saisie : " . $this->errorData [$key] ["valeur"] . "<br>";
				} elseif ($this->errorData [$key] ["code"] == 2) {
					$res .= "Le champ " . $this->errorData [$key] ["colonne"] . " est trop grand. Longueur maximale autorisée : " . $this->longueurs [$this->errorData [$key] ["colonne"]] . ". Valeur saisie : " . $this->errorData [$key] ["valeur"] . " (" . strlen ( $this->errorData [$key] ["valeur"] ) . " caracteres)<br>";
				} elseif ($this->errorData [$key] ["code"] == 3) {
					$res .= "Le contenu du champ " . $this->errorData [$key] ["colonne"] . " ne correspond pas au format attendu. Masque autorisé : " . $this->pattern [$this->errorData [$key] ["colonne"]] . ". Valeur saisie : " . $this->errorData [$key] ["valeur"] . "<br>";
				} elseif ($this->errorData [$key] ["code"] == 4) {
					$res .= "Le champ " . $this->errorData [$key] ["colonne"] . " est obligatoire, mais n'a pas été renseigné.<br>";
				}
			}
		} else {
			$res = $this->errorData;
			/*
			 * Reinitialisation des erreurs
			 */
		}
		$this->resetErrorData ();
		return $res;
	}
	/**
	 * private function htmlEncode
	 *
	 * @param
	 *        	$data
	 * @return $data Encode les donnees devant etre affichees en utilisant la fonction htmlspecialchars
	 */
	private function htmlEncode($data) {
		if (is_array ( $data )) {
			foreach ( $data as $key => $value ) {
				$data [$key] = $this->htmlEncode ( $value );
			}
		} else {
			$data = htmlspecialchars ( $data );
		}
		return $data;
	}
	
	/**
	 * Encode en utf8 si demande
	 * 
	 * @param unknown $data        	
	 */
	private function utf8Encode($data) {
		return $data;
		if (is_array ( $data )) {
			foreach ( $data as $key => $value ) {
				$data [$key] = $this->utf8Encode ( $value );
			}
		} else {
			$data = utf8_encode ( $data );
		}
		return $data;
	}
	
	/**
	 * Retire les codages HTML, et convertit en iso-8859-1 le cas echeant
	 *
	 * @param unknown_type $data        	
	 */
	private function htmlDecode($data) {
		if (is_array ( $data )) {
			foreach ( $data as $key => $value ) {
				if (is_array ( $value )) {
					foreach ( $value as $key1 => $value1 ) {
						$data [$key] [$key1] = htmlspecialchars_decode ( $value1 );
						/*
						 * Traitement de l'UTF8
						 */
						if ($this->param ["utf8"] == true)
							$data [$key] [$key1] = utf8_decode ( $data [$key] [$key1] );
					}
				} else {
					$data [$key] = htmlspecialchars_decode ( $value );
					if ($this->param ["utf8"] == true)
						$data [$key] = utf8_decode ( $data [$key] );
				}
			}
		} else {
			$data = htmlspecialchars_decode ( $data );
			if ($this->param ["utf8"] == true)
				$data = utf8_decode ( $data );
		}
		return $data;
	}
	
	/**
	 * function ecrireTableNN
	 *
	 * @param
	 *        	$nomTable
	 * @param
	 *        	$nomCle1
	 * @param
	 *        	$nomCle2
	 * @param
	 *        	$id
	 * @param array $lignes        	
	 * @return unknown_type Fonction permettant d'ecrire dans une table de relation N-N.
	 *         Gere la suppression et l'ajout des lignes
	 *         Ne fonctionne que si la table ne possede que deux champs numeriques
	 */
	function ecrireTableNN($nomTable, $nomCle1, $nomCle2, $id, $lignes) {
		/* Verification des types */
		if (strlen ( $id ) == 0)
			return false;
		if (is_numeric ( $id ) == false)
			return false;
			// Preparation de la requete de lecture des relations existantes
		if (strlen ( preg_replace ( "#[^A-Z]+#", "", $nomCle1 ) > 0 ))
			$cle1 = $this->quoteIdentifier . $nomCle1 . $this->quoteIdentifier;
		else
			$cle1 = $nomCle1;
		if (strlen ( preg_replace ( "#[^A-Z]+#", "", $nomCle2 ) > 0 ))
			$cle2 = $this->quoteIdentifier . $nomCle2 . $this->quoteIdentifier;
		else
			$cle2 = $nomCle2;
		$sql = "select " . $cle2 . " from " . $nomTable . " where " . $cle1 . " = " . $id;
		$rs = $this->execute ( $sql );
		$orig = array ();
		$orig = $rs->getArray ();
		$orig1 = array ();
		
		// Extraction des valeurs en tableau simple
		foreach ( $orig as $key => $value ) {
			$orig1 [] = $value [$nomCle2];
		}
		
		// calcul des intersections (les valeurs presentes dans les deux tableaux)
		$intersect = array_intersect ( $orig1, $lignes );
		// Calcul des tableaux de suppression ou de creation
		$suppr = array_diff ( $orig1, $intersect );
		$creation = array_diff ( $lignes, $intersect );
		// Lancement des mises en fichier
		// Gestion des suppressions
		foreach ( $suppr as $key => $value ) {
			$sql = "delete from " . $nomTable . " where " . $cle1 . " = " . $id . " and " . $cle2 . " = " . $value;
			$this->execute ( $sql );
		}
		// Gestion des insertions
		foreach ( $creation as $key => $value ) {
			$sql = "insert into " . $nomTable . "(" . $cle1 . "," . $cle2 . ") values (" . $id . "," . $value . ")";
			$this->execute ( $sql );
		}
	}
	/**
	 * synonyme de ecrireTableNN()
	 *
	 * @param
	 *        	$nomTable
	 * @param
	 *        	$nomCle1
	 * @param
	 *        	$nomCle2
	 * @param
	 *        	$id
	 * @param
	 *        	$lignes
	 * @return unknown_type
	 */
	function writeTableNN($nomTable, $nomCle1, $nomCle2, $id, $lignes) {
		return $this->ecrireTableNN ( $nomTable, $nomCle1, $nomCle2, $id, $lignes );
	}
	/**
	 * Reinitialise le tableau des erreurs
	 */
	function resetErrorData() {
		$this->errorData = array ();
	}
	/**
	 * Fonction retournant la date du du jour au format courant
	 */
	function getDateJour() {
		$data = date ( 'Y-m-d' );
		return $this->formatDateDBversLocal ( $data );
	}
	function getDateHeure() {
		$data = date ( 'Y-m-d H:i:s' );
		return $this->formatDateDBversLocal ( $data, 3 );
	}
	/**
	 * Fonction permettant de recuperer les valeurs par defaut
	 *
	 * @param int $parentValue        	
	 * @return array
	 */
	function getDefaultValue($parentValue = 0) {
		$data = array ();
		/*
		 * Assignation des valeurs par defaut
		 */
		foreach ( $this->defaultValue as $key => $value ) {
			/*
			 * Test si la valeur renseignee est une fonction ou non
			 */
			if (strlen ( $value ) > 0) {
				if (is_callable ( array (
						$this,
						$value 
				) )) {
					/*
					 * Appel de la fonction
					 */
					$data [$key] = $this->$value ();
				} else {
					/*
					 * Attribution de la valeur par defaut
					 */
					$data [$key] = $value;
				}
			}
		}
		/*
		 * Gestion de l'attribut "pere"
		 */
		if ($parentValue > 0 && strlen ( $this->parentAttrib ) > 0) {
			$data [$this->parentAttrib] = $parentValue;
		}
		return $data;
	}
	
	/**
	 * Fonction encodant toutes les quotes pour le tableau fourni en parametre
	 * Devrait etre appelee avant toute requete SQL
	 *
	 * @param array|string $data        	
	 * @return array|string
	 */
	function encodeData($data) {
		if (is_array ( $data )) {
			/*
			 * Traitement des tableaux
			 */
			foreach ( $data as $key => $value ) {
				$data [$key] = $this->encodeData ( $value );
			}
		} else {
			/*
			 * Traitement des chaines individuelles
			 */
			if ($this->typeDatabase == 'postgre') {
				if ($this->UTF8 == true) {
					if (mb_detect_encoding ( $value ) != "UTF-8")
						$data = mb_convert_encoding ( $data, 'UTF-8' );
				}
				$data = pg_escape_string ( $data );
			} else {
				$data = addslashes ( $data );
			}
		}
		return $data;
	}
}
?>
