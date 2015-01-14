<?php

/* @var $this yii\web\View */
/* @var $model app\modules\mediafile\Mediafiletype */
use yii\helpers\Url;
use app\modules\mediafile\MediafileModule;

$this->title = MediafileModule::t('mediafile', 'Create new media file type');

$this->params['breadcrumbs'][] = [
    'label' => MediafileModule::t('mediafile', 'Media file types'),
        'url' => Url::toRoute(['mediafiletype/index']),];
$this->params['breadcrumbs'][] = MediafileModule::t('mediafile', 'New media file type');

?>

<div class="mediafiletype-update">
<?= $this->render('_form', ['model' => $model]); ?>
</div>


