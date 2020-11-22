<?php
require_once "autoload.php";

use Recurrent\Model\Kassa;
use Recurrent\Model\Subscribers;
use Recurrent\Model\Payments;

$subscriber = new Subscribers();
$subscriber->phone = $_POST['phone'];
$subscriber->email = $_POST['email'];
$subscriber->price = (int) $_POST['price'];
$subscriber->status = Subscribers::statuses['wait'];
$subscriber->payment_method_id = '';
$subscriber->payment_date = date('d');
$subscriber->save();

$kassa = new Kassa();
$kassa_payment = $kassa->createPayment($subscriber->price, 'Ежемесячное пожертвование', '', true);

$payment = new Payments();
$payment->subscriber_id = $subscriber->id;
$payment->kassa_id = $kassa_payment['id'];
$payment->price = $subscriber->price;
$payment->status = 'pending';
$payment->save();

echo $kassa_payment['confirmation']['confirmationUrl'];
