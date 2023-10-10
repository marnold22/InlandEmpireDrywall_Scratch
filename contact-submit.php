<?php

// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require 'vendor/autoload.php';

//Load .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();


// RECAPTCHA
function reCaptcha($recaptcha)
{
    $secret = $_ENV['SECRET_KEY'];
    $ip = $_SERVER['REMOTE_ADDR'];

    $postvars = array("secret" => $secret, "response" => $recaptcha, "remoteip" => $ip);
    $url = "https://www.google.com/recaptcha/api/siteverify";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postvars);
    $data = curl_exec($ch);
    curl_close($ch);
    return json_decode($data, true);
}

// START FORM PROCESS
// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if ($res['success']) {
        echo '<script>alert("CORRECT: ReCaptcha Successful")</script>';
    }else {
        echo '<script>alert("ERROR: ReCaptcha Not Successful")</script>';
    }
}