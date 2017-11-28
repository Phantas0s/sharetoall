<?php

namespace App\Tests\Service\Api\OAuth1;

use App\Service\Api\OAuth1\QueryBuilder;
use TestTools\TestCase\UnitTestCase;

class QueryBuilderTest extends UnitTestCase
{
    public function setUp()
    {
        $this->queryBuilder = new QueryBuilder();
    }

    public function testCreateUrl()
    {
        $params =  [
            'params1' => 'value1',
            'params2' => 'value2'
        ];

        $url = $this->queryBuilder->createUrl('http://localhost', $params);
        $this->assertEquals('http://localhost?params1=value1&params2=value2', $url);
    }
}
