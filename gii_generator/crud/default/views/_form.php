<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

/* @var $model \yii\db\ActiveRecord */
$model = new $generator->modelClass();
$safeAttributes = $model->safeAttributes();
if (empty($safeAttributes)) {
    $safeAttributes = $model->attributes();
}

echo "<?php\n";
?>

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model <?= ltrim($generator->modelClass, '\\') ?> */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="box box-primary">
    <?= "<?php " ?>$form = ActiveForm::begin(); ?>
    <div class="box-header with-border">
        <h3 class="box-title">
            <?= Inflector::camel2words(StringHelper::basename($generator->modelClass)) ?> Information
        </h3>
    </div>
    <div class="box-body">
    
        <?php
        echo "<div class='row'>\n";
        $i = 0;
        foreach ($generator->getColumnNames() as $attribute) {
            if (in_array($attribute, $safeAttributes)) {
                if ($i % 2 == 0 && $i > 0) {
                    echo "        </div>\n";
                    echo "        <div class='row'>\n";
                }
                echo "            <div class='col-md-6'>\n";
                echo "                <?= " . $generator->generateActiveField($attribute) . " ?>\n";
                echo "            </div>\n";
                $i++;
            }
        }

        if ($i > 0) {
            echo "        </div>";
        }
        ?>

    </div>
    <div class="box-footer text-right">
        <?= "<?= " ?>Html::a('Cancel', ['index'], ['class' => 'btn btn-danger', 'style' => 'width: 100px;']) ?>    
        <?= "<?= " ?>Html::submitButton(<?= $generator->generateString('Save') ?>, ['class' => 'btn btn-success']) ?>
    </div>

    <?= "<?php " ?>ActiveForm::end(); ?>

</div>
