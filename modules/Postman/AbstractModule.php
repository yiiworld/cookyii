<?php
/**
 * AbstractModule.php
 * @author Revin Roman
 * @link https://rmrevin.ru
 */

namespace cookyii\modules\Postman;

/**
 * Class AbstractModule
 * @package cookyii\modules\Postman
 */
abstract class AbstractModule extends \yii\base\Module
{

    /** @var string|null */
    public $subjectPrefix;

    /** @var string|null */
    public $subjectSuffix;
}