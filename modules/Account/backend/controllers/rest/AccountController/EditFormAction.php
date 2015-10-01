<?php
/**
 * EditFormAction.php
 * @author Revin Roman
 * @link https://rmrevin.com
 */

namespace cookyii\modules\Account\backend\controllers\rest\AccountController;

use cookyii\modules\Account;

/**
 * Class EditFormAction
 * @package cookyii\modules\Account\backend\controllers\rest\AccountController
 */
class EditFormAction extends \cookyii\rest\Action
{

    /**
     * @return array
     */
    public function run()
    {
        $result = [
            'result' => false,
            'message' => \Yii::t('account', 'Unknown error'),
        ];

        $account_id = (int)Request()->post('account_id');

        /** @var $modelClass \cookyii\modules\Account\resources\Account */
        $modelClass = $this->modelClass;

        $Account = null;

        if ($account_id > 0) {
            $Account = $modelClass::find()
                ->byId($account_id)
                ->one();
        }

        if (empty($Account)) {
            $Account = new $modelClass;
        }

        $AccountEditForm = new Account\backend\forms\AccountEditForm(['Account' => $Account]);

        $AccountEditForm->load(Request()->post())
        && $AccountEditForm->validate()
        && $AccountEditForm->save();

        if ($AccountEditForm->hasErrors()) {
            $result = [
                'result' => false,
                'message' => \Yii::t('account', 'When executing a query the error occurred'),
                'errors' => $AccountEditForm->getFirstErrors(),
            ];
        } else {
            $result = [
                'result' => true,
                'message' => \Yii::t('account', 'Account successfully saved'),
                'account_id' => $Account->id,
            ];
        }

        return $result;
    }
}