<?php

define('_DB_HOST', 'localhost');
define('_DB_SOCK', '/tmp/mariadb55.sock');
define('_DB_NAME', 'knet');
define('_DB_USER', 'knet');
define('_DB_PASSWD', 'VrYyVbg4J8NN3gFu');

class Jdb {

  protected $link;

  public function __construct($db = _DB_NAME, $host = _DB_HOST, $user = _DB_USER, $password = _DB_PASSWD, $socket = _DB_SOCK) {

    $this->link = mysqli_connect($host, $user, $password, $db, 0, $socket) or print("Error " . mysqli_error($this->link));

    if ($this->link->connect_errno > 0) {
      die('DB_ERROR [' . $this->link->connect_error . ']');
    }

    if (!$this->link) {
      var_dump($this->link->error);
      die("DB_ERROR:" . mysqli_error($this->link));
    }

    $this->link->query("SET character_set_client=utf8");
    $this->link->query("SET character_set_connection=utf8");
    $this->link->query("SET character_set_results=utf8");
  }

  private function getResult($sQuery) {

    $sQuery = <<<SQL
    $sQuery 
SQL;

    if (!$result = $this->link->query($sQuery)) {
      die('DB_ERROR [' . $this->link->error . ']' . ' STMT :' . $sQuery);
    }

    return $result;
  }
  
    public function getRows( $sTable, $aKeys, $aLimit = []) {

    $sStmt = 'SELECT * FROM `' . $sTable . '` WHERE ';
    foreach ($aKeys as $key => $value) {
      $sCond .= '`' . $key . '` = "' . $value . '" AND';
    }
    
    $pResult = $this->getResult(substr($sStmt . $sCond, 0, -3));

    while ($aRow = $pResult->fetch_assoc()) {
      //var_dump($aRow);
      $aReturn[] = $aRow;
    }

    $pResult->free();

    return $aReturn;
  }

  public function getRow($sQuery) {

    $pResult = $this->getResult($sQuery);

    $aRow = $pResult->fetch_assoc();

    $pResult->free();
    //var_dump($aRow);
    return $aRow;
  }

  public function updateInsert($sTable, $aKeys, $aData) {
    $aData = array_merge($aData,$aKeys);
    $sCond = '';
    $sStmt = 'SELECT count(*) as count FROM `' . $sTable . '` WHERE ';
    foreach ($aKeys as $key => $value) {
      $sCond .= '`' . $key . '` = "' . $value . '" AND';
    }
    $aRow = $this->getRow(substr($sStmt . $sCond, 0, -3));
    var_dump(substr($sStmt . $sCond, 0, -3), $aRow);
    if ($aRow['count'] > 0) {
      //UPDATE
      $sStmt = 'UPDATE `' . $sTable . '` SET ';
      foreach ($aData as $key => $value) {
        $sStmt .= ' `' . $key . '` = "' . $value . '",';
      }
      $sStmt = substr($sStmt, 0, -1) . ' WHERE ' . $sCond;
      //var_dump(substr( $sStmt ,0,-3));

      $this->getResult(substr($sStmt, 0, -3));
      //TODO UNSAFE
    } else {
      //INSERT
      foreach ($aData as $key => $value)
        $aData[$key] = "'$aData[$key]'";
      $sStmt = 'INSERT INTO `' . $sTable . '` (' . implode(array_keys($aData), ' ,') . ') VALUES (' . implode(array_values($aData), ' ,') . ') ';
      $this->getResult($sStmt);
    }

    return $aRow;
  }

  public function update($sTable, $aKeys, $aData) {
    global $bDebug;
    $sCond = '';
    foreach ($aKeys as $key => $value) {
      $sCond .= '`' . $key . '` = "' . $value . '" AND';
    }
    //UPDATE
    $sStmt = 'UPDATE `' . $sTable . '` SET ';
    foreach ($aData as $key => $value) {
      $sStmt .= ' `' . $key . '` = "' . $value . '",';
    }
    $sStmt = substr($sStmt, 0, -1) . ' WHERE ' . $sCond;
    //var_dump(substr( $sStmt ,0,-3));

    $this->getResult(substr($sStmt, 0, -3));
    //TODO UNSAFE
    if ($bDebug)
      return substr($sStmt, 0, -3);
  }

}

$objDb = new Jdb('feiapp', _DB_HOST, 'feiapp', 'PfkxZL0j2w', _DB_SOCK);

