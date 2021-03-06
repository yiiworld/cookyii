<?php
/**
 * fill.php
 * @author Revin Roman
 * @link https://rmrevin.com
 *
 * @var \yii\web\View $this
 * @var Account\forms\FillAttributesForm $FillAttributesForm
 */

use cookyii\modules\Account;
use cookyii\widgets\angular\ActiveForm;
use rmrevin\yii\fontawesome\FA;
use yii\helpers\Html;

Account\frontend\assets\FillAttributesAssetBundle::register($this);

?>

<div class="box" ng-controller="Account.FillAttributesController">
    <div class="box-logo">
        <?= Yii::t('cookyii.account', 'To complete the registration you must specify your email') ?>
    </div>

    <div class="box-body">
        <?php
        /** @var ActiveForm $form */
        $form = ActiveForm::begin([
            'model' => $FillAttributesForm,
        ]);

        echo $form->field($FillAttributesForm, 'email')
            ->textInput();

        ?>
        <div class="row">
            <div class="col-xs-12 text-center">
                <?php
                $icon = FA::icon('cog', ['ng-show' => 'in_progress', 'class' => 'wo-animate'])->spin();

                echo Html::submitButton($icon . ' ' . Yii::t('cookyii.account', 'Sign in'), [
                    'class' => 'btn btn-sm btn-primary btn-block btn-flat',
                    'ng-disabled' => 'in_progress',
                ]);
                ?>
            </div>
        </div>
        <?php
        ActiveForm::end();
        ?>
    </div>
</div>
