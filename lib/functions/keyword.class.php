<?php
/** 
 * TestLink Open Source Project - http://testlink.sourceforge.net/ 
 * This script is distributed under the GNU General Public License 2 or later. 
 *
 * @package 	TestLink
 * @copyright 	2007-2009, TestLink community 
 * @version    	CVS: $Id: keyword.class.php,v 1.20 2009/06/09 10:34:27 havlat Exp $
 * @filesource	http://testlink.cvs.sourceforge.net/viewvc/testlink/testlink/lib/functions/keyword.class.php?view=markup
 * @link 		http://www.teamst.org/index.php
 *
 **/

require_once('object.class.php');
require_once('csv.inc.php');
require_once('xml.inc.php');

/**
 * Support for keywords management
 */ 
class tlKeyword extends tlDBObject implements iSerialization,iSerializationToXML,iSerializationToCSV
{
	/** @var string the name of the keyword */
	public $name;

	/** @var string the notes for the keyword */
	public $notes;

	/** @var string the testprojectID the keyword belongs to */
	public $testprojectID;

	// config value
	/** @TODO havlatm: remove this option - no duplicity allowed */
	protected $allowDuplicateKeywords; 
	
	/** error codes */
	const E_NAMENOTALLOWED = -1;
	const E_NAMELENGTH = -2;
	const E_NAMEALREADYEXISTS = -4;
	const E_DBERROR = -8;
	const E_WRONGFORMAT = -16;
	
	protected function _clean($options = self::TLOBJ_O_SEARCH_BY_ID)
	{
		$this->name = null;
		$this->notes = null;
		$this->testprojectID = null;
		if (!($options & self::TLOBJ_O_SEARCH_BY_ID))
		{
			$this->dbID = null;
		}	
			
	}
	
	function __construct($dbID = null)
	{
		parent::__construct($dbID);
		$this->allowDuplicateKeywords = config_get('allow_duplicate_keywords');
	}
	
	function __destruct()
	{
		parent::__destruct();
		$this->_clean();
	}
	
	/* fills the members  */
	function initialize($testprojectID,$name,$notes)
	{
		$this->name = $name;
		$this->notes = $notes;
		$this->testprojectID = $testprojectID;
	}
	
	//BEGIN interface iDBSerialization
	public function readFromDB(&$db,$options = self::TLOBJ_O_SEARCH_BY_ID)
	{
		$this->_clean($options);
		$query = " SELECT id,keyword,notes,testproject_id FROM {$this->tables['keywords']} ";
		
		$clauses = null;
		if ($options & self::TLOBJ_O_SEARCH_BY_ID)
		{
			$clauses[] = "id = {$this->dbID}";		
		}
		if ($clauses)
		{
			$query .= " WHERE " . implode(" AND ",$clauses);
		}
		$info = $db->fetchFirstRow($query);			 
		if ($info)
		{
			$this->dbID = $info['id'];
			$this->name = $info['keyword'];
			$this->notes = $info['notes'];
			$this->testprojectID = $info['testproject_id'];
		}
		return $info ? tl::OK : tl::ERROR;
	}

	public function writeToDB(&$db)
	{
		$result = $this->checkKeyword($db);
		if ($result >= tl::OK)
		{
			$name = $db->prepare_string($this->name);
			$notes = $db->prepare_string($this->notes);

			if ($this->dbID)
			{
				$query = "UPDATE {$this->tables['keywords']} " .
				         " SET keyword = '{$name}',notes='{$notes}',testproject_id={$this->testprojectID}" .
						 " WHERE id = {$this->dbID}";
				$result = $db->exec_query($query);
			}
			else
			{
				$query = " INSERT INTO {$this->tables['keywords']} (keyword,testproject_id,notes) " .
						 " VALUES ('" . $name .	"'," . $this->testprojectID . ",'" . $notes . "')";
				
				$result = $db->exec_query($query);
				if ($result)
				{
					$this->dbID = $db->insert_id($this->tables['keywords']);
				}	
			}
			$result = $result ? tl::OK : self::E_DBERROR;
		}
		return $result;
	}

	/**
	 * Check if keyword name is not duplicated
	 */
	protected function checkKeyword(&$db)
	{
		$this->name = trim($this->name);
		$this->notes = trim($this->notes);
		
		$result = tl::OK;
		if (!$this->allowDuplicateKeywords)
		{
			$result = tlKeyword::doesKeywordExist($db,$this->name,$this->testprojectID,$this->dbID);
		}
		if ($result >= tl::OK)
		{
			$result = tlKeyword::checkKeywordName($this->name);
		}	
		return $result;
	}

	public function deleteFromDB(&$db)
	{
		$sql = "DELETE FROM {$this->tables['testcase_keywords']} WHERE keyword_id = " . $this->dbID;
		$result = $db->exec_query($sql);
		if ($result)
		{
			$sql = "DELETE FROM {$this->tables['object_keywords']}  WHERE keyword_id = " . $this->dbID;
			$result = $db->exec_query($sql);
		}
		if ($result)
		{
			$sql = "DELETE FROM {$this->tables['keywords']} WHERE id = " . $this->dbID;
			$result = $db->exec_query($sql);
		}
		return $result ? tl::OK : tl::ERROR;	
	}

	static public function getByID(&$db,$id,$detailLevel = self::TLOBJ_O_GET_DETAIL_FULL)
	{
		return tlDBObject::createObjectFromDB($db,$id,__CLASS__,tlKeyword::TLOBJ_O_SEARCH_BY_ID,$detailLevel);
	}

	static public function getByIDs(&$db,$ids,$detailLevel = self::TLOBJ_O_GET_DETAIL_FULL)
	{
		return self::handleNotImplementedMethod(__FUNCTION__);
	}

	static public function getAll(&$db,$whereClause = null,$column = null,$orderBy = null,
	                              $detailLevel = self::TLOBJ_O_GET_DETAIL_FULL)
	{
		return self::handleNotImplementedMethod(__FUNCTION__);
	}

	//END interface iDBSerialization
	/** @TODO remove - for legacy purposes */
	public function getInfo()
	{
		return array("id" => $this->dbID,"keyword" => $this->name,
			         "notes" => $this->notes,"testproject_id" => $this->testprojectID);
	}
	
	/**
	 * Checks a keyword against syntactic rules
	 *
	 **/
	static public function checkKeywordName($name)
	{
		$result = tl::OK;
		if ($name != "")
		{
			//we shouldnt allow " and , in keywords any longer
			$dummy = null;
			if (preg_match("/(\"|,)/",$name,$dummy))
				$result = self::E_NAMENOTALLOWED;
		}
		else
		{
			$result = self::E_NAMELENGTH;
        }
		return $result;
	}
	
	/**
	 * checks if a keyword already exists in the database
	 **/
	static public function doesKeywordExist(&$db,$name,$tprojectID,$kwID)
	{
		$result = tl::OK;
		$object_table=DB_TABLE_PREFIX . 'keywords';
		$name = $db->prepare_string(strtoupper($name));
		$query = " SELECT id FROM {$object_table} " .
				 " WHERE UPPER(keyword) ='" . $name.
			     "' AND testproject_id = " . $tprojectID ;
		
		if ($kwID)
		{
			$query .= " AND id <> " .$kwID;
		}
		
		if ($db->fetchFirstRow($query))
		{
			$result = self::E_NAMEALREADYEXISTS;
		}
		return $result;
	}

	//BEGIN interface iSerializationToXML
	
	/**
	 * gets the format descriptor for XML
	 **/
	public function getFormatDescriptionForXML()
	{
		return "<keywords><keyword name=\"name\">Notes</keyword></keywords>";
	}

	public function writeToXML(&$xml,$bNoHeader = false)
	{
		//SCHLUNDUS: maybe written with SimpleXML ?
		$keywords = array($this->getInfo());
		$keywordElemTpl = '<keyword name="{{NAME}}"><notes><![CDATA['."\n||NOTES||\n]]>".'</notes></keyword>'."\n";
		$keywordInfo = array (
							"{{NAME}}" => "keyword",
							"||NOTES||" => "notes",
						);
		$xml .= exportDataToXML($keywords,"{{XMLCODE}}",$keywordElemTpl,$keywordInfo,$bNoHeader);
	}

	public function readFromXML($xml)
	{
		$keyword = simplexml_load_string($xml);
		return $this->readFromSimpleXML($keyword);
	}

	public function readFromSimpleXML($keyword)
	{
		$this->name = NULL;
		$this->notes = NULL;
		
		if (!$keyword || $keyword->getName() != 'keyword')
			return self::E_WRONGFORMAT;
			
		$attributes = $keyword->attributes();
		if (!isset($attributes['name']))
			return self::E_WRONGFORMAT;
			
		$this->name = (string)$attributes['name'];
		if ($keyword->notes)
			$this->notes = (string)$keyword->notes[0];
			
		return tl::OK;
	}
	//END interface iSerializationToXML
	
	//BEGIN interface iSerializationToCSV
	public function getFormatDescriptionForCSV()
	{
		return "keyword;notes";
	}

	public function writeToCSV(&$csv,$delimiter = ';')
	{
		$keyword = array($this->getInfo());
		$sKeys = array(
					"keyword",
					"notes",
				   );
		$csv .= exportDataToCSV($keyword,$sKeys,$sKeys);
	}

	public function readFromCSV($csv,$delimiter = ';')
	{
		$data = explode($delimiter,$csv);
	 					
		$this->name = isset($data[0]) ? $data[0] : null;
		$this->notes = isset($data[1]) ? $data[1] : null;
		
		return sizeof($data) ? true : false;
	}
	//END interface iSerializationToCSV
}
?>