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

    public function searchAllNetworksByUserId(int $userId): SearchResult
    {
        $db = $this->getDb();
        $params = [
            'table_alias' => 'n',
            'columns' => [
                'n.*',
                'un.userId',
                'un.userNetworkTokenKey',
                'un.userNetworkTokenSecret',
                'un.userNetworkTokenExpire',
            ],
            'left_join' => [
                [
                    'n',
                    'UserNetwork', 'un',
                    'un.networkSlug = n.networkSlug AND un.userId = ' . $db->quote($userId) . ' OR un.userId IS NULL'
                ]
            ],
        ];


        return parent::search($params);
    }

    public function searchWithNetworkUser(array $params = []): SearchResult
    {
        $defaults = [
            'table_alias' => 'n',
            'columns' => [
                'n.*',
                'un.userId',
                'un.userNetworkTokenKey',
                'un.userNetworkTokenSecret',
                'un.networkSlug',
            ],
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
