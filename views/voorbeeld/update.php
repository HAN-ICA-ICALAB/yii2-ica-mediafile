<?php

/* @var $this yii\web\View */
/* @var $model app\modules\mediafile\models\Mediafile */
use yii\helpers\Url;
use app\modules\mediafile\MediafileModule;

$this->title = MediafileModule::t('mediafile', 'Update example');

$this->params['breadcrumbs'][] = [
    'label' => MediafileModule::t('mediafile', 'Examples'),
        'url' => Url::toRoute(['voorbeeld/index']),];
$this->params['breadcrumbs'][] = $model->waarde;
?>

<div class="voorbeeld-update">
<?= $this->render('_form', ['model' => $model]); ?>
</div>


