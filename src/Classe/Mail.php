<?php

namespace App\Classe;

use \Mailjet\Client;
use \Mailjet\Resources;

class Mail{
    private $api_key = '8103c56945803ecc98c33f6d78a35fdc';
    private $api_key_secret = '76347ba4984a6c1c307fc71e3bb2fdd9';

    public function send($to_email, $to_name, $subject, $content, $token)
    {
        $mj = new Client($this->api_key, $this->api_key_secret, true, ['version' => 'v3.1']);
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "snowtricksprojet@gmail.com",
                        'Name' => "Snowtricks"
                    ],
                    'To' => [
                        [
                            'Email' => $to_email,
                            'Name' => $to_name
                        ]
                    ],
                    'TemplateID' => 4426123,
                    'TemplateLanguage' => true,
                    'Subject' => $subject,
                    'Variables' => [
                        'content' => $content,
                        'token' => $token
                    ]
                ]
            ]
        ];
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        $response->success(); //&& dd($response->getData());
    }

    public function sendResetPassword($to_email, $to_name, $subject, $content)
    {
        $mj = new Client($this->api_key, $this->api_key_secret, true, ['version' => 'v3.1']);
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "snowtricksprojet@gmail.com",
                        'Name' => "Snowtricks"
                    ],
                    'To' => [
                        [
                            'Email' => $to_email,
                            'Name' => $to_name
                        ]
                    ],
                    'TemplateID' => 4412113,
                    'TemplateLanguage' => true,
                    'Subject' => $subject,
                    'Variables' => [
                        'content' => $content,
                    ]
                ]
            ]
        ];
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        $response->success(); //&& dd($response->getData());
    }
}
?>