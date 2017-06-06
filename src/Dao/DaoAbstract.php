<?php

namespace App\Dao;

use App\Exception\DeletedException;
use Doctrine\ActiveRecord\Dao\EntityDao;
use Doctrine\ActiveRecord\Search\SearchResult;

/**
 * @see https://github.com/lastzero/doctrine-active-record
 */
abstract class DaoAbstract extends EntityDao
{
    /**
     * @var string
     */
    protected $_softDelete = false;
    protected $_softDeleteColumnName = 'deleted';
    /**
     * Returns true if this DAO implenent a datatime when the object is deleted
     */
    public function hasSoftDelete(): bool
    {
        return ($this->_softDelete === true);
    }

    public function getSoftDeleteColumn()
    {
        return $this->{$this->_softDeleteColumnName};
    }

    private function setSoftDeleteTimestamp(string $value)
    {
        $this->{$this->_softDeleteColumnName} = $value;
    }

    public function delete()
    {
        if ($this->hasSoftDelete() === false) {
            return parent::delete();
        }

        $now = $this->getDateTimeInstance();
        $this->setSoftDeleteTimestamp($now->format(Format::DATETIME));
        $this->update();
    }

    public function find($id)
    {
        parent::find($id);

        if ($this->hasSoftDelete() === true && $this->getSoftDeleteColumn() !== null) {
            $this->_data = [];
            throw new DeletedException('The result was deleted');
        }

        return $this;
    }

    public function findAll(array $cond = [], bool $wrapResult = true): array
    {
        if ($this->hasSoftDelete() === true) {
            $cond = array_merge($cond, [$this->_softDeleteColumnName => null]);
        }

        return parent::findAll($cond, $wrapResult);
    }

    public function findList(
        string $colName,
        string $order = '',
        string $where = '',
        string $indexName = ''
    ): array {
        if ($this->hasSoftDelete() === true) {
            $where .= " AND {$this->_softDeleteColumnName} IS NULL";
        }

        return parent::findList(
            $colName,
            $order,
            $where,
            $indexName
        );
    }

    public function search(array $params): SearchResult
    {
        if (!isset($params['cond'])) {
            $params['cond'] = [];
        }

        // Optional SQL filter table alias canonization
        if (empty($params['table_alias'])) {
            $params['table_alias'] = $this->getDefaultTableAlias(isset($params['table']) ? $params['table'] : $this->getTableName());
        }

        if ($this->hasSoftDelete()) {
            if ($this->extractValueFromArray($params['cond'], 'deleted')) {
                $params['cond'][] = $params['table_alias'] . '. ' . $this->_softDeleteColumnName . ' IS NOT NULL';
            } else {
                $params['cond'][] = $params['table_alias'] . '. ' . $this->_softDeleteColumnName . ' IS NULL';
            }
        }

        return parent::search($params);
    }
}
