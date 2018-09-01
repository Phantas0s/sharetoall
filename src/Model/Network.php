<?php declare(strict_types=1);

namespace App\Model;

use App\Model\Network;
use Doctrine\ActiveRecord\Search\SearchResult;

/**
 * @see https://github.com/lastzero/doctrine-active-record
 */
class Network extends ModelAbstract
{
    protected $_daoName = 'Network';

    public function findAllNetworkByUserId(int $userId): SearchResult
    {
        $results = $this->getDao()->searchAllNetworksByUserId($userId);
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

    public function invalidateUserNetwork()
    {
        $userNetworkDao = $this->createDao('UserNetwork');
        $userNetworkDao->invalidate();
    }

    public function deleteUserNetwork(int $userId, string $networkSlug)
    {
        $userNetworkDao = $this->createDao("UserNetwork");
        $userNetworkDao->deleteAll(["userId" => $userId, "networkSlug" => $networkSlug]);
    }

    /** @param array networks Network objects */
    public function mapNetworksToFrontend(SearchResult $networks):array
    {
        $networksArr = [];
        $results = $networks->getAllResults();
        foreach ($results as $network) {
            $networksArr[$network->networkSlug] = [
                "networkSlug" => $network->networkSlug,
                "networkName" => $network->networkName,
                "userId" => $network->userId,
                "networkTokenExpire" => $network->userNetworkTokenExpire,
            ];
        }

        return $networksArr;
    }
}
