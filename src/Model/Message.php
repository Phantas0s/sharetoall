<?php
declare(strict_types=1);

namespace App\Model;

class Message extends ModelAbstract
{
    public function saveMessage(array $values)
    {
        $messageDao = $this->createDao('Message');
        $messageDao->setValues($values);
        $messageDao->save();
    }
}
