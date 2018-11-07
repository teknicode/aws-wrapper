<?php
namespace Teknicode;

class Aws{
    protected $credentials,$sdk,$mailer;

    public function __construct($credentials)
    {
        //configure credentials
        $this->credentials = $credentials;

        //create AWS instance
        $this->sdk = new \Aws\Sdk([
            'credentials' => array(
                'key' => $this->credentials['aws_access_key_id'],
                'secret' => $this->credentials['aws_secret_access_key'],
            ),
            'region'   => $this->credentials['default_region'],
            'version'  => 'latest'
        ]);

        //create PHPMailer instance
        $this->mailer = new \PHPMailer\PHPMailer\PHPMailer;
    }

    public function sms($number,$msg){

        $sns = $this->sdk->createSns();

        $args = array(
            "Message" => $msg,
            "PhoneNumber" => $number,
            'MessageAttributes' => [
                'AWS.SNS.SMS.SenderID' => [
                    'DataType' => 'String',
                    'StringValue' => $this->credentials['sms_sender_id']
                ]
            ],
        );

        try {
            $response = $sns->publish($args);
            return [
                "status" => "success",
                "message_id" => $response->get("MessageId")
            ];
        } catch (\Aws\Exception\AwsException $e) {
            return [
                "status" => "error",
                "error" => $e->getAwsErrorCode()
            ];
        }

    }

    public function email($to,$subject,$html,$attachments=null){

        $ses = $this->sdk->createSes();

        if( !empty($attachments) && gettype($attachments) == "string" ){
            $attachments=explode(",",$attachments);
        }

        if(!$attachments) {

            $args = [
                'Destination' => [
                    'ToAddresses' => [
                        $to,
                    ],
                ],
                'Message' => [
                    'Body' => [
                        'Html' => [
                            'Charset' => 'utf-8',
                            'Data' => $html,
                        ],
                        'Text' => [
                            'Charset' => 'utf-8',
                            'Data' => strip_tags($html),
                        ],
                    ],
                    'Subject' => [
                        'Charset' => 'utf-8',
                        'Data' => $subject,
                    ],
                ],
                'Source' => $this->credentials['email_from']
            ];

            try {
                $response = $ses->sendEmail($args);
                return [
                    "status" => "success",
                    "message_id" => $response->get("MessageId")
                ];
            } catch (\Aws\Ses\Exception\SesException $e) {
                return [
                    "status" => "error",
                    "error" => $e->getAwsErrorMessage()
                ];
            }

        }else{

            $this->mailer->setFrom($this->credentials['email_from']);
            $this->mailer->addAddress($to);
            $this->mailer->Subject = $subject;
            $this->mailer->Body = $html;
            $this->mailer->AltBody = strip_tags($html);
            if( !empty($attachments) && count($attachments) > 0 ) {
                foreach($attachments as $attachment){
                  if( !is_array($attachment) ){
                    $this->mailer->addAttachment($attachment);
                  }else{
                    $this->mailer->addStringAttachment($attachment['content'],$attachment['name']);
                  }
                }
            }

            if (!$this->mailer->preSend()) {
                return [
                    "status" => "error",
                    "error" => $this->mailer->ErrorInfo
                ];
            } else {
                $message = $this->mailer->getSentMIMEMessage();

                try {
                    $send = $ses->sendRawEmail([
                        'RawMessage' => [
                            'Data' => $message
                        ]
                    ]);
                    return [
                        "status" => "success",
                        "message_id" => $send->get('MessageId')
                    ];
                } catch (\Aws\Ses\Exception\SesException $error) {
                    return [
                        "status" => "error",
                        "error" => $error->getAwsErrorMessage()
                    ];
                }


            }


        }

    }

}
