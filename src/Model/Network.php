<?php

namespace App\Model;

use Doctrine\ActiveRecord\Search\SearchResult;

/**
 * @see https://github.com/lastzero/doctrine-active-record
 */
class Network extends ModelAbstract
{
    protected $_daoName = 'Network';

    public function findByNetworkUser(int $userId)
    {
        $params['un.userId'] = $userId;
        $results = $this->getDao()->searchWithNetworkUser(['cond' => $params]);

        return $results->getAllResultsAsArray();
    }

    public function findWithNetworkUser(int $userId): SearchResult
    {
        $params[] = 'un.userId = ' . $userId . ' OR un.userId IS NULL';
        $results = $this->getDao()->searchWithNetworkUser(['cond' => $params]);

        return $results;
    }
}
