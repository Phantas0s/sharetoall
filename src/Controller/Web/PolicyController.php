<?php
declare(strict_types=1);

namespace App\Controller\Web;

use Symfony\Component\HttpFoundation\Request;

class PolicyController
{
    public function indexAction()
    {
        return [
            'realm' => 'web'
        ];
    }

    public function termsAction()
    {
        return [
            'realm' => 'web'
        ];
    }
}
