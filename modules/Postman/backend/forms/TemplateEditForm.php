<?php
/**
 * TemplateEditForm.php
 * @author Revin Roman
 * @link https://rmrevin.com
 */

namespace cookyii\modules\Postman\backend\forms;

use cookyii\modules\Postman\resources\PostmanTemplate\Model as PostmanTemplateModel;
use yii\helpers\Json;

/**
 * Class TemplateEditForm
 * @package cookyii\modules\Postman\backend\forms
 */
class TemplateEditForm extends \cookyii\base\FormModel
{

    use \cookyii\traits\PopulateErrorsTrait;

    /** @var PostmanTemplateModel */
    public $Template;

    public $code;
    public $subject;
    public $content_text;
    public $content_html;
    public $styles;
    public $address;
    public $params;
    public $description;
    public $use_layout;

    public function init()
    {
        if (!($this->Template instanceof PostmanTemplateModel)) {
            throw new \yii\base\InvalidConfigException(\Yii::t('cookyii.postman', 'Not specified template to edit.'));
        }
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        /** @var PostmanTemplateModel $TemplateModel */
        $TemplateModel = \Yii::createObject(PostmanTemplateModel::className());

        return [
            /** type validators */
            [['code', 'subject', 'description', 'content_text', 'content_html', 'styles'], 'string'],
            [['use_layout'], 'boolean'],

            /** semantic validators */
            [['code', 'subject'], 'required'],
            [['code', 'subject', 'description', 'styles'], 'filter', 'filter' => 'str_clean'],
            [['content_text', 'content_html'], 'filter', 'filter' => 'trim'],
            [['address', 'params'], 'safe'],

            /** default values */
            [['use_layout'], 'default', 'value' => $TemplateModel::USE_LAYOUT],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'code' => \Yii::t('cookyii.postman', 'Code'),
            'subject' => \Yii::t('cookyii.postman', 'Subject'),
            'content_text' => \Yii::t('cookyii.postman', 'Plain content'),
            'content_html' => \Yii::t('cookyii.postman', 'HTML content'),
            'address' => \Yii::t('cookyii.postman', 'Address'),
            'params' => \Yii::t('cookyii.postman', 'Parameters'),
            'description' => \Yii::t('cookyii.postman', 'Description'),
            'use_layout' => \Yii::t('cookyii.postman', 'Use layout'),
        ];
    }

    /**
     * @return array
     */
    public function formAction()
    {
        return ['/postman/rest/template/edit'];
    }

    /**
     * @return bool
     */
    public function isNewTemplate()
    {
        return $this->Template->isNewRecord;
    }

    /**
     * @return bool
     */
    public function save()
    {
        $Template = $this->Template;

        $address = [];
        if (!empty($this->address) && is_array($this->address)) {
            foreach ($this->address as $addr) {
                if ($addr === null || empty($addr['email'])) {
                    continue;
                }

                $address[] = $addr;
            }
        }

        $params = [];
        if (!empty($this->params) && is_array($this->params)) {
            foreach ($this->params as $param) {
                if ($param === null || empty($param['key']) || isset($param['default'])) {
                    continue;
                }

                $params[] = $param;
            }
        }

        $Template->code = $this->code;
        $Template->subject = $this->subject;
        $Template->content_text = $this->content_text;
        $Template->content_html = $this->content_html;
        $Template->styles = $this->styles;
        $Template->address = Json::encode($address);
        $Template->params = Json::encode($params);
        $Template->description = $this->description;
        $Template->use_layout = $this->use_layout;

        $result = $Template->validate() && $Template->save();

        if ($Template->hasErrors()) {
            $this->populateErrors($Template, 'code');
        }

        $this->Template = $Template;

        return $result;
    }
}