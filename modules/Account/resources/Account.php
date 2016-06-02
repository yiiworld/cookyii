<?php
/**
 * Account.php
 * @author Revin Roman
 * @link https://rmrevin.com
 */

namespace cookyii\modules\Account\resources;

use cookyii\helpers\ApiAttribute;
use yii\helpers\ArrayHelper;

/**
 * Class Account
 * @package cookyii\modules\Account\resources
 *
 * @property integer $id
 * @property string $name
 * @property string $email
 * @property string $avatar
 * @property integer $gender
 * @property integer $timezone
 * @property string $password_hash
 * @property string $token
 * @property string $auth_key
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $deleted_at
 * @property integer $activated_at
 *
 * @property \cookyii\modules\Account\resources\AccountProperty[] $properties
 * @property \cookyii\modules\Account\resources\AccountAlert[] $alerts
 *
 * @property \cookyii\modules\Account\resources\helpers\AccountPresent $presentHelper
 * @property \cookyii\modules\Account\resources\helpers\AccountNotification $notificationHelper
 */
class Account extends \cookyii\db\ActiveRecord implements \yii\web\IdentityInterface, \cookyii\interfaces\AccountInterface
{

    use \cookyii\modules\Account\resources\traits\AccountSocialTrait,
        \cookyii\db\traits\ActivationTrait,
        \cookyii\db\traits\SoftDeleteTrait;

    /**
     * @var string
     */
    public $password;

    /**
     * @var string
     */
    public $presentHelperClass = 'cookyii\\modules\\Account\\resources\\helpers\\AccountPresent';

    /**
     * @var string
     */
    public $notificationHelperClass = 'cookyii\\modules\\Account\\resources\\helpers\\AccountNotification';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            \cookyii\behaviors\TimestampBehavior::className(),
        ];
    }

    /**
     * Register event handlers
     */
    protected function registerEventHandlers()
    {
        $this->on(static::EVENT_BEFORE_INSERT, [$this, 'appendDataBeforeInsert']);
        $this->on(static::EVENT_BEFORE_UPDATE, [$this, 'updatePasswordHashBeforeUpdate']);
    }

    /**
     * @inheritdoc
     */
    public function fields()
    {
        $fields = parent::fields();

        unset(
            $fields['password_hash'], $fields['token'], $fields['auth_key'],
            $fields['created_at'], $fields['updated_at'], $fields['activated_at'], $fields['deleted_at']
        );

        $fields['avatar'] = function (self $Model) {
            return $Model->presentHelper->avatar;
        };

        $fields['deleted'] = [$this, 'isDeleted'];
        $fields['activated'] = [$this, 'isActivated'];

        return $fields;
    }

    /**
     * @inheritdoc
     */
    public function extraFields()
    {
        $fields = parent::extraFields();

        $fields['roles'] = function (Account $Model) {
            $result = [];

            $roles = AuthManager()->getRolesByUser($Model->id);

            foreach ($roles as $role => $conf) {
                $result[$role] = true;
            }

            $result[\common\Roles::USER] = true;

            return $result;
        };

        $fields['permissions'] = function (Account $Model) {
            $result = [];

            $permissions = AuthManager()->getPermissionsByUser($Model->id);

            foreach ($permissions as $permission => $conf) {
                $result[$permission] = true;
            }

            return $result;
        };

        $fields['properties'] = function (Account $Model) {
            return $Model->properties();
        };

        $fields['alerts'] = function (self $Model) {
            $Alerts = $this->alerts;

            return empty($Alerts) ? [] : ArrayHelper::getColumn($Alerts, function (AccountAlert $Model) {
                return $Model->toArray();
            });
        };

        ApiAttribute::datetimeFormat($fields, 'created_at');
        ApiAttribute::datetimeFormat($fields, 'updated_at');
        ApiAttribute::datetimeFormat($fields, 'activated_at');
        ApiAttribute::datetimeFormat($fields, 'deleted_at');

        return $fields;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            /** type validators */
            [['name', 'avatar', 'password', 'password_hash'], 'string'],
            [['gender', 'timezone', 'created_at', 'updated_at', 'activated_at', 'deleted_at'], 'integer'],

            /** semantic validators */
            [['email'], 'email'],
            [['email'], 'unique', 'filter' => $this->isNewRecord ? null : ['not', ['id' => $this->id]]],
            [['name', 'email', 'avatar'], 'filter', 'filter' => 'str_clean'],
            [['gender'], 'in', 'range' => [static::MALE, static::FEMALE]],

            /** default values */
            [['gender'], 'default', 'value' => static::MALE],
        ];
    }

    /**
     * @return bool|string
     */
    public function isAvailable()
    {
        $result = true;

        if (empty($this->activated_at)) {
            $result = 'not-activated';
        } elseif (!empty($this->deleted_at)) {
            $result = 'deleted';
        }

        return $result;
    }

    /**
     * @return bool
     */
    public function isMale()
    {
        return $this->gender === static::MALE;
    }

    /**
     * @return bool
     */
    public function isFemale()
    {
        return $this->gender === static::FEMALE;
    }

    /** @var array */
    private $_access = [];

    /**
     * @param string $permissionName
     * @param array $params
     * @param boolean $allowCaching
     * @return boolean
     */
    public function can($permissionName, $params = [], $allowCaching = true)
    {
        if ($allowCaching && empty($params) && isset($this->_access[$permissionName])) {
            return $this->_access[$permissionName];
        }
        $access = AuthManager()->checkAccess($this->id, $permissionName, $params);
        if ($allowCaching && empty($params)) {
            $this->_access[$permissionName] = $access;
        }

        return $access;
    }

    /**
     * @return string
     */
    public function getEncryptKey()
    {
        return sha1($this->created_at . $this->auth_key . $this->email);
    }

    /**
     * @return \cookyii\modules\Account\resources\helpers\AccountPresent
     * @throws \yii\base\InvalidConfigException
     */
    public function getPresentHelper()
    {
        return $this->getHelper($this->presentHelperClass);
    }

    /**
     * @return \cookyii\modules\Account\resources\helpers\AccountNotification
     * @throws \yii\base\InvalidConfigException
     */
    public function getNotificationHelper()
    {
        return $this->getHelper($this->notificationHelperClass);
    }

    /**
     * @param bool $save
     * @return string
     */
    public function refreshToken($save = true)
    {
        $token = Security()->generateRandomString();

        $this->token = $token;

        $this->validate() && ($save && $this->save());

        return $token;
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::find()
            ->byId($id)
            ->one();
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::find()
            ->byToken($token)
            ->one();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Security()->validatePassword($password, $this->password_hash);
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        $result = [];

        $roles = static::getAllRoles();
        $Assignments = AuthManager()->getAssignments($this->id);

        foreach (array_keys($Assignments) as $role) {
            $result[$role] = $roles[$role];
        }

        unset($result['user']);

        return $result;
    }

    private $_properties = null;

    /**
     * @param bool $reload
     * @return array
     */
    public function properties($reload = false)
    {
        if ($this->_properties === null || $reload === true) {
            $this->_properties = [];

            $Properties = $this->properties;
            if (!empty($Properties)) {
                foreach ($Properties as $Property) {
                    $this->_properties[] = [
                        'key' => $Property->key,
                        'value' => $Property->value,
                    ];
                }
            }
        }

        return $this->_properties;
    }

    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function property($key, $default = null)
    {
        $result = $default;

        $Properties = $this->properties;
        if (!empty($Properties)) {
            foreach ($Properties as $Property) {
                if ($key === $Property->key) {
                    $result = $Property->value;
                }
            }
        }

        return $result;
    }

    /**
     * @return \cookyii\modules\Account\resources\queries\AccountQuery
     */
    public function getProperties()
    {
        /** @var \cookyii\modules\Account\resources\AccountProperty $AccountPropertyModel */
        $AccountPropertyModel = \Yii::createObject(\cookyii\modules\Account\resources\AccountProperty::className());

        return $this->hasMany($AccountPropertyModel::className(), ['account_id' => 'id']);
    }

    /**
     * @return \cookyii\modules\Account\resources\queries\AccountAlertQuery
     */
    public function getAlerts()
    {
        /** @var \cookyii\modules\Account\resources\AccountAlert $AccountAlertModel */
        $AccountAlertModel = \Yii::createObject(\cookyii\modules\Account\resources\AccountAlert::className());

        /** @var \cookyii\modules\Account\resources\queries\AccountAlertQuery $Query */
        $Query = $this->hasMany($AccountAlertModel::className(), ['account_id' => 'id']);

        return $Query
            ->withoutDeleted();
    }

    /**
     * @return \cookyii\modules\Account\resources\queries\AccountQuery
     */
    public static function find()
    {
        return \Yii::createObject(
            \cookyii\modules\Account\resources\queries\AccountQuery::className(),
            [get_called_class()]
        );
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%account}}';
    }

    /**
     * @return array
     */
    public static function getAllRoles()
    {
        return ArrayHelper::map(AuthManager()->getRoles(), 'name', 'description');
    }

    /**
     * @return array
     */
    public static function getAllPermissions()
    {
        return ArrayHelper::map(AuthManager()->getPermissions(), 'name', 'description');
    }

    /**
     * @return array
     */
    public static function getGenderValues()
    {
        return [
            static::MALE => \Yii::t('cookyii.account', 'Male'),
            static::FEMALE => \Yii::t('cookyii.account', 'Female'),
        ];
    }

    /**
     * @param \yii\base\ModelEvent $Event
     * @throws \yii\base\Exception
     */
    public function appendDataBeforeInsert(\yii\base\ModelEvent $Event)
    {
        /** @var static $Model */
        $Model = $Event->sender;

        $Model->password_hash = empty($this->password) ? null : Security()->generatePasswordHash($this->password);
        $Model->timezone = isset($_COOKIE['timezone']) && !empty($_COOKIE['timezone']) ? $_COOKIE['timezone'] : 0;
        $Model->auth_key = Security()->generateRandomString();
        $Model->token = Security()->generateRandomString();
    }

    /**
     * @param \yii\base\ModelEvent $Event
     * @throws \yii\base\Exception
     */
    public function updatePasswordHashBeforeUpdate(\yii\base\ModelEvent $Event)
    {
        /** @var static $Model */
        $Model = $Event->sender;

        if (!empty($this->password)) {
            $Model->password_hash = Security()->generatePasswordHash($this->password);
        }
    }

    const MALE = 1;
    const FEMALE = 2;
}
