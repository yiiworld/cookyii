<?php
/**
 * AccountEditForm.php
 * @author Revin Roman
 */

namespace cookyii\modules\Account\backend\forms;

use yii\helpers\ArrayHelper;

/**
 * Class AccountEditForm
 * @package cookyii\modules\Account\backend\forms
 */
class AccountEditForm extends \yii\base\Model
{

    use \common\traits\ActiveRecord\PopulateErrorsTrait;

    /** @var \resources\Account */
    public $Account;

    public $name;
    public $email;
    public $new_password;
    public $new_password_app;

    public function init()
    {
        if (!($this->Account instanceof \resources\Account)) {
            throw new \yii\base\InvalidConfigException(\Yii::t('account', 'Not specified user to edit.'));
        }
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            /** type validators */
            [['name', 'email', 'new_password', 'new_password_app'], 'string'],

            /** semantic validators */
            [['name', 'email'], 'required'],
            [['email'], 'email'],
            [['name', 'email'], 'filter', 'filter' => 'str_clean'],
            [['new_password_app'], 'compare', 'compareAttribute' => 'new_password', 'operator' => '==='],

            /** default values */
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => \Yii::t('account', 'Username'),
            'email' => \Yii::t('account', 'Email'),
            'new_password' => \Yii::t('account', 'New password'),
            'new_password_app' => \Yii::t('account', 'Approve new password'),
        ];
    }

    /**
     * @return array
     */
    public function formAction()
    {
        return ['/account/rest/edit'];
    }

    /**
     * @return bool
     */
    public function isNewAccount()
    {
        return $this->Account->isNewRecord;
    }

    /**
     * @return bool
     */
    public function save()
    {
        $Account = $this->Account;

        $Account->name = $this->name;
        $Account->email = $this->email;

        if ($Account->isNewRecord) {
            $Account->activated = \resources\Account::NOT_ACTIVATED;
            $Account->deleted = \resources\Account::NOT_DELETED;
        }

        if (!empty($this->new_password)) {
            $Account->password = $this->new_password;
        }

        $result = $Account->validate() && $Account->save();

        if ($Account->hasErrors()) {
            $this->populateErrors($Account, 'name');
        }

        if (AuthManager() instanceof \yii\rbac\DbManager) {
            AuthManager()->invalidateCache();
        }

        $this->Account = $Account;

        return $result;
    }

    /**
     * @return array
     */
    public static function getRoleValues()
    {
        return ArrayHelper::map(AuthManager()->getRoles(), 'name', 'description');
    }

    /**
     * @return array
     */
    public static function getPermissionValues()
    {
        return ArrayHelper::map(AuthManager()->getPermissions(), 'name', 'description');
    }

    /**
     * @return array
     */
    public static function getGroupedPermissionValues()
    {
        $permissions = static::getPermissionValues();

        $result = [
            'items' => [],
            'children' => [],
        ];

        if (!empty($permissions)) {
            foreach ($permissions as $permission => $description) {
                if (empty($permission)) {
                    continue;
                }

                $part = explode('.', $permission);

                if (empty($part) || count($part) < 1) {
                    continue;
                }

                $count = count($part);

                if ($count === 1) {
                    if (!in_array($permission, $result['items'], true)) {
                        $result['items'][$permission] = $description;
                    }
                } else {
                    $g1 = sprintf('%s.*', $part[0]);

                    if (!isset($result['children'][$g1])) {
                        $result['children'][$g1] = [
                            'items' => [],
                        ];
                    }

                    if (!in_array($permission, $result['children'][$g1]['items'], true)) {
                        $result['children'][$g1]['items'][$permission] = $description;
                    }
                }
            }
        }

        return $result;
    }
}