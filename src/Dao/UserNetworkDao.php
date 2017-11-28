<?php
declare(strict_types=1);

namespace App\Dao;

class UserNetworkDao extends DaoAbstract
{
    protected $_tableName = 'UserNetwork';
    protected $_primaryKey = ['userId', 'networkSlug'];
    protected $_timestampEnabled = true;
    protected $_softDelete = true;

    protected $_formatMap = [
        'userId' => Format::INT,
        'networkSlug' => Format::STRING
    ];
}
