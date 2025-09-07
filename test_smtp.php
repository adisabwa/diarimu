<?php
require 'vendor/autoload.php'; // kalau pakai composer, tapi bisa di-skip

$host = 'mail.codev-app.my.id';
$port = 465; // 465 (SSL) atau 587 (TLS)
$username = 'ashoimu@codev-app.my.id'; // email penuh
$password = 'DAULAHislam2014';     // password email dari cPanel

// Buat koneksi SSL
$contextOptions = [
    'ssl' => [
        'verify_peer'       => false,
        'verify_peer_name'  => false,
        'allow_self_signed' => true,
    ]
];
$context = stream_context_create($contextOptions);

echo "Mencoba connect ke $host:$port...\n";

$fp = stream_socket_client(
    "ssl://$host:$port",
    $errno,
    $errstr,
    30,
    STREAM_CLIENT_CONNECT,
    $context
);

if (!$fp) {
    die("Gagal koneksi: $errstr ($errno)\n");
}

echo "✅ Terhubung ke SMTP server\n";

// Baca respon awal
echo fgets($fp, 512);

// EHLO
fwrite($fp, "EHLO testdomain.com\r\n");
while ($line = fgets($fp, 512)) {
    echo $line;
    if (strpos($line, '250 ') === 0) break;
}

// AUTH LOGIN
fwrite($fp, "AUTH LOGIN\r\n");
echo fgets($fp, 512);

// Kirim username
fwrite($fp, base64_encode($username) . "\r\n");
echo fgets($fp, 512);

// Kirim password
fwrite($fp, base64_encode($password) . "\r\n");
echo fgets($fp, 512);

fwrite($fp, "QUIT\r\n");
fclose($fp);