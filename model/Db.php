<?php

namespace Recurrent\Model;

class Db
{
  private static $_db;
  
  private static function db()
  {
    if (!self::$_db) {
      $host = Config::DB_HOST;
      $name = Config::DB_NAME;
      self::$_db = new \PDO("mysql:host=$host;dbname=$name;charset=utf8", Config::DB_USER, Config::DB_PASSWORD);
      self::$_db->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_OBJ);
      self::$_db->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
    }
    
    return self::$_db;
  }
  
  public static function getAll($query, $prms = [])
  {
    $stmt = self::db()->prepare($query);
    $stmt->execute($prms);
    $data = $stmt->fetchAll();
    $stmt->closeCursor();
    return $data;
  }
  
  public static function getOne($query, $prms = [])
  {
    $stmt = self::db()->prepare($query);
    $stmt->execute($prms);
    $data = $stmt->fetch();
    $stmt->closeCursor();
    return $data;
  }
  
  public static function insert($table, $prms = [], $force = false)
  {
    $rows = [];
    $vals = [];
    foreach ($prms as $row => $val) {
      if (!$force && $row == 'id') continue;
      $rows[] = $row;
      $vals[] = $val;
      $ques[] = '?';
    }
    $rows = implode('`,`', $rows);
    $ques = implode(', ', $ques);
    
    $stmt = self::db()->prepare("INSERT INTO `$table` (`$rows`) VALUES ($ques)");
    $stmt->execute($vals);
    $stmt->closeCursor();
    
    $q = "INSERT INTO `$table` (`$rows`) VALUES (".implode(', ', $vals).")";
    
    $sth = self::db()->prepare("SELECT LAST_INSERT_ID() FROM `$table`");
    $sth->execute();
    $id = $sth->fetchColumn();
    $sth->closeCursor();
    return $id;
  }
  
  public static function update($table, $prms = [], $column = 'id')
  {
    $updates = [];
    if (is_array($prms)) {
      $prms = (object)$prms;
    }
    
    foreach ($prms as $row => $val) {
      if ($row === $column) continue;
      $updates[] = "`$row` = ?";
      $vals[] = $val;
    }
    $updates = implode(', ', $updates);
    $vals[] = $prms->$column;
    
    $q = "UPDATE `$table` SET $updates WHERE `$column` = ?";
    $stmt = self::db()->prepare($q);
    $stmt->execute($vals);
    $stmt->closeCursor();
  }
  
  public static function delete($table, $keys) {
    if (!$keys) return;
    $vals = [];
    $where = '';
    foreach ($keys as $key => $value) {
      $where .= $where ? ' AND ' : ' WHERE ';
      $where .= "`$key` = ?";
      $vals[] = $value;
    }
    
    $q = "DELETE FROM `$table` $where";
    $stmt = self::db()->prepare($q);
    $stmt->execute($vals);
    $stmt->closeCursor();
  }
}
