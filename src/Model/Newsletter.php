<?php declare(strict_types=1);

namespace App\Model;

use App\Model\ModelAbstract;

class Newsletter extends ModelAbstract
{
    protected $_daoName = 'Newsletter';

    public function existOrSave(array $values): array
    {
        if (!$this->getDao()->exists($values)) {
            $this->save($values);
        }

        return $values;
    }
}
