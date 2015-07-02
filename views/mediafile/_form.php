<?php
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model icalab\mediafile\models\Mediafile */

use icalab\mediafile\MediafileModule;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

?>

<?php
$form = ActiveForm::begin([
    'id' => 'mediafile-form',
    'layout' => 'horizontal',
    'options' => ['enctype' => 'multipart/form-data'],
]);
echo $form->errorSummary($model);

if(file_exists($model->filePath))
{
    echo '<img src="'
        . Url::to(['view', 'id' => $model->primaryKey])
        . '" />';

}
echo $form->field($model, 'file')->fileInput();
echo $form->field($model, 'title');
echo $form->field($model, 'notes')->textarea(['rows' => 3]);

if($model->isNewRecord)
{
    echo Html::submitButton(MediafileModule::t('mediafile', 'Create'), ['class' => 'btn btn-primary']);
}
else
{
    echo Html::submitButton(MediafileModule::t('mediafile', 'Update'), ['class' => 'btn btn-primary']);
}

ActiveForm::end();

