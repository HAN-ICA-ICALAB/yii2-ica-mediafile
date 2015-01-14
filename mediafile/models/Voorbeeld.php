<?php
namespace app\modules\mediafile\models;

use Yii;
use yii\db\ActiveRecord;
use app\modules\mediafile\models\Mediafile;
use app\modules\mediafile\behaviors\ModelWithMediafileBehavior;

/**
 * @property integer $id
 * @property string $waarde
 */
class Voorbeeld extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%voorbeeld}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            ModelWithMediafileBehavior::className(),
            ];
    }
    
}
