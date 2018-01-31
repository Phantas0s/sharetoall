<?php
declare(strict_types=1);

namespace App\Controller\Web;

use App\Exception\FormInvalidException;
use InputValidation\Form;
use Symfony\Component\HttpFoundation\Request;

class IndexController
{
    protected $session;

    public function indexAction(Request $request)
    {
        if ($request->query->get("reset")) {
            return ['realm' => 'web', 'resetToken' => $request->query->get('reset')];
        }

        return ['realm' => 'web'];
    }
}
