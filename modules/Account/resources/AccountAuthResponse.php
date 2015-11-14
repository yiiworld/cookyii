<?php
/**
 * AccountAuthResponse.php
 * @author Revin Roman
 * @link https://rmrevin.com
 */

namespace cookyii\modules\Account\resources;

/**
 * Class AccountAuthResponse
 * @package cookyii\modules\Account\resources
 *
 * @property integer $id
 * @property integer $received_at
 * @property string $client
 * @property string $response
 * @property string $result
 * @property string $user_ip
 */
class AccountAuthResponse extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            /** type validators */
            [['received_at', 'user_ip'], 'integer'],
            [['client', 'response', 'result'], 'string'],

            /** semantic validators */
            [['client', 'response', 'result'], 'required'],

            /** default values */
            [['received_at'], 'default', 'value' => time()],
            [['user_ip'], 'default', 'value' => ip2long(Request()->userIP)],
        ];
    }

    /**
     * @return \cookyii\modules\Account\resources\queries\AccountAuthResponseQuery
     */
    public static function find()
    {
        return \Yii::createObject(
            \cookyii\modules\Account\resources\queries\AccountAuthResponseQuery::className(), [
                get_called_class(),
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%account_auth_response}}';
    }
}