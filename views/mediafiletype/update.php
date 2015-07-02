<?php

/* @var $this yii\web\View */
/* @var $model icalab\mediafile\Mediafiletype */
use yii\helpers\Url;
use icalab\mediafile\MediafileModule;

$this->title = MediafileModule::t('mediafile', 'Update media file type');

$this->params['breadcrumbs'][] = [
    'label' => MediafileModule::t('mediafile', 'Media file types'),
        'url' => Url::toRoute(['mediafiletype/index']),];
$this->params['breadcrumbs'][] = $model->name;
?>

<div class="mediafiletype-update">
<?= $this->render('_form', ['model' => $model]); ?>
</div>


