<?php declare(strict_types=1);

namespace App\Controller\Rest;

use App\Model\Newsletter;
use Symfony\Component\HttpFoundation\Request;

class NewsletterController
{
    /** @var Newsletter */
    private $newsletterModel;

    public function __construct(Newsletter $newsletterModel)
    {
        $this->newsletterModel = $newsletterModel;
    }

    public function postAction(Request $request): string
    {
        $email = $request->request->get('email');
        $this->newsletterModel->existOrSave(['newsletterEmail' => $email]);

        return $email;
    }
}
