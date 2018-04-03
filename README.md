## Simple AWS Wrapper

I'm wrote this function to speed up my own workflow when using Amazon Web Services to send email or sms.

I plan to expand it as and when I have time :)

#### Install with composer

https://packagist.org/packages/teknicode/aws-wrapper

`composer require teknicode/aws-wrapper`

#### Requirements

If installed with composer dependents will also be installed. Otherwise, also install and require the AWS PHP SDK version 3+.

#### Usage

Include the class:

`require('./src/Aws.php')`

Create an instance:

```php
$aws_credentials = array(
   "aws_access_key_id" => "", /* REQUIRED */
   "aws_secret_access_key" => "", /* REQUIRED */
   "default_region" => "", /* REQUIRED */
   "sms_sender_id" => "", /* REQUIRED for sms sending */
   "email_from" => "" /* REQUIRED for email sending */
);

$wrapper = new Teknicode\Aws($aws_credentials);
```

Send SMS:

```php
$sms = $wrapper->sms("phone_number","message");

returns
array("status"=>"success","message_id"=>"{MESSAGE ID}")
or
array("status"=>"failed","error"=>"{ERROR MESSAGE}")
```

Send Email:

```php
$sms = $wrapper->email("email address","subject","html");

returns
array("status"=>"success","message_id"=>"{MESSAGE ID}")
or
array("status"=>"failed","error"=>"{ERROR MESSAGE}")
```

#### License

Copyright 2018 Teknicode

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.