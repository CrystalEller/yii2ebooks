<?php
use app\models\Category;
use yii\helpers\Url;
use app\components\widget\TagCloud;

?>

<?php $this->beginContent('@app/views/layouts/main.php'); ?>
    <div class="row">
        <div class="col-lg-3">
            <div class="panel panel-default">
                <div class="panel-heading">Categories</div>
                <ul class="list-group">
                    <?php $categories = Category::find()->all(); ?>
                    <?php foreach ($categories as $category): ?>
                        <li class="list-group-item">
                            <a href="<?php Url::toRoute(['book/index', 'category' => $category->id])?>">
                                <?php echo $category->name; ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php echo TagCloud::widget(['maxTags' => Yii::$app->params['tagCloudCount']]) ?>
        </div>
        <div class="col-lg-9">
            <?php echo $content; ?>
        </div>
    </div>
<?php $this->endContent(); ?>