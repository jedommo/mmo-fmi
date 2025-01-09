<?php

$botToken = "1687364449:AAHpiyYwUPdccB0Ut5g9-1xPKRbfLoPxWbQ";
$apiUrl = "https://api.telegram.org/bot$botToken/";


$update = json_decode(file_get_contents("php://input"), true);

if (isset($update["message"])) {
    $chatId = $update["message"]["chat"]["id"];
    $text = $update["message"]["text"];


    $reply = "You said: $text"; 


    file_get_contents($apiUrl . "sendMessage?chat_id=" . $chatId . "&text=" . urlencode($reply));
}
