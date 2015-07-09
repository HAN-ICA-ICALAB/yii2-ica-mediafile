<?php

/* @var $this yii\web\View */
/* @var $model icalab\mediafile\Mediafiletype */
use yii\helpers\Url;
use icalab\mediafile\MediafileModule;

$this->title = Yii::t('mediafile', 'Create new media file type');

$this->params['breadcrumbs'][] = [
    'label' => Yii::t('mediafile', 'Media file types'),
        'url' => Url::toRoute(['mediafiletype/index']),];
$this->params['breadcrumbs'][] = Yii::t('mediafile', 'New media file type');

?>

<div class="mediafiletype-update">
<?= $this->render('_form', ['model' => $model]); ?>
</div>


