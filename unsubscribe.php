<?php
require_once "autoload.php";

use Recurrent\Model\Subscribers;

$subscribers = Subscribers::findByEmailAndDate($_POST['email'], $_POST['date']);

if (count($subscribers) == 0 || count($subscribers) > 1) {
  echo 'Не удалось найти. Пожалуйста, напишите нам';
  die();
}

if ($subscribers[0]->status == Subscribers::statuses['deleted']) {
  echo 'Вы уже отписались от пожертвования';
  die();
}

$subscribers[0]->status = Subscribers::statuses['deleted'];
$subscribers[0]->save();
echo 'Вы успешно отписались.';
