<?php
/**
 * @pcode uchun Telegram Pay kodi
 * @author ShaXzod Jomurodov <shah9409@gmail.com>
 * @contact https://t.me/idFox AND https://t.me/ads_buy
 * @date 13.05.2021 15:21
 */

//sozlash
include 'Telegram.php';
include 'config.php';

$telegram = new Telegram($bot_token);
$efede3 = $telegram->getData();

if(!$efede3) {
    //Telegram botni ulash
    $url = "https://api.telegram.org/bot{$bot_token}/setWebhook?url=https://{$_SERVER['HTTP_HOST']}{$_SERVER['SCRIPT_NAME']}";

    @file_get_contents($url);
    die("webhook o'rnatildi manimcha )");
} else echo "OK";

//basic
$text = $efede3["message"]["text"];
$msg = $efede3["message"]["message_id"];
$chat_id = $efede3["message"]["chat"]["id"];

// chat
$cfname = $efede3['message']['chat']['first_name'];
$cid = $efede3["message"]["chat"]["id"];
$clast_name = $efede3['message']['chat']['last_name'];
$turi = $efede3["message"]["chat"]["type"];
$username = $efede3['message']['chat']['username'];
$cusername = $efede3['message']['chat']['username'];
$ctitle = $efede3['message']['chat']['title'];

// PAYMENT 
$pay_id = $efede3['pre_checkout_query']['id'];
$pay_uid = $efede3['pre_checkout_query']['from']['id'];
$pay_username = $efede3['pre_checkout_query']['from']['username'];

$pay_currency = $efede3['pre_checkout_query']['currency'];
$pay_total_amount = $efede3['pre_checkout_query']['total_amount'];
$pay_invoice_payload = $efede3['pre_checkout_query']['invoice_payload'];


$ms_currency = $efede3["message"]["successful_payment"]["currency"];
$ms_total_amount = $efede3["message"]["successful_payment"]["total_amount"];
$telegram_payment_charge_id = $efede3["message"]["successful_payment"]["telegram_payment_charge_id"];
$provider_payment_charge_id = $efede3["message"]["successful_payment"]["provider_payment_charge_id"];

//User to'lovini tasdiqlash
if ($pay_id) {
    //  $pay_invoice_payload da oldindan berilgan key keladi
    if ($pay_invoice_payload == 'tg_pay') {
        $content = ['pre_checkout_query_id' => $pay_id, 'ok'=>true];
        $telegram->answerPreCheckoutQuery($content);
    } else {
        $content = ['pre_checkout_query_id' => $pay_id, 'ok'=>false, 'error_message' => "To'lov info si topilmadi."];
        $telegram->answerPreCheckoutQuery($content);
    }
}

// to'lov omadli bo'lganda bu haqida bot orqali userga xabar berish
if ($ms_currency) {
    $content = ['chat_id' => $chat_id, 'text' => "Siz {$ms_total_amount} SUM to'ladingiz", 'parse_mode' => 'markdown'];
    $telegram->sendMessage($content);
}

// To'lov qilib ko'rish
if ($text == "/pay") {
    $data = 12*100;
    
    $pul = json_encode([['label' => "E-POLIS narxi", 'amount' => "{$data[summa]}"]]);

    $content = ['chat_id' => $chat_id, 'title' => 'Xalq-sug\'urta Telegram Pay Test', 'description' => '1yillik E-POLIS uchun to\'lov qilish', 'payload' => 'tg_pay', 'provider_token' => $provider_token , 'start_parameter' => 'pay', 'currency' => 'UZS', 'photo_url' => "https://cdn.paycom.uz/merchants/70e14aea76f8957dafc6f6a5004c8df1c51fc211.png", 'photo_width' => 80, 'photo_height' => 80, 'prices' => $pul, 'parse_mode' => 'markdown'];
    $telegram->sendInvoice($content);
    die();
}

// To'lov qilib ko'rish
if ($text == "/start") {

    $pul = json_encode([['label' => "E-POLIS narxi", 'amount' => "120000"],['label' => "Yetkazib berish", 'amount' => "50000"]]);

    $content = ['chat_id' => $chat_id, 'title' => 'Xalq-sug\'urta Telegram Pay Test', 'description' => '1yillik E-POLIS uchun to\'lov qilish', 'payload' => 'tg_pay', 'provider_token' => $provider_token , 'start_parameter' => 'pay', 'currency' => 'UZS', 'photo_url' => "https://cdn.paycom.uz/merchants/70e14aea76f8957dafc6f6a5004c8df1c51fc211.png", 'photo_width' => 80, 'photo_height' => 80, 'prices' => $pul, 'parse_mode' => 'markdown'];
    $xabar = $telegram->sendInvoice($content);
    $xabar_msg = $xabar["result"]["message_id"];

    if (!$xabar_msg) {
        $content = ['chat_id' => $chat_id, 'text' => "Siz @BotFather dan kirib Payment sozlamasini sozlaganingiz aniqmi?", 'parse_mode' => 'markdown'];
        $telegram->sendMessage($content);
    }
}

$telegram->respondSuccess();