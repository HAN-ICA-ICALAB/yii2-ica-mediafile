<?php

/* @var $this yii\web\View */
/* @var $model icalab\mediafile\models\Mediafile */
use yii\helpers\Url;
use icalab\mediafile\MediafileModule;

$this->title = MediafileModule::t('mediafile', 'Update example');

$this->params['breadcrumbs'][] = [
    'label' => MediafileModule::t('mediafile', 'Examples'),
        'url' => Url::toRoute(['voorbeeld/index']),];
$this->params['breadcrumbs'][] = $model->waarde;
?>

<div class="voorbeeld-update">
<?= $this->render('_form', ['model' => $model]); ?>
</div>


