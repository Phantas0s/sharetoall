<?php declare(strict_types=1);

namespace App\Controller\Rest;

use App\Exception\FormInvalidException;
use App\Form\FormAbstract;
use App\Model\Newsletter;
use Symfony\Component\HttpFoundation\Request;

class NewsletterController
{
    /** @var Newsletter */
    private $newsletterModel;

    /** @var FormAbstract */
    private $newsletterForm;

    public function __construct(
        Newsletter $newsletterModel,
        FormAbstract $newsletterForm
    ) {
        $this->newsletterModel = $newsletterModel;
        $this->newsletterForm = $newsletterForm;
    }

    public function postAction(Request $request): string
    {
        $email = $request->request->get('email');

        $this->newsletterForm->setDefinedWritableValues(['newsletterEmail' => $email]);
        $this->newsletterForm->validate();

        if ($this->newsletterForm->hasErrors()) {
            throw new FormInvalidException($this->newsletterForm->getFirstError());
        }

        $this->newsletterModel->existOrSave(['newsletterEmail' => $email]);

        return $email;
    }
}
