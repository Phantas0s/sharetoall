<?php

namespace App\Dao;

use Doctrine\ActiveRecord\Search\SearchResult;

class UserDao extends DaoAbstract
{
    protected $_tableName = 'User';
    protected $_primaryKey = 'userId';
    protected $_timestampEnabled = true;
    protected $_softDelete = true;

    protected $_formatMap = [
        'userId' => Format::INT,
        'userRoles' => Format::CSV
    ];

    protected $_hiddenFields = [
        'userPassword',
        'userVerificationToken',
        'userVerified',
        'userPasswordToken',
    ];
}
