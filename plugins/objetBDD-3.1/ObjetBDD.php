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
 * @copyright (C) Eric Quinton 2006-2016
 * @version 3.2du 13/05/2016
 * @package ObjetBDD
 * 
 * News :
 * 13/05/2016
 * Rajout de la gestion des exceptions (throw) sur chaque anomalie analysee
 * Basculement de toutes les requetes en mode prepare
 * Suppression des tableaux de valeurs separees (types, longueurs, patterns)
 * 
 * 27/04/2016
 * Ajout du support des exceptions pour toutes les commandes utilisant PDO
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
	 * instance PDO
	 */
	public $connection;
	/**
	 * @public $table : nom de la table
	 */
	public $table;
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
	 * @public $cle : nom du champ utilise comme cle primaire de la table
	 */
	public $cle;
	/**
	 * @public $keys : tableau des cles, si cles multiples
	 */
	public $keys;
	public $cleMultiple = 0;
	/**
	 * @public $id_auto : booleen definissant le type d'id de la table (0=non auto, 1=auto, auto gere par valeur max())
	 */
	public $id_auto = 1;
	/**
	 * @public $auto_date
	 * int definissant la gestion automatique de la date
	 * 0 : pas de gestion de la date
	 * 1 : gestion de la date "classique"
	 * (0|1)
	 */
	public $auto_date = 1;
	/**
	 * @public $separateurDB
	 * char separateur du SGBD
	 */
	public $separateurDB = "-"; // char separateur du serveur de donnees
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
	public $separateurLocal = "/";
	/**
	 * @public $formatDate
	 * int Format de date en affichage
	 * 0 : amj
	 * 1 : jma
	 * 2 : mja
	 */
	public $formatDate = 1;
	/**
	 * @public $dateMini
	 * int annee minimale sur 2 chiffres
	 * Exemple :
	 * 29 pour 1929,
	 * les annees sur 2 chiffres seront traduites entre
	 * 1929 et 2030.
	 */
	public $dateMini = 49;
	/**
	 * @public $debug_mode;
	 * 0 : pas de mode debug
	 * 1 : mode debug sur les erreurs
	 * 2 : mode debug permanent
	 */
	public $debug_mode = 1;
	/**
	 * @public $verifData
	 * 0 : pas de vérification des donnees avant les operations d'ecriture
	 * 1 : activation de la verification des donnees (par defaut)
	 */
	public $verifData = 1;
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
	public $codageHtml = true;
	/**
	 *
	 *
	 * Variable permettant d'indiquer si la table
	 * est totalement decrite dans la classe
	 * $fullDescription = 1 : tous les champs sont decrits
	 * Defaut : 1 
	 * conserve a des fins de compatibilite
	 * @deprecated
	 * @var int
	 */
	public $fullDescription = 1;
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
	public $UTF8 = false;
	/**
	 * Indique s'il faut ou non transformer les valeurs en UTF8
	 * (application codée en UTF8, base de données en autre codage)
	 *
	 * @var boolean
	 */
	public $toUTF8 = false;

	/**
	 * Valeur du SRID pour les imports de donnees geographiques postgis
	 * Vaut -1 si non fourni
	 *
	 * @var integer
	 */
	public $srid = - 1;
	/**
	 * Caractere utilise pour entourer les noms des colonnes
	 *
	 * @var string
	 */
	public $quoteIdentifier = '"';
	/**
	 * Transforme les virgules en points, pour les champs numeriques
	 *
	 * @var integer
	 */
	public $transformComma = 1;
	
	/**
	 * ObjetBDD
	 * Fonction d'initialisation de la classe
	 * Modifier les parametres generaux de la classe si necessaire
	 * Dans la classe heritee, renseigner systematiquement les valeurs suivantes :
	 * $table : nom de la table en base de donnees
	 * decrire les colonnes ($colonnes => array ($key => array ("type"=>1, "requis"=>1))
	 *
	 * @param PDO $p_connection        	
	 * @param array $param        	
	 */
	function __construct(PDO &$p_connection, array $param = NULL) {
		$this->connection = $p_connection;
		$this->param = $param;
		/**
		 * valeurs par defaut / Defaults values *
		 */
		$this->sepValide = array (
				"/",
				"-",
				".",
				" " 
		);
		$this->typeDatabase = $this->connection->getAttribute ( $p_connection::ATTR_DRIVER_NAME );
		
		$this->connection->setAttribute ( $p_connection::ATTR_DEFAULT_FETCH_MODE, $p_connection::FETCH_ASSOC );
		/*
		 * Preparation des tableaux intermediaires a partir du tableau $colonnes
		 */
		if (is_array ( $this->colonnes )) {
			$this->keys = array ();
			// $this->defaultVal = array ();
			$nbcle = 0;
			foreach ( $this->colonnes as $key => $value ) {
				/*
				 * Preparation du tableau des cles
				 */
				if ($value ["key"] == 1 || $value ["cle"] == 1) {
					$this->keys [] = $key;
					$nbcle ++;
					$this->cle = $key;
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
				$this->connection->exec ( "set names 'utf8'" );
			} else {
				$this->connection->exec ( "SET CLIENT_ENCODING TO UTF8" );
			}
		}
		/*
		 * Definition du mode de gestion des erreurs
		 */
		if ($this->debug_mode > 0) {
			$this->connection->setAttribute ( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		} else
			$this->connection->setAttribute ( PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT );
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
		if ($this->typeDatabase == 'pgsql')
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
	 * @param String $sql        	
	 * @return array
	 */
	private function execute($sql) {
		$rs = array ();
		
		try {
			foreach ( $this->connection->query ( $sql ) as $row )
				$rs [] = $row;
			return $rs;
		} catch ( PDOException $e ) {
			$message = $e->getCode () . " " . $e->getMessage ();
			if ($this->debug_mode > 0) {
				$this->addMessage ( $message );
			}
			throw new Exception ( $message );
		}
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
		$data = array ();
		// Integration cles multiples
		if ($this->cleMultiple == 1) {
			// Verification de la structure de la cle
			if ($this->verifData == 1) {
				if ($this->verifDonnees ( $id ) == false)
					return false;
			}
			$where = "";
			foreach ( $id as $key => $value ) {
				if ($where != "")
					$where .= " and ";
				if (strlen ( preg_replace ( "#[^A-Z]+#", "", $key ) ) > 0)
					$cle = $this->quoteIdentifier . $key . $this->quoteIdentifier;
				else
					$cle = $key;
					// $where .= $cle . ' = ' . $value;
				$where .= $cle . ' = :' . $key;
				$data [$key] = $value;
			}
		} else {
			/*
			 * Verification de la cle unique
			 */
			if ($this->verifData == 1) {
				if (is_numeric ( $id ) == false)
					return false;
			}
			if (strlen ( preg_replace ( "#[^A-Z]+#", "", $this->cle ) ) > 0)
				$cle = $this->quoteIdentifier . $this->cle . $this->quoteIdentifier;
			else
				$cle = $this->cle;
				// $where = $cle . ' = ' . $id;
			$where = $cle . ' = :id';
			$data ["id"] = $id;
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
				if (strlen ( preg_replace ( "#[^A-Z]+#", "", $key ) ) > 0)
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
		// $collection = $this->execute ( $sql );
		$collection = $this->executeAsPrepared ( $sql, $data );
		if (count ( $collection ) == 0) {
			if ($getDefault == true) {
				$collection = $this->getDefaultValue ( $parentValue );
			} else {
				$collection = false;
			}
		} else {
			/*
			 * on ne conserve que la premiere ligne
			 */
			$collection = $collection [0];
			if ($this->auto_date == 1) {
				$collection = $this->utilDatesDBVersLocale ( $this->types, $collection );
			}
			if ($this->codageHtml == true)
				$collection = $this->htmlEncode ( $collection );
			if ($this->toUTF8 == true)
				$collection = $this->utf8Encode ( $collection );
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
		$collection = $this->execute ( $sql );
		$collection = $collection [0];
		if ($this->auto_date == 1) {
			$collection = $this->utilDatesDBVersLocale ( $this->types, $collection );
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
		$data = array ();
		// Integration cles multiples
		if ($this->cleMultiple == 1) {
			// Verification de la structure de la cle
			if ($this->verifData == 1) {
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
				$where .= $cle . ' = :' . $key;
				$data [$key] = $value;
			}
		} else {
			/*
			 * Verification de la cle unique
			 */
			if ($this->verifData == 1) {
				if (is_numeric ( $id ) == false)
					return false;
			}
			if (strlen ( preg_replace ( "#[^A-Z]+#", "", $this->cle ) > 0 ))
				$cle = $this->quoteIdentifier . $this->cle . $this->quoteIdentifier;
			else
				$cle = $this->cle;
				// $where = $cle . ' = ' . $id;
			$where = $cle . '= :id';
			$data ['id'] = $id;
		}
		$sql = "delete from " . $this->table . " where " . $where;
		return $this->executeAsPrepared ( $sql, $data, true );
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
			$sql = "delete from " . $this->table . " where " . $cle . "= :id";
			$data ["id"] = $id;
			return $this->executeAsPrepared ( $sql, $data, true );
			// try {
			// $res = $this->connection->exec ( "delete from " . $this->table . " where " . $cle . "=" . $id );
			// } catch ( PDOException $e ) {
			// $res = - 1;
			// if ($this->debug_mode > 0)
			// $this->addMessage ( $e->getMessage () );
			// throw new Exception ( $e->getMessage () );
			// }
			// return $res;
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
						throw new Exception ( $key . " is not numeric (" . $value . ")" );
						return - 1;
					}
					
					if ($where != "")
						$where .= " and ";
					if (strlen ( preg_replace ( "#[^A-Z]+#", "", $value ) ) > 0)
						$cle = $this->quoteIdentifier . $value . $this->quoteIdentifier;
					else
						$cle = $value;
					$where .= $cle . ' = :' . $key;
					$ds [$key] = $data [value];
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
					throw new Exception ( $this->cle . " is not numeric (" . $data [$this->cle] . ")" );
					return - 1;
				}
				if (strlen ( preg_replace ( "#[^A-Z]+#", "", $this->cle ) ) > 0)
					$cle = $this->quoteIdentifier . $this->cle . $this->quoteIdentifier;
				else
					$cle = $this->cle;
				$where = $cle . "= :" . $cle;
				$ds = array (
						$cle->$data [$this->cle] 
				);
			}
			$sql = "select " . $cle . " from " . $this->table . " where " . $where;
			$rs = $this->executeAsPrepared ( $sql, $ds );
			if (count ( $rs ) == 0) {
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
		
		if ($this->verifData == 1) {
			if ($this->verifDonnees ( $data, $mode ) == false)
				return false;
		}
		/*
		 * Traitement de la mise en fichier
		 */
		$total = count ( $data );
		$ds = array ();
		if ($mode == "ajout") {
			$sql = "insert into " . $this->table . "(";
			$i = 0;
			$valeur = ") values (";
			// echo $this->id_auto."<br>";
			foreach ( $data as $key => $value ) {
				// echo $key." ".$value."<br>";
				
				// Traitement de la cle automatique. Uniquement sur cle unique !
				if ($this->id_auto == 1 && $key == $this->cle) {
					// on utilise la cle automatique, et le champ courant est la cle... On ne fait rien !
				} elseif ($this->id_auto == 2 && $key == $this->cle) {
					if ($i > 0) {
						$sql .= ", ";
						$valeur .= ", ";
					}
					// On traite la clé automatique gérée par le max()
					if (strlen ( preg_replace ( "#[^A-Z]+#", "", $this->cle ) ) > 0)
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
					if (strlen ( preg_replace ( "#[^A-Z]+#", "", $key ) ) > 0)
						$key = $this->quoteIdentifier . $key . $this->quoteIdentifier;
					$sql .= $key;
					$valeur .= $cle;
				} else {
					if ($i > 0) {
						$sql .= ", ";
						$valeur .= ", ";
					}
					$i ++;
					if (strlen ( preg_replace ( "#[^A-Z]+#", "", $key ) ) > 0)
						$key = $this->quoteIdentifier . $key . $this->quoteIdentifier;
					$sql .= $key;
					if ($value == '' || is_null ( $value )) {
						$ds [$key] = "NULL";
					} else
						$ds [$key] = $value;
					if ($this->colonnes [$key] ["type"] == 4 && $ds [$key] != "NULL") {
						$valeur .= "ST_GeomFromText( :" . $key . " ," . $this->srid . ")";
					} else
						$valeur .= ":" . $key;
				}
			}
			$sql .= $valeur . ")";
			/*
			 * On rajoute la recuperation de la cle avec postgresql
			 */
			if ($this->typeDatabase == 'pgsql' && $this->id_auto == 1) {
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
				if (strlen ( preg_replace ( "#[^A-Z]+#", "", $key ) ) > 0)
					$cle = $this->quoteIdentifier . $key . $this->quoteIdentifier;
				else
					$cle = $key;
				if ($value == '' || is_null ( $value )) {
					// Traitement des null
					$sql .= $cle . "=null";
				} else {
					$ds [$key] = $value;
					
					if ($this->types [$key] == 4) {
						/*
						 * Traitement de l'import d'un champ geographique
						 */
						$sql .= $cle . " = ST_GeomFromText( :" . $key . " ," . $this->srid . ")";
					} else {
						// $sql .= $key." = '".addslashes($value)."'";
						$sql .= $cle . " = :" . $key;
					}
				}
			}
			$sql .= " where " . $where;
		}
		$rs = $this->executeAsPrepared ( $sql, $ds );
		if ($mode == "ajout" && $this->id_auto == 1) {
			if ($this->typeDatabase == 'pgsql' && count ( $rs ) > 0) {
				$ret = $rs [0] [$this->cle];
			} else {
				$last_id = $this->execute ( 'SELECT LAST_INSERT_ID() as last_id' );
				$ret = $last_id [0] ['last_id'];
			}
		} else {
			$test = count ( $rs );
			if ($test > 0) {
				if ($this->cleMultiple == 1) {
					$ret = 1;
				} else {
					$ret = $data [$this->cle];
				}
			} else
				$ret = - 1;
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
		$collection = $this->execute ( $sql );
		if ($this->auto_date == 1)
			$collection = $this->utilDatesDBVersLocale ( $this->types, $collection );
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
		$collection = $this->execute ( $sql );
		if ($this->auto_date == 1)
			$collection = $this->utilDatesDBVersLocale ( $this->types, $collection );
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
	 * Fonction permettant de renvoyer la liste des enregistrements
	 * a partir de la cle du parent
	 *
	 * @param int $parentId        	
	 * @param number $order        	
	 * @return tableau|NULL
	 */
	function getListFromParent($parentId, $order = 0) {
		if ($parentId > 0 && strlen ( $this->parentAttrib ) > 0) {
			$sql = "select * from " . $this->table;
			/*
			 * Preparation du where
			 */
			if (strlen ( preg_replace ( "#[^A-Z]+#", "", $this->parentAttrib ) ) > 0)
				$cle = $this->quoteIdentifier . $this->parentAttrib . $this->quoteIdentifier;
			else
				$cle = $this->parentAttrib;
			$sql .= " where " . $cle . " = " . $parentId;
			if ($order > 0)
				$sql .= " order by " . $order;
			return $this->getListeParam ( $sql );
		} else
			return null;
	}
	/**
	 * function utilDatesDBVersLocale
	 * transforme les dates d'un tableau (format SGBD) en date locale pour l'affichage
	 *
	 * @param
	 *        	array contenant les valeurs a traiter (l'enregistrement en cours)
	 * @return array contenant toutes les valeurs, modifiees ou non
	 */
	function utilDatesDBVersLocale($data) {
		foreach ( $data as $key => $value ) {
			if (is_array ( $value )) {
				/*
				 * Traitement recursif
				 */
				$data [$key] = $this->utilDatesDBVersLocale ( $value );
			} else {
				if (($this->colonnes [$key] ["type"] == 2 || $this->colonnes [$key] ["type"] == 3) && strlen ( $value ) > 0) {
					/*
					 * Formatage de la date
					 */
					$data [$key] = $this->formatDateDBversLocal ( $value, $this->colonnes [$key] ["type"] );
				}
			}
		}
		
		return $data;
	}
	/**
	 * function utilDatesLocaleVersDB
	 * transforme les dates du tableau (selon le type $this->types, en format utilisable par le SGBD
	 *
	 * @param
	 *        	array contenant les valeurs a traiter (l'enregistrement en cours)
	 * @return array contenant toutes les valeurs, modifiees ou non
	 */
	function utilDatesLocaleVersDB($data) {
		foreach ( $data as $key => $value ) {
			if (is_array ( $value )) {
				/*
				 * Traitement recursif
				 */
				$data [$key] = $this->utilDatesDBVersLocale ( $value );
			} else {
				if (($this->colonnes [$key] ["type"] == 2 || $this->colonnes [$key] ["type"] == 3) && strlen ( $value ) > 0) {
					/*
					 * Formatage de la date
					 */
					$data [$key] = $this->formatDateLocaleVersDB ( $value, $this->colonnes [$key] ["type"] );
				}
			}
		}
		
		return $data;
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
		$date = ltrim ( $date );
		$date = rtrim ( $date ); 
		/*
		 * Separation de la date et de l'heure
		 */
		$temp = @explode ( " ", $date );
		$date = $temp [0]; 
		$heure = $temp [1]; 
		$j = 0;
		do {
			$test = @strpos ( $date, $this->sepValide [$j] );
			if ($test === false)
				$j ++; 
		} while ( $j < count ( $this->sepValide ) and ($test === false) );
		$separateurLocal = $this->sepValide [$j];
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
		/*
		 * Reintegration de l'heure le cas echeant
		 */ 
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
		$date = ltrim ( $date ); 
		$date = rtrim ( $date ); 
		$temp = @explode ( " ", $date );
		$date = $temp [0]; 
		$heure = $temp [1];
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
	 * Fonction permettant de formater l'ensemble des dates d'un tableau
	 * en fournissant les attributs de date concernes
	 * Fonction recursive, traitant les tableaux multilignes
	 *
	 * @param array $data        	
	 * @return array
	 */
	function formatDatesVersLocal($data) {
		return $this->utilDatesDBVersLocale ( $date );
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
		try {
			$res = $this->connection->exec ( 'delete from ' . $this->table );
		} catch ( PDOException $e ) {
			$res = - 1;
			if ($this->debug_mode > 0)
				$this->addMessage ( $e->getMessage () );
			throw new Exception ( $e->getMessage () );
		}
		return $res;
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
			if ($this->colonnes [$key]["type"] == 1) {
				if (strlen ( $value ) > 0 && is_numeric ( $value ) == false) {
					$testok = false;
					$this->errorData [] = array (
							"code" => 1,
							"colonne" => $key,
							"valeur" => $value 
					);
					throw new Exception ( "not numeric value - " . $key . ":" . $value );
				}
			}
			/*
			 * Verification des longueurs des champs textes
			 */
			if ($this->colonnes [$key]["longueur"] > 0) {
				if (strlen ( $value ) > $this->colonnes [$key]["longueur"]) {
					$testok = false;
					$this->errorData [] = array (
							"code" => 2,
							"colonne" => $key,
							"valeur" => $value,
							"demande" => $this->colonnes [$key]["longueur"] 
					);
					throw new Exception ( "string length to height (" . $this->colonnes [$key]["longueur"] . ") - " . $key . ":" . $value );
				}
			}
			
			/*
			 * Verification des masques (patterns)
			 */
			if (strlen ( $this->colonnes [$key]["pattern"] ) > 0) {
				if (strlen ( $value ) > 0 && preg_match ( $this->colonnes [$key]["pattern"], $value ) == 0) {
					$testok = false;
					$this->errorData [] = array (
							"code" => 3,
							"colonne" => $key,
							"valeur" => $value,
							"demande" => $this->colonnes [$key] ["pattern"]
					);
					throw new Exception ( "pattern not compliant (" . $this->colonnes [$key] ["pattern"] . ") - " . $key . ":" . $value );
				}
			}
			
			/*
			 * Verification des champs obligatoires
			 */
			if ($this->colonnes [$key]["requis"] == 1 && strlen ( $data ) == 0) {
				$this->errorData [] = array (
						"code" => 4,
						"colonne" => $value 
				);
				$testok = false;
				throw new Exception ( "field required - " . $key );
			}
		}
		
		/*
		 * Verification que tous les champs obligatoires soient renseignes, en mode ajout
		 */
		if ($mode == "ajout") {
			foreach ( $colonnes as $key=>$colonne ) {
				if ( $colonne["requis"] == 1 && strlen ( $data [$key] ) == 0) {
					$this->errorData [] = array (
							"code" => 4,
							"colonne" => $key 
					);
					$testok = false;
					throw new Exception ( "field required - " . $key );
				}
			}
		}
		
		return $testok;
	}
	
	/**
	 * Ajoute un message d'erreur
	 *
	 * @param string $texte        	
	 */
	function addMessage($texte) {
		$this->errorData [] ["message"] = $texte;
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
				$data [$key] ["valeur"] = htmlentities ( $data [$key] ["valeur"] );
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
		if (strlen ( $id ) == 0) {
			throw new Exception ( "key is empty" );
			return false;
		}
		if (is_numeric ( $id ) == false) {
			throw new Exception ( "key is not numeric (" . $id );
			return false;
		}
		/*
		 * Verification du tableau de valeurs
		 */
		if (! is_array ( $lignes ) && strlen ( $lignes ) > 0) {
			throw new Exception ( "data is not an array" );
			return false;
		}
		
		if (! is_array ( $lignes ))
			$lignes = array ();
		foreach ( $lignes as $key => $value ) {
			if (! is_numeric ( $value )) {
				throw new Exception ( $key . "(" . $value . ") is not numeric" );
				return false;
			}
		}
		// Preparation de la requete de lecture des relations existantes
		if (strlen ( preg_replace ( "#[^A-Z]+#", "", $nomCle1 ) ) > 0)
			$cle1 = $this->quoteIdentifier . $nomCle1 . $this->quoteIdentifier;
		else
			$cle1 = $nomCle1;
		if (strlen ( preg_replace ( "#[^A-Z]+#", "", $nomCle2 ) ) > 0)
			$cle2 = $this->quoteIdentifier . $nomCle2 . $this->quoteIdentifier;
		else
			$cle2 = $nomCle2;
		$sql = "select " . $cle2 . " from " . $nomTable . " where " . $cle1 . " = :id";
		$ds ["id"] = $id;
		$orig = $this->executeAsPrepared ( $sql, $ds );
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
		if (count ( $suppr ) > 0) {
			$sql = "delete from " . $nomTable . " where " . $cle1 . " = " . $id . " and " . $cle2 . " = :key2";
			try {
				$stmt = $this->connection->prepare ( $sql );
				/*
				 * Execution de la requete
				 */
				$res = $stmt->execute ( $data );
				foreach ( $suppr as $key => $value ) {
					$ds = array (
							"key2" => $value 
					);
					$stmt->execute ( $ds );
				}
			} catch ( PDOException $e ) {
				if ($this->debug_mode > 0)
					$this->addMessage ( $e->getMessage () );
				throw new Exception ( $e->message );
			}
		}
		/*
		 * Traitement du tableau de creation
		 */
		if (count ( $creation ) > 0) {
			$sql = "insert into " . $nomTable . "(" . $cle1 . "," . $cle2 . ") values (:id,:key2)";
			try {
				$stmt = $this->connection->prepare ( $sql );
				/*
				 * Execution de la requete
				 */
				foreach ( $suppr as $key => $value ) {
					$ds = array (
							"id" => $id,
							"key2" => $value 
					);
					$stmt->execute ( $ds );
				}
			} catch ( PDOException $e ) {
				if ($this->debug_mode > 0)
					$this->addMessage ( $e->getMessage () );
				throw new Exception ( $e->message );
			}
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
	 * Retourne le login, pour creer la valeur par defaut
	 *
	 * @return string
	 */
	function getLogin() {
		if (isset ( $_SESSION ["login"] ))
			return $_SESSION ["login"];
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
		foreach ( $this->colonnes as $key => $colonne ) {
			if (strlen($colonne["defaultValue"]) > 0) {
				if (is_callable ( array (
						$this,
						$colonne["defaultValue"]
				) )) {
					/*
					 * Appel de la fonction
					 */
					$data [$key] = $this->$colonne["defaultValue"] ();
				} else {
					/*
					 * Attribution de la valeur par defaut
					 */
					$data [$key] = $colonne["defaultValue"];
				}
			}
			/*
			 * Gestion de l'attribut "pere"
			 */
			if ($parentValue > 0 && strlen ( $colonne["parentAttrib"] ) > 0) 
				$data [$key] = $parentValue;
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
			if ($this->typeDatabase == 'pgsql') {
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
	
	/**
	 * Fonction permettant de recuperer la reference d'un champ binaire, pour traitement
	 * par exemple, pour manipuler une image :
	 * $image = new Imagick ();
	 * $image->readimagefile($BlobRef);
	 * ou bien, pour envoyer directement au navigateur :
	 * header("Content-Type: image/png");
	 * fpassthru ($BlobRef);
	 *
	 * @param int $id        	
	 * @param string $fieldName
	 *        	: nom du champ contenant la donnee binaire
	 * @return reference|NULL
	 */
	function getBlobReference($id, $fieldName) {
		if ($id > 0) {
			$sql = "select " . $fieldName . " from " . $this->table . " 
			where " . $this->cle . " = ?";
			$query = $this->connection->prepare ( $sql );
			$query->execute ( array (
					$id 
			) );
			if ($query->rowCount () == 1) {
				$query->bindColumn ( 1, $BlobRef, PDO::PARAM_LOB );
				$query->fetch ( PDO::FETCH_BOUND );
				return $BlobRef;
			}
		}
		return null;
	}
	
	/**
	 * Execute une requete preparee
	 *
	 * @param string $sql
	 *        	: requete preparee, en mode associatif
	 * @param array $data
	 *        	: tableau des valeurs a inserer
	 * @throws Exception
	 * @return s array : tableau des resultats
	 */
	function executeAsPrepared($sql, $data, $onlyExecute = false) {
		try {
			$stmt = $this->connection->prepare ( $sql );
			/*
			 * Execution de la requete
			 */
			$res = $stmt->execute ( $data );
			if ($res && $onlyExecute == false) {
				return $stmt->fetchAll ( PDO::FETCH_ASSOC );
			} else
				return $res;
		} catch ( PDOException $e ) {
			if ($this->debug_mode > 0)
				$this->addMessage ( $e->getMessage () );
			throw new Exception ( $e->message );
		}
	}
	/**
	 * Lit un enregistrement a partir d'une requete preparee
	 *
	 * @param string $sql        	
	 * @param array $data        	
	 * @return array
	 */
	function lireParamAsPrepared($sql, $data) {
		$collection = $this->executeAsPrepared ( $sql, $data );
		$collection = $collection [0];
		if ($this->auto_date == 1) {
			$collection = $this->utilDatesDBVersLocale ( $this->types, $collection );
		}
		if ($this->codageHtml == true)
			$collection = $this->htmlEncode ( $collection );
		if ($this->toUTF8 == true)
			$collection = $this->utf8Encode ( $collection );
		return $collection;
	}
	/**
	 * Lit une collection a partir d'une requete preparee
	 *
	 * @param string $sql        	
	 * @param array $data        	
	 * @return array
	 */
	function getListeParamAsPrepared($sql, $data) {
		$collection = $this->executeAsPrepared ( $sql, $data );
		if ($this->auto_date == 1)
			$collection = $this->utilDatesDBVersLocale ( $this->types, $collection );
		if ($this->codageHtml == true)
			$collection = $this->htmlEncode ( $collection );
		if ($this->toUTF8 == true)
			$collection = $this->utf8Encode ( $collection );
		return $collection;
	}
}
?>
