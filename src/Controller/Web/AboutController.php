<?php
declare(strict_types=1);

namespace App\Controller\Web;

use Symfony\Component\HttpFoundation\Request;

class AboutController
{
    public function indexAction(Request $request)
    {
        return ['realm' => 'web'];
    }
}
