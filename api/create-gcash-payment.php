<?php
header("Content-Type: application/json");

// 🧠 Replace with your PayMongo API keys
$paymongo_secret_key = "sk_test_your_secret_key_here";  // test or live key

// 🧾 Read data from frontend
$data = json_decode(file_get_contents("php://input"), true);
$amount = $data["amount"];
$booking_id = $data["booking_id"];
$name = $data["name"];

// ✅ Prepare the payload
$payload = [
  "data" => [
    "attributes" => [
      "amount" => intval($amount * 100), // centavos
      "redirect" => [
        "success" => "https://yourdomain.com/ui/payment-success.html?booking_id={$booking_id}&amount={$amount}&name=" . urlencode($name),
        "failed"  => "https://yourdomain.com/ui/payment-failed.html"
      ],
      "type" => "gcash",
      "currency" => "PHP"
    ]
  ]
];

// ✅ Send to PayMongo API
$ch = curl_init("https://api.paymongo.com/v1/sources");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
  "Content-Type: application/json",
  "Authorization: Basic " . base64_encode($paymongo_secret_key . ":")
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

$response = curl_exec($ch);
curl_close($ch);

// ✅ Return to frontend
echo $response;
