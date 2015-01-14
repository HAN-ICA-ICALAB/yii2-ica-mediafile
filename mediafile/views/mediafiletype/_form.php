<?php
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\modules\mediafile\models\Mediafiletype */

use app\modules\mediafile\MediafileModule;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

?>

<?php
$form = ActiveForm::begin([
    'id' => 'mediafiletype-form',
    'layout' => 'horizontal'
]);
echo $form->errorSummary($model);


echo $form->field($model, 'name');
echo $form->field($model, 'mimetype');
echo $form->field($model, 'extension');

if($model->isNewRecord)
{
    echo Html::submitButton(MediafileModule::t('mediafile', 'Create'), ['class' => 'btn btn-primary']);
}
else
{
    echo Html::submitButton(MediafileModule::t('mediafile', 'Update'), ['class' => 'btn btn-primary']);
}

ActiveForm::end();

