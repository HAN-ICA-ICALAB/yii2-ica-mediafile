<?php
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model icalab\mediafile\models\Mediafile */

use icalab\mediafile\MediafileModule;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use icalab\mediafile\components\AttachMediafileWidget;
?>

<?php
$form = ActiveForm::begin([
    'id' => 'voorbeeld-form',
    'layout' => 'horizontal',
    'options' => ['enctype' => 'multipart/form-data'],
]);
echo $form->errorSummary($model);

echo AttachMediafileWidget::widget(['model' => $model, 'form' => $form]);

echo $form->field($model, 'waarde');

if($model->isNewRecord)
{
    echo Html::submitButton(MediafileModule::t('mediafile', 'Create'), ['class' => 'btn btn-primary']);
}
else
{
    echo Html::submitButton(MediafileModule::t('mediafile', 'Update'), ['class' => 'btn btn-primary']);
}

ActiveForm::end();

