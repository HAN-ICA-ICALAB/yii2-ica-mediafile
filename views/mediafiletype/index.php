<?php
/* @var $this yii\web\View */
/* @var $dataProvider dataProvider */
//namespace app\modules\mediafile\views\mediafiletype;
//use Yii;
use yii\widgets\ListView;
use yii\helpers\Html;
use yii\helpers\Url;
use app\modules\mediafile\MediafileModule;
$this->title = MediafileModule::t('mediafile', 'File types');
?>

<div class="mediafiletype-index">
<?php
echo ListView::widget([
    'dataProvider' => $dataProvider,
    'itemView' => function($model, $key, $index, $widget)
    {
        return 
            Html::a($model->name,
                Url::toRoute(['mediafiletype/update', 'id' => $model->primaryKey]));
    }

    ]);
?>
    
</div>
