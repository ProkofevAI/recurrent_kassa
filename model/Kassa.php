<?php

namespace Recurrent\Model;

include_once "lib/yandex/autoload.php";
use YandexCheckout\Client;

class Kassa 
{
  private $client;
  
  function __construct()
  {
    if (!$this->client) {
      $this->client = new Client();
      $this->client->setAuth(Config::SHOP_ID, Config::SHOP_PASSWORD);
    }
  }
  
  function createPayment ($price, $description = '', $return_url = '', $save_payment_method = false, $payment_method_id = '')
  {
    $uniq = uniqid('', true);
    $query = [
      'amount' => ['value' => $price, 'currency' => 'RUB'],
      'description' => $description,
    ];
    
    if ($save_payment_method) {
      $query['save_payment_method'] = true;
      $query['payment_method_data'] = ['type' => 'bank_card'];
      $query['confirmation'] = ['type' => 'redirect', 'return_url' => $return_url ? $return_url : 'https://vonmi.org'];
    }
    
    if ($payment_method_id) {
      $query['payment_method_id'] = $payment_method_id;
    }
  
    return $this->client->createPayment($query, $uniq);
  }
  
  function checkPayment($id) {
    try {
      $payment = $this->client->getPaymentInfo($id);
    } catch (\Exception $e) {
      $payment = ['status' => 'pending'];
    }
    return $payment;
  }
}
