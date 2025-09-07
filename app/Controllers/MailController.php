<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class MailController extends Controller
{
    public function send()
    {
        $mailer = service('mailer');

        $ok = $mailer->send(
            'adi.sabwa@gmail.com',                                 // to
            'Tes Email dari PHPMailer Library',                    // subject
            '<p>Halo, ini kiriman dari <b>PHPMailService</b></p>', // HTML
            [
                'textBody' => 'Halo Halo Halo, ini kiriman dari PHPMailService',
                // 'attachments' => [WRITEPATH.'uploads/invoice.pdf'],
                // 'cc' => ['team@your-domain.tld' => 'Team'],
                // 'bcc' => ['audit@your-domain.tld'],
            ]
        );

        echo $ok ? '✅ terkirim' : '❌ gagal';
    }
}
