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


    if ($name_err == "" && $email_err == "" && $message_err == "") {
        // SEND EMAIL

        //Unset the post submission (for next load)
        unset($_POST['submit']);

        // Compose the email
        $composed_email = "";
        $composed_email .= "Name: " . $name . "<br>";
        $composed_email .= "Email: " . $email . "<br>";
        $composed_email .= "Message: " . $message . "<br>";

        // Create new PHPMailer object
        $mail = new PHPMailer(TRUE);

        // Test Settings for SMTP Server
        $mail->SMTPDebug = SMTP::DEBUG_OFF;
        $mail->isSMTP();
        $mail->Host       = $_ENV["SMTP_HOST"];
        $mail->SMTPAuth   = true;
        $mail->Username   = $_ENV["FROM_EMAIL"];
        $mail->Password   = $_ENV["FROM_EMAIL_PASS"];
        $mail->Port       = 587;
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        //Recipients
        $mail->setFrom($_ENV["FROM_EMAIL"], 'Inland Empire Website');           // Set default address that emails are sent from
        $mail->addAddress($_ENV["TO_EMAIL"], 'Inland Empire Drywall');          // This is who the email is being sent to (ie. Ben's work email)

        // Content
        $mail->isHTML(true);                                              // Set email format to HTML
        $mail->Subject = 'Request More Information From Website';         // Subject
        $mail->Body    = $composed_email;                                 // Body
        $mail->AltBody = $composed_email;                                 // Alt. Body

        // Send the email
        if ($mail->send()) {
            $mailsend_success = "Message sent! Thank you for contacting us.";
            // echo '<script>alert("' . $mailsend_success . '")</script>';
            reset_form_data();
            header("Location: thankyou.html");
        } else {
            $mailsend_error = "Message could not be sent. Mailer Error: {" . $mail->ErrorInfo . "}";
            echo '<script>alert("' . $mailsend_error . '")</script>';
            reset_form_data();
        }
    }
}