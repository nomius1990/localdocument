<?php

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "https://mybank.icbc.com.cn/servlet/AsynGetDataServlet",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => "Area_code=0200&trademode=1&proIdsIn=130060000043&isFirstTime=0&tranCode=A00500",
  CURLOPT_HTTPHEADER => array(
    "cache-control: no-cache",
    "content-type: application/x-www-form-urlencoded",
    "postman-token: 164a8879-7cd9-59dc-c4de-1162aba58645"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);
$data = json_decode($response,TRUE);

print_r($data);exit;