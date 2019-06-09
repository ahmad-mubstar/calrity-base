<?php

class Shopgo_Store_Model_Store extends Mage_Core_Model_Store
{
	public function time_elapsed_B($secs){
		$bit = array(
			'year'        => $secs / 31556926 % 12,
			'week'        => $secs / 604800 % 52,
			'day'        => $secs / 86400 % 7,
			'hour'        => $secs / 3600 % 24
//			'minute'    => $secs / 60 % 60,
//			'second'    => $secs % 60
		);

		foreach($bit as $k => $v){
			if($v > 1)$ret[] = $v . ' ' . ' ' . Mage::helper('store')->__($k . 's');
			if($v == 1)$ret[] = $v . ' ' . Mage::helper('store')->__($k);
		}

		array_splice($ret, count($ret)-1, 0, Mage::helper('store')->__('and'));
		$ret[] = '';
		return join(' ', $ret);
	}

	public function getStoreData($storeName) {

		$domain = explode('.', $_SERVER['HTTP_HOST']);
		if ($domain[1] == "shopgo" && $domain[2] == "com") {
			$link = mysql_connect('localhost:/tmp/mysql.sock', 'root', 't00r', 'ms2p2v5_ms28gw5');
		} else {
			$link = mysql_connect('localhost:/tmp/mysql.sock', 'ms2p2v5_ms28g4i', '+.x(9-dkZ;Jv', 'ms2p2v5_ms28gw5');
		}


		if (!$link) {
			die('Could not connect: ' . mysql_error());
		}

		$sql = "SELECT * from shopgo_storecreator_store where name = '" . $storeName  ."'";
		$db_selected = mysql_select_db('ms2p2v5_ms28gw5', $link);
		if (!$db_selected) {
			die ('Can\'t use foo : ' . mysql_error());
		}

		$result = mysql_query($sql);
		if ($result)
		{
		}
		else
		{
			echo "Database Error: " . mysql_error()."<br><b>" . $sql . "</b>";
			die();
		}
		mysql_close($link);
		return $result;
	}

}