<?php
/**
 * DetailAction.php
 * @author Revin Roman
 */

namespace cookyii\modules\Feed\backend\controllers\item\rest\ItemController;

use yii\helpers\Json;

/**
 * Class DetailAction
 * @package cookyii\modules\Feed\backend\controllers\item\rest\ItemController
 */
class DetailAction extends \yii\rest\Action
{

    /**
     * @param integer $id
     * @return array
     * @throws \yii\web\NotFoundHttpException
     */
    public function run($id)
    {
        /** @var \resources\Feed\Item $model */
        $model = $this->findModel($id);

        $result = $model->attributes;

        $meta = Json::decode($model->meta);

        $result = array_merge($result, $meta);

        $result['hash'] = sha1(serialize($result));

        return $result;
    }
}