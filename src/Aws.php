<?php
namespace Teknicode;

class Aws{
    protected $credentials,$sdk;

    public function __construct($credentials)
    {
        //configure credentials
        $this->credentials = $credentials;


        $this->sdk = new \Aws\Sdk([
            'credentials' => array(
                'key' => $this->credentials['aws_access_key_id'],
                'secret' => $this->credentials['aws_secret_access_key'],
            ),
            'region'   => $this->credentials['default_region'],
            'version'  => 'latest'
        ]);

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

    public function email($to,$subject,$html){
        $ses = $this->sdk->createSes();

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

        try{
            $response = $ses->sendEmail($args);
            return [
                "status" => "success",
                "message_id" => $response->get("MessageId")
            ];
        } catch (\Aws\Ses\Exception\SesException $e){
            return [
                "status" => "error",
                "error" => $e->getAwsErrorMessage()
            ];
        }

    }

}