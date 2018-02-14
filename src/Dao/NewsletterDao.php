<?php

namespace App\Dao;

class NewsletterDao extends DaoAbstract
{
    protected $_tableName = 'Newsletter';
    protected $_primaryKey = 'newsletterEmail';

    protected $_formatMap = [
        'networkSlug' => Format::STRING,
    ];
}
