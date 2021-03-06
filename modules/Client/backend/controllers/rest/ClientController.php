<?php
/**
 * ClientController.php
 * @author Revin Roman
 * @link https://rmrevin.com
 */

namespace cookyii\modules\Client\backend\controllers\rest;

use cookyii\modules\Client;
use cookyii\modules\Client\resources\Client\Model as ClientModel;

/**
 * Class ClientController
 * @package cookyii\modules\Client\backend\controllers\rest
 */
class ClientController extends \cookyii\rest\Controller
{

    public $modelClass = ClientModel::class;

    /**
     * @inheritdoc
     */
    public function accessRules()
    {
        return [
            [
                'allow' => true,
                'roles' => [Client\backend\Permissions::ACCESS],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    protected function verbs()
    {
        $verbs = parent::verbs();

        $verbs['edit'] = ['POST'];
        $verbs['roles'] = ['PUT'];
        $verbs['detail'] = ['GET'];
        $verbs['activate'] = ['POST'];
        $verbs['deactivate'] = ['POST'];
        $verbs['update'] = ['PUT'];
        $verbs['restore'] = ['PATCH'];
        $verbs['create-account'] = ['POST'];
        $verbs['unlink-account'] = ['POST'];

        return $verbs;
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        $actions = parent::actions();

        $actions['index']['prepareDataProvider'] = [$this, 'prepareListDataProvider'];

        $actions['create-account'] = [
            'class' => Client\backend\controllers\rest\ClientController\CreateAccountAction::className(),
            'modelClass' => $this->modelClass,
            'checkAccess' => [$this, 'checkAccess'],
        ];

        $actions['unlink-account'] = [
            'class' => Client\backend\controllers\rest\ClientController\UnlinkAccountAction::className(),
            'modelClass' => $this->modelClass,
            'checkAccess' => [$this, 'checkAccess'],
        ];

        $actions['edit'] = [
            'class' => Client\backend\controllers\rest\ClientController\EditFormAction::className(),
            'modelClass' => $this->modelClass,
            'checkAccess' => [$this, 'checkAccess'],
        ];

        $actions['detail'] = [
            'class' => Client\backend\controllers\rest\ClientController\DetailAction::className(),
            'modelClass' => $this->modelClass,
            'checkAccess' => [$this, 'checkAccess'],
        ];

        $actions['activate'] = [
            'class' => \cookyii\rest\actions\ActivateAction::className(),
            'modelClass' => $this->modelClass,
            'checkAccess' => [$this, 'checkAccess'],
        ];

        $actions['deactivate'] = [
            'class' => \cookyii\rest\actions\DeactivateAction::className(),
            'modelClass' => $this->modelClass,
            'checkAccess' => [$this, 'checkAccess'],
        ];

        $actions['delete'] = [
            'class' => \cookyii\rest\actions\DeleteAction::className(),
            'modelClass' => $this->modelClass,
            'checkAccess' => [$this, 'checkAccess'],
        ];

        $actions['restore'] = [
            'class' => \cookyii\rest\actions\RestoreAction::className(),
            'modelClass' => $this->modelClass,
            'checkAccess' => [$this, 'checkAccess'],
        ];

        return $actions;
    }

    /**
     * @inheritdoc
     */
    protected function serializeData($data)
    {
        return parent::serializeData($data);
    }

    /**
     * @param \yii\rest\Action $action
     * @return \yii\data\ActiveDataProvider
     */
    public function prepareListDataProvider($action)
    {
        /* @var $modelClass ClientModel */
        $modelClass = $action->modelClass;

        $Query = $modelClass::find()
            ->with(['account']);

        $search = str_clean(Request()->get('search'));
        if (!empty($search)) {
            $Query->search($search);
        }

        $deleted = Request()->get('deleted');
        if ($deleted === 'false') {
            $Query->withoutDeleted();
        }

        return new \yii\data\ActiveDataProvider([
            'query' => $Query,
            'pagination' => ['pageSize' => 15],
        ]);
    }
}