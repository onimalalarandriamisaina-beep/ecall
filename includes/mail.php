<?php
require_once __DIR__ . '/../config/mail.php'; 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../PHPMailer-master/src/Exception.php';
require __DIR__ . '/../PHPMailer-master/src/PHPMailer.php';
require __DIR__ . '/../PHPMailer-master/src/SMTP.php';

function envoyerEmail($destinataire, $sujet, $corps) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST; 
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USER; 
        $mail->Password   = SMTP_PASS; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = SMTP_PORT; 
        $mail->CharSet    = 'UTF-8';

        $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        $mail->addAddress($destinataire);

        $mail->isHTML(true);
        $mail->Subject = $sujet;
        $mail->Body    = $corps;

        $mail->send();
        
        logEmailAction("SUCCÈS : Email envoyé à $destinataire");
        return true;

    } catch (Exception $e) {
        logEmailAction("ERREUR : Impossible d'envoyer l'email. Erreur PHPMailer : {$mail->ErrorInfo}");
        return false;
    }
}

function logEmailAction($message) {
    $logDir = __DIR__ . '/../logs';
    if (!is_dir($logDir)) {
        mkdir($logDir, 0777, true);
    }
    $logMessage = "[" . date('Y-m-d H:i:s') . "] " . $message . "\n";
    file_put_contents($logDir . '/sent.log', $logMessage, FILE_APPEND);
}

function envoyerEmailBienvenue($email, $nom) {
    $sujet = "Bienvenue sur E-CALL !";
    $corps = '
    <div style="font-family: Arial, sans-serif; max-width: 600px; border: 1px solid #E3D6BF; border-radius: 10px; overflow: hidden;">
        <div style="background: #933B5B; color: white; padding: 20px; text-align: center;">
            <h1 style="margin: 0;">E-CALL</h1>
        </div>
        <div style="padding: 30px; background: #F5F2EB;">
            <h2 style="color: #2D2A24;">Bonjour ' . htmlspecialchars($nom) . ' !</h2>
            <p>Nous sommes ravis de vous compter parmi nos clients. Votre compte a été créé avec succès.</p>
            <div style="text-align: center; margin-top: 30px;">
                <a href="http://localhost/ecall/auth/login.php" style="background: #933B5B; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold;">Accéder à mon compte</a>
            </div>
        </div>
    </div>';
    
    return envoyerEmail($email, $sujet, $corps);
}

function envoyerEmailConfirmationAppel($email, $nom, $appelId, $date) {
    $sujet = "Confirmation d'appel - E-CALL";
    $corps = '
    <div style="font-family: Arial, sans-serif; max-width: 600px; border: 1px solid #E3D6BF; border-radius: 10px; overflow: hidden;">
        <div style="background: #933B5B; color: white; padding: 20px; text-align: center;">
            <h1 style="margin: 0;">E-CALL</h1>
        </div>
        <div style="padding: 30px; background: #F5F2EB;">
            <h2 style="color: #2D2A24;">Bonjour ' . htmlspecialchars($nom) . ' !</h2>
            <p>Votre appel a été enregistré avec succès.</p>
            <p><strong>ID Appel :</strong> #' . $appelId . '</p>
            <p><strong>Date :</strong> ' . $date . '</p>
        </div>
    </div>';
    
    return envoyerEmail($email, $sujet, $corps);
}

function sendWelcomeEmail($email, $nom) {
    return envoyerEmailBienvenue($email, $nom);
}

function sendCallConfirmationEmail($email, $nom, $appelId, $date) {
    return envoyerEmailConfirmationAppel($email, $nom, $appelId, $date);
}