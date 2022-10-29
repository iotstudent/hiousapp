<?php

function PaymentGateway($amount,$bank_code,$account_number,$call_back)
{
    GLOBAL $httpcode;
    GLOBAL $response;
    $ref= mt_rand()* time();

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://api.flutterwave.com/v3/transfers',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>'{
        "account_bank":"'.$bank_code.'",
        "account_number":"'.$account_number.'",
        "amount":'.$amount.',
        "narration": "Withdrawal from wallet ",
        "currency": "NGN",
        "reference":"'.$ref.'",
        "callback_url":"'.$call_back.'",
        "debit_currency": "NGN"
    }',
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/json',
    'Authorization:FLWSECK-36ac712ec17cbf1e298ca01b09b0a9f2-X'
  ),
));
  
  $response = curl_exec($curl);
  $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
  curl_close($curl);
}
