<?php
require_once "autoload.php";

use Recurrent\Model\Db;
use Recurrent\Model\Kassa;
use Recurrent\Model\Subscribers;
use Recurrent\Model\Payments;

checkPayments();
createPayments();

function checkPayments() {
  $payments = Db::getAll("SELECT * FROM payments WHERE status = 'pending' AND dt >= ? LIMIT 5", [date('Y-m-d')]);
  $kassa = new Kassa();
  
  foreach ($payments as $payment) {
    $kassa_payment = $kassa->checkPayment($payment->kassa_id);
    
    if ($kassa_payment['status'] !== 'pending') {
      Db::update('payments', ['id' => $payment->id, 'status' => $kassa_payment['status']]);
      
      $subscriber = Subscribers::getById($payment->subscriber_id);
      
      if ($subscriber->status == Subscribers::statuses['wait']) {
        $subscriber->status = Subscribers::statuses['active'];
        $subscriber->payment_method_id = $kassa_payment['payment_method']['id'];
        $subscriber->save();
      }
    }
  }
}


function createPayments() {
  $subscribers = Db::getAll("
    SELECT s.* FROM subscribers s 
    LEFT JOIN payments p ON (p.subscriber_id = s.id AND p.dt >= ?)
    WHERE s.status = ?
    AND s.payment_date = ?
    AND p.id is NULL 
    LIMIT 5", [date('Y-m-d'), Subscribers::statuses['active'], date('d')]);
  
  $kassa = new Kassa();
  
  foreach ($subscribers as $subscriber) {
    $kassa_payment = $kassa->createPayment(
      $subscriber->price, 
      'Ежемесячное пожертвование',
      '', 
      false, 
      $subscriber->payment_method_id
    );
  
    $payment = new Payments();
    $payment->subscriber_id = $subscriber->id;
    $payment->kassa_id = $kassa_payment['id'];
    $payment->price = $subscriber->price;
    $payment->status = 'pending';
    $payment->save();
  }
}
