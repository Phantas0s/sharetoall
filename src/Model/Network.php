<?php
declare(strict_types=1);

namespace App\Model;

use App\Model\Network;
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
        $results['rows'] = $this->wrapAll($results->getAllResults());

        return $results;
    }

    public function findWithNetworkUser(array $params): SearchResult
    {
        $results = $this->getDao()->searchWithNetworkUser(['cond' => $params]);
        $results['rows'] = $this->wrapAll($results->getAllResults());

        return $results;
    }

    public function saveUserNetwork(array $values)
    {
        $networkUserDao = $this->createDao('UserNetwork');
        $networkUserDao->setValues($values);
        $networkUserDao->save();
    }
}
