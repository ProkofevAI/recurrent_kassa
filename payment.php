<?php
require_once "autoload.php";

use Recurrent\Model\Kassa;
use Recurrent\Model\Subscribers;
use Recurrent\Model\Payments;

if (!$_POST['name']) {
  echo json_encode(['message' => 'Пожалуйста, заполните имя']);
  die();
}

if (!$_POST['email']) {
  echo json_encode(['message' => 'Пожалуйста, заполните email']);
  die();
}

$price = (int) $_POST['price'];
$isRecurrent = $_POST['type'] == 1;
$subscriber = new Subscribers();

if ($isRecurrent) {
  $subscriber->name = $_POST['name'];
  $subscriber->email = $_POST['email'];
  $subscriber->price = $price;
  $subscriber->status = Subscribers::statuses['wait'];
  $subscriber->payment_method_id = '';
  $subscriber->payment_date = date('d');
  $subscriber->save();
}

$kassa = new Kassa();
$kassa_payment = $kassa->createPayment($price, $isRecurrent ? 'Ежемесячное пожертвование' : 'Пожертвование', '', $isRecurrent);

$payment = new Payments();
$payment->subscriber_id = $subscriber->id ? $subscriber->id : 0;
$payment->kassa_id = $kassa_payment['id'];
$payment->price = $price;
$payment->status = 'pending';
$payment->save();

echo json_encode(['redirect' => $kassa_payment['confirmation']['confirmationUrl']], JSON_UNESCAPED_UNICODE);
