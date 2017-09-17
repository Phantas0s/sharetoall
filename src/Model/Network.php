<?php

namespace App\Model;

use Doctrine\ActiveRecord\Search\SearchResult;

/**
 * @see https://github.com/lastzero/doctrine-active-record
 */
class Network extends ModelAbstract
{
    protected $_daoName = 'Network';

    public function findByNetworkUser(int $userId): SearchResult
    {
        $params[] = 'un.userId = ' . $userId . ' OR un.userId IS NULL';
        $results = $this->getDao()->searchWithNetworkUser(['cond' => $params]);

        return $results;
    }

    public function findWithNetworkUser(array $params): SearchResult
    {
        $results = $this->getDao()->searchWithNetworkUser(['cond' => $params]);
        return $results;
    }

    public function hasToken()
    {
        return !empty($this->userNetworkToken);
    }
}
