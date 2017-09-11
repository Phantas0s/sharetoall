<?php

namespace App\Dao;

class MessageDao extends DaoAbstract
{
    protected $_tableName = 'Message';
    protected $_primaryKey = 'messageId';
    protected $_timestampEnabled = true;
    protected $_softDelete = true;

    protected $_formatMap = [
        'messageId' => Format::INT,
    ];
}
