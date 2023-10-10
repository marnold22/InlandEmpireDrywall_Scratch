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

// Clean user input function
function clean_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Reset Form
function reset_form_data()
{
    $name = $email = $message = "";
    $name_err = $email_err = $message_err = "";
}

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

// Set initial variables to empty string ""
$name = $email = $message = "";
$name_err = $email_err = $message_err = "";


// START FORM PROCESS
// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Check required fields are filled
    if (!isset($_POST["name"]) || !isset($_POST["email"]) || !isset($_POST["message"])) {
        echo "Sorry one of the required fields is missing, please try again!";
        $name_err = $email_err = $message_err = "REQUIRED FIELDS";
        die();
    }

    // NAME
    $name = clean_input($_POST["name"]);
    if (!preg_match("/^[a-zA-Z ]*$/", $name)) {
        $name_err = "Only letters and whitespace allowed.";
    }

    // EMAIL
    $email = clean_input($_POST["email"]);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $email_error = "Invalid email format";
    }

    // MESSAGE
    $message = clean_input($_POST["message"]);

    // SET RECAPTCHA
    $recaptcha = $_POST['g-recaptcha-response'];
    $res = reCaptcha($recaptcha);

    if ($res['success']) {
        echo '<script>alert("CORRECT: ReCaptcha Successful")</script>';
    }else {
        echo '<script>alert("ERROR: ReCaptcha Not Successful")</script>';
    }
}