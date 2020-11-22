<?php

namespace Recurrent\Model;

class Subscribers
{
  const TABLE = 'subscribers';
  
  public $id;
  public $phone;
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
    $entity = new self();
    $entity->id = $data->id;
    $entity->phone = $data->phone;
    $entity->email = $data->email;
    $entity->price = $data->price;
    $entity->payment_method_id = $data->payment_method_id;
    $entity->payment_date = $data->payment_date;
    $entity->status = $data->status;
    $entity->dt = $data->dt;
    return $entity;
  }
  
  public function save() {
    if ($this->id) {
      Db::update(self::TABLE, $this->dataForSave());
    } else {
      $this->dt = date('Y-m-d H:i:s');
      $this->id = Db::insert(self::TABLE, $this->dataForSave());
    }
  }
  
  public function getByPhoneOrEmail() {
    
  }
  
  private function dataForSave() {
    $data = [
      'phone' => $this->phone,
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
