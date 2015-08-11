<?php
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\Tag;
?>

<div class="panel panel-default">
    <div class="panel-heading">Tags</div>
    <div class="panel-body">
        <?php
        foreach (Tag::findTagWeights() as $tag => $weight) {
                $link = Url::toRoute(['book/index', 'tag' => $tag]);
                echo Html::tag('a', $tag, array(
                        'style' => "font-size:{$weight}pt",
                        'href' => $link
                    )) . "\n";
        }
        ?>
    </div>
</div>