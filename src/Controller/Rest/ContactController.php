<?php
declare(strict_types=1);

namespace App\Controller\Rest;

use App\Exception\FormInvalidException;
use App\Form\Contact\CreateForm;
use App\Service\Mail;
use Symfony\Component\HttpFoundation\Request;

class ContactController
{
    /** @var CreateForm */
    private $form;

    /** @var Mail */
    private $mail;

    public function __construct(CreateForm $form, Mail $mail)
    {
        $this->form = $form;
        $this->mail = $mail;
    }

    public function postAction(Request $request): array
    {
        $this->form->setDefinedWritableValues($request->request->get("form"))->validate();

        if ($this->form->hasErrors()) {
            throw new FormInvalidException($this->form->getFirstError());
        }

        $values = $this->form->getValues();
        $this->mail->contact(
            $values['email'],
            $values['message']
        );

        return $this->form->getValues();
    }
}
