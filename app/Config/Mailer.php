<?php
namespace Config;

use CodeIgniter\Config\BaseConfig;

class Mailer extends BaseConfig
{
    /** PHPMailer SMTP config */
    public string $host       = 'mail.codev-app.my.id';
    public int    $port       = 465;      // 465=ssl, 587=tls
    public string $encryption = 'ssl';    // 'ssl' or 'tls'
    public bool   $auth       = true;

    /** Credentials */
    public string $username   = 'ashoimu@codev-app.my.id';
    public string $password   = 'DAULAHislam2014';

    /** Defaults */
    public string $fromEmail  = 'no-reply.ashoimu@codev-app.my.id';
    public string $fromName   = 'Ashoi-Mu';
    public string $replyTo    = '';       // optional, leave empty to use From
    public bool   $isHTML     = true;
    public string $charset    = 'utf-8';
    public int    $timeout    = 30;       // seconds

    /** Optional: relax SSL (useful on localhost/self-signed) */
    public array $sslOptions = [
        'ssl' => [
            'verify_peer'       => false,
            'verify_peer_name'  => false,
            'allow_self_signed' => true,
        ],
    ];
}
