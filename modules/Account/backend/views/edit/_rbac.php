<?php
/**
 * _rbac.php
 * @author Revin Roman
 * @link https://rmrevin.com
 *
 * @var yii\web\View $this
 * @var Account\backend\forms\AccountEditForm $AccountEditForm
 */

use cookyii\modules\Account;
use yii\helpers\Html;

?>

    <div class="box rbac" ng-controller="AccountRolesController">
        <div class="box-header">
            <h3 class="box-title"><?= Yii::t('cookyii.account', 'Roles') ?></h3>
        </div>

        <div class="box-body">
            <?php
            foreach ($AccountEditForm::getRoleValues() as $role => $label) {
                $options = [
                    'ng-change' => 'saveRoles()',
                    'ng-model' => sprintf('data.roles.%s', $role),
                    'value' => $role,
                ];

                if ($role === \common\Roles::USER) {
                    $options['disabled'] = true;
                }

                echo Html::tag('md-checkbox', $label, $options);
            }
            ?>
        </div>
    </div>

<?php
