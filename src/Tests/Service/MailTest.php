<?php

namespace App\Tests\Service;
use App\Service\Mail;
use TestTools\TestCase\UnitTestCase;
use Swift_Message;

class MailTest extends UnitTestCase
{
    /** @var Mail */
    private $mail;

    public function setUp()
    {
        $container = $this->getContainer();
        $this->mail = $container->get('service.mail');
    }

    public function testCreateMessageReturnSwiftMessage()
    {
        $values = array(
            'firstname' => 'firstname',
            'lastname' => 'lastname',
            'email' => 'email',
        );

        $message = $this->mail->createNewMessage('test', ['test@test.com'], 'welcome', $values);
        $this->assertInstanceOf(Swift_Message::class, $message);
    }
}
