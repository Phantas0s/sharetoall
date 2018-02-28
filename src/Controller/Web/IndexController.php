<?php
declare(strict_types=1);

namespace App\Controller\Web;

use App\Exception\FormInvalidException;
use App\Service\Session;
use InputValidation\Form;
use Symfony\Component\HttpFoundation\Request;

class IndexController
{
    public function indexAction(Request $request)
    {
        if ($request->query->get("reset")) {
            return ['realm' => 'web', 'resetToken' => $request->query->get('reset')];
        }

        return ['realm' => 'web'];
    }

    public function aboutAction()
    {
        return ['realm' => 'web'];
    }
}
