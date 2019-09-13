<?php

namespace modules\sproing\toolkit\controllers;

use Craft;
use craft\web\Controller;

class EmployeePortalController extends Controller
{
    protected $allowAnonymous =  true;

    public function actionUpdateReadBulletins()
    {
        $userSession = Craft::$app->getUser();
        $request = Craft::$app->getRequest();

        $user = $userSession->getIdentity();
        $user->setFieldValue('readBulletins', array_merge($user->readBulletins->ids(), $request->getBodyParam('fields', 'readBulletins')));

        Craft::$app->getElements()->saveElement($user);

        return $this->redirectToPostedUrl($user, '');
    }
}
