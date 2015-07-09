<?php

/* @var $this yii\web\View */
/* @var $model icalab\mediafile\models\Mediafile */
use yii\helpers\Url;
use icalab\mediafile\MediafileModule;

$this->title = Yii::t('mediafile', 'Create new media file');

$this->params['breadcrumbs'][] = [
    'label' => Yii::t('mediafile', 'Media files'),
        'url' => Url::toRoute(['mediafile/index']),];
$this->params['breadcrumbs'][] = Yii::t('mediafile', 'New media file');

?>

<div class="mediafile-update">
<?= $this->render('_form', ['model' => $model]); ?>
</div>


