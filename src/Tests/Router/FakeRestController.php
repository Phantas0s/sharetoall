<?php

namespace App\Tests\Router;

use Symfony\Component\HttpFoundation\Request;

class FakeRestController
{
    public $actionName;
    public $request;

    public function cgetAction(Request $request)
    {
        $this->actionName = 'cgetAction';
        $this->request = $request;

        return array('actionName' => $this->actionName, 'request' => $request);
    }

    public function getAction($id, Request $request)
    {
        $this->actionName = 'getAction';
        $this->request = $request;

        return array('actionName' => $this->actionName, 'id' => $id, 'request' => $request);
    }

    public function optionsCommentAction($id, $commentId, Request $request)
    {
        $this->actionName = 'optionsCommentAction';
        $this->request = $request;

        return array('actionName' => $this->actionName, 'id' => $id, 'commentId' => $commentId, 'request' => $request);
    }
}