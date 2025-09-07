<?php
namespace App\Libraries;

use Config\Mailer as MailerConfig;

class PHPMail
{
    /** @var \PHPMailer\PHPMailer\PHPMailer */
    protected $mailer;

    /** @var MailerConfig */
    protected MailerConfig $config;

    public function __construct(?MailerConfig $config = null)
    {
        $this->config = $config ?? config('Mailer');

        // Try Composer autoload first
        $autoloads = [
            defined('ROOTPATH') ? ROOTPATH . 'vendor/autoload.php' : null,
            __DIR__ . '/../../vendor/autoload.php',
            __DIR__ . '/vendor/autoload.php',
        ];
        foreach ($autoloads as $path) {
            if ($path && file_exists($path)) {
                require_once $path;
                break;
            }
        }

        // If PHPMailer class still not found, try manual includes (put PHPMailer src under app/Libraries/PHPMailer/src)
        if (!class_exists(\PHPMailer\PHPMailer\PHPMailer::class)) {
            $base = APPPATH . 'Libraries/PHPMailer/src/';
            if (is_dir($base)) {
                require_once $base . 'Exception.php';
                require_once $base . 'PHPMailer.php';
                require_once $base . 'SMTP.php';
            }
        }

        if (!class_exists(\PHPMailer\PHPMailer\PHPMailer::class)) {
            throw new \RuntimeException('PHPMailer not found. Install via Composer (phpmailer/phpmailer) or place src files in app/Libraries/PHPMailer/src.');
        }

        $this->mailer = new \PHPMailer\PHPMailer\PHPMailer(true);
        $this->boot();
    }

    protected function boot(): void
    {
        $m = $this->mailer;

        // Base SMTP setup
        $m->isSMTP();
        $m->Host       = $this->config->host;
        $m->SMTPAuth   = $this->config->auth;
        $m->Username   = $this->config->username;
        $m->Password   = $this->config->password;
        $m->Port       = $this->config->port;
        $m->CharSet    = $this->config->charset;
        $m->Timeout    = $this->config->timeout;
        $m->SMTPKeepAlive = false;

        // Encryption
        // Accepts: 'ssl' => ENCRYPTION_SMTPS (465), 'tls' => ENCRYPTION_STARTTLS (587)
        $enc = strtolower($this->config->encryption);
        if ($enc === 'ssl') {
            $m->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
        } elseif ($enc === 'tls') {
            $m->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        } else {
            $m->SMTPSecure = '';
        }

        // SSL options (useful on localhost)
        if (!empty($this->config->sslOptions)) {
            $m->SMTPOptions = $this->config->sslOptions;
        }

        // Defaults
        $fromEmail = $this->config->fromEmail;
        $fromName  = $this->config->fromName;
        $m->setFrom($fromEmail, $fromName);

        if (!empty($this->config->replyTo)) {
            $m->addReplyTo($this->config->replyTo);
        } else {
            $m->addReplyTo($fromEmail, $fromName);
        }

        $m->isHTML($this->config->isHTML);
    }

    /**
     * Send email quickly.
     *
     * @param string|array $to  email or list: 'a@b.com' OR ['a@b.com' => 'Name', 'c@d.com']
     * @param string       $subject
     * @param string       $htmlBody
     * @param array        $options [
     *   'textBody' => 'Plain text',
     *   'cc' => [], 'bcc' => [],
     *   'attachments' => [ '/path/file1', ['path'=>'/path/file2','name'=>'Invoice.pdf'] ],
     *   'fromEmail' => '', 'fromName' => '', 'replyTo' => '',
     * ]
     * @return bool
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public function send($to, string $subject, string $htmlBody, array $options = []): bool
    {
        $m = $this->mailer;

        // Reset recipients & attachments if this instance is reused
        $m->clearAllRecipients();
        $m->clearAttachments();

        // Override From / Reply-To if provided
        if (!empty($options['fromEmail'])) {
            $m->setFrom($options['fromEmail'], $options['fromName'] ?? '');
        }
        if (!empty($options['replyTo'])) {
            $m->clearReplyTos();
            $m->addReplyTo($options['replyTo']);
        }

        // To
        $this->addAddresses($m, $to);

        // CC / BCC
        if (!empty($options['cc']))  $this->addAddresses($m, $options['cc'], 'cc');
        if (!empty($options['bcc'])) $this->addAddresses($m, $options['bcc'], 'bcc');

        // Subject & bodies
        $m->Subject = $subject;
        $m->Body    = $htmlBody;
        if (!empty($options['textBody'])) {
            $m->AltBody = $options['textBody'];
        } else {
            // Auto-generate plain text fallback
            $m->AltBody = trim(strip_tags(preg_replace('/<br\s*\/?>/i', "\n", $htmlBody)));
        }

        // Attachments
        if (!empty($options['attachments'])) {
            foreach ($options['attachments'] as $att) {
                if (is_array($att)) {
                    $m->addAttachment($att['path'] ?? '', $att['name'] ?? '');
                } else {
                    $m->addAttachment($att);
                }
            }
        }

        return $m->send();
    }

    protected function addAddresses($m, $list, string $type = 'to'): void
    {
        if (is_string($list)) {
            $list = [$list];
        }
        foreach ($list as $email => $name) {
            // Allow ['email' => 'Name'] OR ['email1','email2']
            if (is_int($email)) {
                $email = $name;
                $name  = '';
            }
            if ($type === 'to')  $m->addAddress($email, $name);
            if ($type === 'cc')  $m->addCC($email, $name);
            if ($type === 'bcc') $m->addBCC($email, $name);
        }
    }
}
