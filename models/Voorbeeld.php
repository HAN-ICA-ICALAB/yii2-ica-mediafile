<?php
namespace icalab\mediafile\models;

use Yii;
use yii\db\ActiveRecord;
use icalab\mediafile\models\Mediafile;
use icalab\mediafile\behaviors\ModelWithMediafileBehavior;

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
