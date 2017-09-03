<?php

namespace App\Controller\Rest;

class MessageController extends EntityControllerAbstract
{
    protected $modelName = 'Message';
    protected $createFormName = 'Message/CreateForm';

    public function postAction(Request $request)
    {
        $form = $this->createForm($this->createFormName);

        $form->setDefinedWritableValues($request->request->all())->validate();

        if ($form->hasErrors()) {
            throw new FormInvalidException($form->getFirstError());
        }

        $this->model->save($form->getValues());

        $message = $request->request->get('messageContent');

        $this->sendMessageToActivatedAccounts();
    }
}
