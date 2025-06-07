<?php

/*
    * Data fetching and sanatization
*/

use Dotenv\Dotenv;

$name = $email = $phoneno = $message = $website = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = test_input($_POST["name"]);
    $email = test_input($_POST["email"]);
    $website = test_input($_POST["website"]);
    $phoneno = test_input($_POST["phone"]);
    $message = test_input($_POST["message"]);
}

function test_input($data)
{
    $data = trim($data); // Remove whitespaces
    $data = stripslashes($data); // Remove backslashes
    $data = htmlspecialchars($data); // special chars -> HTML entities
    return $data;
}

if (ctype_digit($phoneno) && strlen($phoneno) == 10) {
    $phoneno = "+91-" . $phoneno;
} else {
    $phoneno = "Invalid phone number";
}

if (empty($phoneno)) {
    $phoneno = "Not provided";
}
if (empty($website)) {
    $website = "Not provided";
}

$body = "Name: $name\nEmail: $email\nPhone: $phoneno\nWebsite: $website\nMessage:\n$message";

/*
    * Credentials extraction 
*/
require "vendor/autoload.php";

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->safeload();

$smtp_user = $_ENV['SMTP_USER'];
$smtp_pass = $_ENV['SMTP_PASS'];

/*
    * Email sending script using PHP Mailer
    * Gmail SMTP server used here
*/

use PHPMailer\PHPMailer\PHPMailer;

$mail = new PHPMailer(true);

$mail->isSMTP();
$mail->SMTPAuth = true;
$mail->Host = 'smtp.gmail.com';
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
$mail->Port = 587;

$mail->Username = $smtp_user;
$mail->Password = $smtp_pass;

$mail->setFrom($email, $name);
$mail->addAddress($smtp_user, 'Sarvin Shrivastava');

$mail->Subject = 'New Contact Form Submission';
$mail->Body = $body;

try {
    $mail->send();
    echo "Message sent successfully!";
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$e->getMessage()}";
}
