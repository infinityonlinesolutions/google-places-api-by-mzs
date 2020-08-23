<?php
if (!(class_exists('Dbase'))):
	class Dbase{
		var $db_format;
		var $write_dbLink;
		var $USER_NAME;
		var $USER_PWD;
		var $DATABASE;
		var $db_selected;
		var $debug = 0;
		var $error_debug = 0;
        
		function setDBConnections($db_nsme = "", $db_host = "", $db_user = "", $db_pass = ""){
			if ($db_host){
				$this->MYSQL_HOST = $db_host;
			}else{
				die('Host Can\'t Be Empty');
			}
			if($db_user) {
				$this->USER_NAME = $db_user;
			}else{
				die('Username Can\'t Be Empty');
			}
			if($db_pass){
				$this->USER_PWD = $db_pass;
			}else{
				die('Password Can\'t Be Empty');
			}
			if($db_nsme){
				$this->DATABASE = $db_nsme;
			}else{
				die('Database Name Can\'t Be Empty');
			}
        }
        
		function closedb(){
			mysqli_close($this->write_dbLink);
		}

		function __construct($db_nsme = "", $db_host = "", $db_user = "", $db_pass = ""){
			$this->setDBConnections($db_nsme, $db_host, $db_user, $db_pass);
			$this->write_dbLink = mysqli_connect($this->MYSQL_HOST, $this->USER_NAME, $this->USER_PWD, $this->DATABASE) or die("CONNECTION WRITE ERROR: " . mysqli_error());
			mysqli_set_charset($this->write_dbLink, 'utf8');
        }
        
		function escape($str = ""){
			return (mysqli_real_escape_string($this->write_dbLink, $str));
		}

		function insert($data, $table, $debug = true){
			if (!is_array($data)) return (0);
			foreach($data as $key => $name) {
				$attribs[] = $key;
				$values[] = "'" . $this->escape(stripslashes($name)) . "'";
			}
			$attribs = implode(",", $attribs);
			$values = implode(",", $values);
			$query = "insert into $table ($attribs) values ($values)";
			$this->sql = $query;
            if($this->debug == 1 && $debug) {
				$this->log();
			}
			if (mysqli_query($this->write_dbLink, $query)) {
				return mysqli_insert_id($this->write_dbLink);
			}else{
				if($this->error_debug == 1 && $debug) {
					$this->error_log();
				}
				return false;
			}
        }


		function select($retField, $table, $where = "", $groupby = "", $orderby = "", $limit = ""){
			$fields = implode(",", $retField);
			if ($where != "") {
				$q = "select $fields from $table WHERE $where";
			}else {
				$q = "select $fields from $table";
			}
			if ($groupby != "") {
				$q.= " GROUP BY $groupby";
			}
			if ($orderby != "") {
				$q.= " ORDER BY $orderby";
			}
			if ($limit != "") {
				$q.= " LIMIT $limit";
			}
			$this->sql = $q;
			if ($this->debug == 1) {
				$this->log();
			}
			$r = mysqli_query($this->write_dbLink, $q);
			if (!($r)) {
				if ($this->error_debug == 1) {
					$this->error_log();
				}
			}
			$num = mysqli_num_rows($r);
			$i = 0;
			while ($row = mysqli_fetch_object($r)) {
				$cont[$i] = $row;
				$i++;
			}
			if (mysqli_num_rows($r) > 0) {
				return $cont;
			}
		}

        function selectSRow($retField, $table, $where = "", $groupby = "", $orderby = "", $limit = ""){
			$fields = implode(",", $retField);
			if ($where != "") {
				$q = "select $fields from $table WHERE $where";
			}else {
				$q = "select $fields from $table";
			}
			if ($groupby != "") {
				$q.= " GROUP BY $groupby";
			}
			if ($orderby != "") {
				$q.= " ORDER BY $orderby";
			}
			if ($limit != "") {
				$q.= " LIMIT $limit";
			}
			$this->sql = $q;
			if ($this->debug == 1) {
				$this->log();
			}
			$r = mysqli_query($this->write_dbLink, $q);
			if (!($r)) {
				if ($this->error_debug == 1) {
					$this->error_log();
				}
			}
			$num = mysqli_num_rows($r);
			$i = 1;
			$cont = array();
			$row = mysqli_fetch_array($r);
			$cont = $row;
			$i++;
			return $cont;
		}

		function lastID(){
			return mysqli_insert_id($this->write_dbLink);
		}

		function log(){
			$fp = fopen("sql.log", "a");
			if (flock($fp, LOCK_EX)) {
				$sql = str_replace("\n", " ", $this->sql);
				fputs($fp, date("d-m-Y h:i:s") . " --> $sql\n");
				flock($fp, LOCK_UN);
			}
			fclose($fp);
		}
		function error_log_cache(){
			if(!mysqli_errno($this->write_dbLink) || !mysqli_error($this->write_dbLink) || mysqli_errno($this->write_dbLink) == '' || mysqli_error($this->write_dbLink) == ''){
				return false;
			}else{
				return mysqli_errno($this->write_dbLink)." : (". mysqli_error($this->write_dbLink).")";
			}
  	    }
		function error_log(){
			$fp = fopen("sql_error.log", "a");
			if (flock($fp, LOCK_EX)) {
				$sql = str_replace("\n", " ", $this->sql);
				fputs($fp, date("d-m-Y h:i:s") . " --> $sql\n");
				flock($fp, LOCK_UN);
			}
			fclose($fp);
			$strHTML = "<HTML><HEAD><TITLE>MYSQL DEBUG CONSOLE</TITLE></HEAD><BODY>";
			$strHTML.= "<div id='mysql_error_div'><table width='70%' align='center' border='0' cellspacing='0' cellpadding='0'>";
			$strHTML.= "<tr><td width='1%' align='center' bordercolor='#000000' bgcolor='#FF0000'>&nbsp;</td>";
			$strHTML.= "<td width='98%' align='center' bordercolor='#000000' bgcolor='#FF0000'><font color=#FFFFFF face='verdana' size='+1'>MySQL DEBUG CONSOLE</font> </td>";
			$strHTML.= "<td width='1%' align='center' bordercolor='#000000' bgcolor='#FF0000'>&nbsp;</td></tr>";
			$strHTML.= "<tr><td bgcolor='#FF0000'>&nbsp;</td><td>&nbsp;</td><td bgcolor='#FF0000'>&nbsp;</td></tr>";
			$strHTML.= "<tr><td bgcolor='#FF0000'>&nbsp;</td><td style='padding-left:10px'><strong>Query:</strong></td><td bgcolor='#FF0000'>&nbsp;</td></tr>";
			$strHTML.= "<tr><td bgcolor='#FF0000'>&nbsp;</td><td style='padding-left:20px'>$this->sql</td><td bgcolor='#FF0000'>&nbsp;</td></tr>";
			$strHTML.= "<tr><td bgcolor='#FF0000'>&nbsp;</td><td>&nbsp;</td><td bgcolor='#FF0000'>&nbsp;</td></tr>";
			$strHTML.= "<tr><td bgcolor='#FF0000'>&nbsp;</td><td style='padding-left:10px'><strong>Mysql Response:</strong></td><td bgcolor='#FF0000'>&nbsp;</td></tr>";
			$strHTML.= "<tr><td bgcolor='#FF0000'>&nbsp;</td><td style='padding-left:20px'>" . mysqli_error($this->write_dbLink) . "</td><td bgcolor='#FF0000'>&nbsp;</td></tr>";
			$strHTML.= "<tr><td bgcolor='#FF0000'>&nbsp;</td><td>&nbsp;</td><td bgcolor='#FF0000'>&nbsp;</td></tr>";
			$strHTML.= "<tr><td colspan='3' bgcolor='#FF0000' height='2'></td></tr></table>";
			$strHTML.= "</div></BODY></HTML>";
			echo $strHTML;
			echo "<pre>";
			print_r(debug_backtrace());
		}

		function updateCondition($table = "", $cond = "", $arr = array() , $limit = '', $debug = true){
			if (!is_array($arr)) return (0);
			$sql = array();
			foreach($arr as $k => $v) {
				$sql[] = "$k='" . $this->escape(stripslashes($v)) . "'";
			}
			if ($limit != "") {
				$limit = "LIMIT $limit";
			}
			$query = "UPDATE $table SET " . implode(", ", $sql) . " WHERE $cond $limit";
			$this->sql = $query;
			if ($debug && $this->debug == 1) {
				$this->log();
			}
			return mysqli_query($this->write_dbLink, $query);
		}

		function delete($table = "", $condition = ""){
			$query = "DELETE FROM $table WHERE $condition";
			$this->sql = $query;
			if ($this->debug == 1) {
				$this->log();
			}
			if (!(mysqli_query($this->write_dbLink, $query))) {
				if ($this->error_debug == 1) {
					$this->error_log();
				}
				return false;
			}
			else {
				return true;
			}
		}

		function deleteAll($table = ""){
			$query = "TRUNCATE $table";
			$this->sql = $query;
			if ($this->debug == 1) {
				$this->log();
			}
			if (!(mysqli_query($this->write_dbLink, $query))) {
				if ($this->error_debug == 1) {
					$this->error_log();
				}
				return false;
			}
			else {
				return true;
			}
		}

		function object_2_array($result){
			$array = array();
			if (count($result)) {
				foreach($result as $key => $value) {
					if (is_object($value)) {
						$array[$key] = $this->object_2_array($value);
					}elseif (is_array($value)) {
						$array[$key] = $this->object_2_array($value);
					}else {
						$array[$key] = $value;
					}
				}
			}
			return $array;
		}
	}
endif;
?>
