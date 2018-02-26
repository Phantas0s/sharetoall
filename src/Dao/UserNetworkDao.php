<?php declare(strict_types=1);

namespace App\Dao;

use Doctrine\DBAL\Driver\PDOStatement;

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

    public function invalidate(array $params = []): PDOStatement
    {
        $db = $this->getDb();

        $now = (new \DateTime())->format("Y-m-d H:i:s");

        $queryBuilder = $db->createQueryBuilder();
        $queryBuilder->delete('UserNetwork')
            ->where("userNetworkTokenExpire IS NOT NULL")
            ->andWhere("userNetworkTokenExpire < " . $db->quote($now));

        $result = $db->executeQuery($queryBuilder);
        return $result;
    }
}
