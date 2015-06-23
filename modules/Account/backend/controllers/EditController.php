<?php
/**
 * EditController.php
 * @author Revin Roman
 */

namespace cookyii\modules\Account\backend\controllers;

use cookyii\modules\Account;

/**
 * Class EditController
 * @package cookyii\modules\Account\backend\controllers
 */
class EditController extends Account\backend\components\Controller
{

    /**
     * @inheritdoc
     */
    protected function accessRules()
    {
        return [
            [
                'allow' => true,
                'actions' => ['index'],
                'roles' => [Account\backend\Permissions::ACCESS],
            ],
        ];
    }

    /**
     * @return string
     */
    public function actionIndex()
    {
        $AccountEditForm = new Account\backend\forms\AccountEditForm([
            'Account' => new \resources\Account(),
        ]);

        return $this->render('index', [
            'AccountEditForm' => $AccountEditForm,
        ]);
    }
}