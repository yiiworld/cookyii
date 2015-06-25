<?php
/**
 * EditItemAssetBundle.php
 * @author Revin Roman
 * @link https://rmrevin.ru
 */

namespace cookyii\modules\Feed\backend\_assets;

/**
 * Class EditItemAssetBundle
 * @package cookyii\modules\Feed\backend\_assets
 */
class EditItemAssetBundle extends \yii\web\AssetBundle
{

    public $sourcePath;

    public $css = [
        'edit.css',
    ];

    public $js = [
        'ItemResource.js',
        'ItemDetailController.js',
        'ItemEditController.js',
    ];

    public $depends = [
        'backend\_assets\AppAsset',
    ];

    public function init()
    {
        $this->sourcePath = __DIR__ . '/_sources';

        parent::init();
    }
}