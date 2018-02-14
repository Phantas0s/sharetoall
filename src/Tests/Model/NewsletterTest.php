<?php

namespace App\Tests\Model;

use TestTools\TestCase\UnitTestCase;

class NewsletterTest extends UnitTestCase
{
    public function setUp()
    {
        $this->model = $this->get('model.newsletter');
    }

    public function testExistOrSave()
    {
        $values = ['newsletterEmail' => 'lala@lala.fr'];
        $results = $this->model->existOrSave($values);

        $this->assertEquals($values, $results);
    }
}
