<?php
declare(strict_types=1);

namespace App\Controller\Rest;

use App\Exception\FormInvalidException;
use App\Exception\UnauthorizedException;
use App\Model\ModelFactory;
use App\Service\Session;
use App\Form\FormFactory;
use InputValidation\Form;
use Symfony\Component\HttpFoundation\Request;

/**
 * @see https://github.com/symlex/symlex#rest
 */
class EntityControllerAbstract
{
    protected $modelName = '';
    protected $searchFormName = '';
    protected $createFormName = '';
    protected $editFormName = '';

    protected $session;
    protected $modelFactory;
    protected $formFactory;
    protected $model;

    public function __construct(
        Session $session,
        ModelFactory $modelFactory,
        FormFactory $formFactory
    ) {
        $this->session = $session;
        $this->modelFactory = $modelFactory;
        $this->formFactory = $formFactory;
        $this->model = $this->createModel($this->modelName);

        if (!$this->session->isUser()) {
            throw new UnauthorizedException('Please login or sign up to continue');
        }
    }

    protected function createForm(string $name)
    {
        $params = array(
            'session' => $this->session
        );

        return $this->formFactory->create($name, $params);
    }

    /**
     * @param $name
     * @return \App\Model\ModelAbstract
     */
    protected function createModel(string $name)
    {
        return $this->modelFactory->create($name);
    }

    public function cgetAction(Request $request)
    {
        $form = $this->createForm($this->searchFormName)->setDefinedWritableValues($request->query->all())->validate();

        return $this->search($form, $request);
    }

    protected function search(Form $form, Request $request)
    {
        if ($form->hasErrors()) {
            throw new FormInvalidException($form->getFirstError());
        }

        $options = array(
            'count' => (int)$request->query->get('count', 50),
            'offset' => (int)$request->query->get('offset', 0),
            'order' => (string)$request->query->get('order', '')
        );

        return $this->model->search($form->getValues(), $options);
    }

    public function getAction(string $id, Request $request)
    {
        return $this->model->find($id)->getValues();
    }

    public function coptionsAction()
    {
        if (empty($this->searchFormName)) {
            return null;
        }

        $form = $this->createForm($this->searchFormName);

        return $form->getAsArray();
    }

}
