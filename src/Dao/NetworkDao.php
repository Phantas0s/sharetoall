<?php
declare(strict_types=1);

namespace App\Dao;

use Doctrine\ActiveRecord\Search\SearchResult;

class NetworkDao extends DaoAbstract
{
    protected $_tableName = 'Network';
    protected $_primaryKey = 'networkSlug';

    protected $_formatMap = [
        'networkSlug' => Format::STRING,
    ];

    public function searchWithNetworkUser(array $params = []): SearchResult
    {
        $defaults = [
            'table_alias' => 'n',
            'columns' => ['n.*', 'un.userId', 'un.userNetworkToken'],
            'left_join' => [
                [
                    'n',
                    'UserNetwork', 'un',
                    'un.networkSlug = n.networkSlug',
                ]
            ]
        ];

        if (!isset($params['cond'])) {
            $params['cond'] = [];
        }

        $params = array_merge($defaults, $params);

        return parent::search($params);
    }
}
