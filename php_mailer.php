<?php
// Manual include PHPMailer classes
// require __DIR__ . '/app/Libraries/PHPMailer/src/Exception.php';
// require __DIR__ . '/app/Libraries/PHPMailer/src/PHPMailer.php';
// require __DIR__ . '/app/Libraries/PHPMailer/src/SMTP.php';
require __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

try {
    $mail = new PHPMailer(true);

    // Server settings
    $mail->isSMTP();
    $mail->Host       = 'codev-app.my.id';   // SMTP host dari hosting
    $mail->SMTPAuth   = true;
    $mail->Username   = 'ashoimu@codev-app.my.id'; // email penuh
    $mail->Password   = 'DAULAHislam2014';         // password email hosting
    $mail->SMTPSecure = 'ssl';   // 'ssl' untuk 465, 'tls' untuk 587
    $mail->Port       = 465;
    $mail->SMTPOptions = [
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true,
        ],
    ];
    // Recipients
    $mail->setFrom('ashoimu@codev-app.my.id', 'Ashoi-Mu');
    $mail->addAddress('adi.sabwa@gmail.com', 'Adi Sabwa');

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'Tes Tes Tes Email Manual PHPMailer';
    $mail->Body    = '<p>Halo, ini email dari <b>PHPMailer manual</b> tanpa composer</p>';

    // Send
    if ($mail->send()) {
        echo "✅ Email berhasil dikirim!\n";
    } else {
        echo "❌ Email gagal dikirim: " . $mail->ErrorInfo . "\n";
    }
} catch (Exception $e) {
    echo "❌ PHPMailer Exception: {$e->getMessage()}\n";
}
