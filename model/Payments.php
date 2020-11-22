<?php

namespace Recurrent\Model;

class Payments
{
  private $table = 'payments';
  
  public $id;
  public $subscriber_id;
  public $kassa_id;
  public $price;
  public $dt;
  public $status;
  
  public function save() {
    if ($this->id) {
      Db::update($this->table, $this->dataForSave());
    } else {
      $this->dt = date('Y-m-d H:i:s');
      $this->id = Db::insert($this->table, $this->dataForSave());
    }
  }
  
  private function dataForSave() {
    $data = [
      'subscriber_id' => $this->subscriber_id,
      'kassa_id' => $this->kassa_id,
      'price' => $this->price,
      'status' => $this->status,
      'dt' => $this->dt,
    ];
    
    if ($this->id) {
      $data['id'] = $this->id;
    }
    
    return $data;
  }
}
