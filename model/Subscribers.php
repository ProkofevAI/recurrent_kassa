<?php

namespace Recurrent\Model;

class Subscribers
{
  const TABLE = 'subscribers';
  
  public $id;
  public $name;
  public $email;
  public $price;
  public $payment_method_id;
  public $payment_date;
  public $status;
  public $dt;
  
  const statuses = [
    'wait' => 0,
    'active' => 1,
    'deleted' => -1,
  ];
  

  public static function getById($id) {
    $data = Db::getOne("SELECT * FROM `".self::TABLE."` WHERE id = ?", [$id]);
    if (!$data) return null;
    
    $entity = new self();
    $entity->id = $data->id;
    $entity->name = $data->name;
    $entity->email = $data->email;
    $entity->price = $data->price;
    $entity->payment_method_id = $data->payment_method_id;
    $entity->payment_date = $data->payment_date;
    $entity->status = $data->status;
    $entity->dt = $data->dt;
    return $entity;
  }
  
  public static function findByEmailAndDate($email, $dt) {
    $data = Db::getAll("SELECT * FROM `".self::TABLE."` WHERE email = ? AND payment_date = ?", [$email, $dt]);
    $result = [];
    foreach ($data as $row) {
      $entity = new self();
      $entity->id = $row->id;
      $entity->name = $row->name;
      $entity->email = $row->email;
      $entity->price = $row->price;
      $entity->payment_method_id = $row->payment_method_id;
      $entity->payment_date = $row->payment_date;
      $entity->status = $row->status;
      $entity->dt = $row->dt;
  
      $result[] = $entity;
    }

    return $result;
  }
  
  public function save() {
    if ($this->id) {
      Db::update(self::TABLE, $this->dataForSave());
    } else {
      $this->dt = date('Y-m-d H:i:s');
      $this->id = Db::insert(self::TABLE, $this->dataForSave());
    }
  }
  
  private function dataForSave() {
    $data = [
      'name' => $this->name,
      'email' => $this->email,
      'price' => $this->price,
      'payment_method_id' => $this->payment_method_id,
      'payment_date' => $this->payment_date,
      'status' => $this->status,
      'dt' => $this->dt,
    ];
    
    if ($this->id) {
      $data['id'] = $this->id;
    }
    
    return $data;
  }
}
