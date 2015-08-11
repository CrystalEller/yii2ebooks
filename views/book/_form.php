<?php

use app\models\Author;
use app\models\Category;
use app\models\Format;
use app\models\Language;
use app\models\Publisher;
use app\models\Tag;
use yii\helpers\Html;
use yii\helpers\Url;


$this->registerCssFile("/css/bootstrap-select/bootstrap-select.min.css");

$this->registerJsFile('/js/bootstrap-select/bootstrap-select.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile('/js/book/form.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

$formData = Yii::$app->session['book-form'];
?>

<form id="book-form"
      class="form-horizontal"
      action="<?php echo URL::toRoute('book/create', true); ?>"
      enctype="multipart/form-data"
      method="post">
    <input type="hidden" id="formId" name="formId" value="book-form"
           data-url="<?php echo URL::toRoute('form/save', true); ?>">

    <input type="hidden" name="_csrf" value="<?= Yii::$app->request->getCsrfToken() ?>"/>

    <div class="form-group">
        <label for="title" class="col-lg-2 control-label">Title</label>

        <div class="col-lg-10">
            <input type="text" class="form-control" name="title"
                   value="<?php echo Html::encode($model->title ?: !empty($formData['title']) ? $formData['title'] : ''); ?>"
                   id="title">
        </div>
    </div>
    <div class="form-group">
        <label for="description" class="col-lg-2 control-label">Description</label>

        <div class="col-lg-10">
                <textarea class="form-control" name="description" id="description" rows="8"
                    ><?php echo nl2br(Html::encode($model->description ?: !empty($formData['description']) ? $formData['description'] : '')); ?></textarea>
        </div>
    </div>
    <div class="form-group">
        <label for="category" class="col-lg-2 control-label">Category</label>

        <div class="col-lg-8">
            <select id="category"
                    class="selectpicker form-control"
                    name="categoryId"
                    data-live-search="true">
                <?php $rows = Category::find()->all(); ?>
                <?php foreach ($rows as $row): ?>
                    <?php if (!empty($model->bookCategory)): ?>
                        <?php $selectedRow = $model->bookCategory->id === $row->id; ?>
                    <?php elseif (!empty($formData['category'])): ?>
                        <?php $selectedRow = $row->id == $formData['category'][0]; ?>
                    <?php endif; ?>
                    <option
                        value="<?php echo $row->id; ?>" <?php echo !empty($selectedRow) ? 'selected="selected"' : ''; ?>>
                        <?php echo $row->name; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-lg-2">
            <button type="button"
                    data-id="category"
                    data-url="<?php echo URL::toRoute('category/create', true); ?>"
                    data-success-message="Category %name% successfully created"
                    class="btn btn-link add-new">
                Add new
            </button>
        </div>
    </div>
    <div class="form-group">
        <label for="publisher" class="col-lg-2 control-label">Publisher</label>

        <div class="col-lg-8">
            <select id="publisher"
                    class="selectpicker form-control"
                    name="publisherId"
                    data-live-search="true">
                <?php $rows = Publisher::find()->all(); ?>
                <?php foreach ($rows as $row): ?>
                    <?php if (!empty($model->bookPublisher)): ?>
                        <?php $selectedRow = $model->bookPublisher->id === $row->id; ?>
                    <?php elseif (!empty($formData['publisher'])): ?>
                        <?php $selectedRow = $row->id == $formData['publisher'][0]; ?>
                    <?php endif; ?>
                    <option
                        value="<?php echo $row->id; ?>" <?php echo !empty($selectedRow) ? 'selected="selected"' : ''; ?>>
                        <?php echo $row->name; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-lg-2">
            <button type="button" data-id="publisher"
                    data-url="<?php echo URL::toRoute('publisher/create', true); ?>"
                    data-success-message="Publisher %name% successfully created"
                    class="btn btn-link add-new">
                Add new
            </button>
        </div>
    </div>
    <div class="form-group">
        <label for="author" class="col-lg-2 control-label">Authors</label>

        <div class="col-lg-8">
            <select id="authors"
                    class="selectpicker form-control"
                    name="authors[]"
                    data-live-search="true"
                    multiple data-selected-text-format="count>4">
                <?php
                $rows = Author::find()->all();
                $authors = !empty($model->bookAuthors) ? array_column($model->getBookAuthors()->asArray()->all(), 'id') : array();
                ?>
                <?php foreach ($rows as $row): ?>
                    <?php if (!empty($authors)): ?>
                        <?php $selectedRow = in_array($row->id, $authors); ?>
                    <?php elseif
                    (!empty($formData['authors'])
                    ): ?>
                        <?php $selectedRow = in_array($row->id, $formData['authors']); ?>
                    <?php endif; ?>
                    <option
                        value="<?php echo $row->id; ?>" <?php echo !empty($selectedRow) ? 'selected="selected"' : ''; ?>>
                        <?php echo $row->name; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-lg-2">
            <button type="button"
                    data-id="authors"
                    data-url="<?php echo URL::toRoute('author/create', true); ?>"
                    data-success-message="Author %name% successfully created"
                    class="btn btn-link add-new">
                Add new
            </button>
        </div>
    </div>
    <div class="form-group">
        <label for="tags" class="col-lg-2 control-label">Tags</label>

        <div class="col-lg-8">
            <select id="tags"
                    class="selectpicker form-control"
                    name="tags[]"
                    data-live-search="true"
                    multiple="multiple"
                    data-selected-text-format="count>10">
                <?php
                $rows = Tag::find()->all();
                $tags = !empty($model->bookTags) ? array_column($model->getBookTags()->asArray()->all(), 'id') : array();
                ?>
                <?php foreach ($rows as $row): ?>
                    <?php if (!empty($tags)): ?>
                        <?php $selectedRow = in_array($row->id, $tags); ?>
                    <?php elseif
                    (!empty($formData['tags'])
                    ): ?>
                        <?php $selectedRow = in_array($row->id, $formData['tags']); ?>
                    <?php endif; ?>
                    <option
                        value="<?php echo $row->id; ?>" <?php echo !empty($selectedRow) ? 'selected="selected"' : ''; ?>>
                        <?php echo $row->name; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-lg-2">
            <button type="button"
                    data-id="tags"
                    data-url="<?php echo URL::toRoute('tag/create', true); ?>"
                    data-success-message="Tag %name% successfully created"
                    class="btn btn-link add-new">
                Add new
            </button>
        </div>
    </div>
    <div class="form-group">
        <label for="language" class="col-lg-2 control-label">Language</label>

        <div class="col-lg-8">
            <select id="language"
                    class="selectpicker form-control input-sm"
                    name="languageId"
                    data-live-search="true">
                <?php $rows = Language::find()->all(); ?>
                <?php foreach ($rows as $row): ?>
                    <?php if (!empty($model->bookLanguage)): ?>
                        <?php $selectedRow = $model->bookLanguage->id === $row->id; ?>
                    <?php elseif (!empty($formData['language'])): ?>
                        <?php $selectedRow = $row->id == $formData['language'][0]; ?>
                    <?php endif; ?>
                    <option
                        value="<?php echo $row->id; ?>" <?php echo !empty($selectedRow) ? 'selected="selected"' : ''; ?>>
                        <?php echo $row->name; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-lg-2">
            <button type="button"
                    data-id="language"
                    data-url="<?php echo URL::toRoute('language/create', true); ?>"
                    data-success-message="Language %name% successfully created"
                    class="btn btn-link add-new">
                Add new
            </button>
        </div>
    </div>
    <div class="form-group">
        <label for="pages" class="col-lg-2 control-label">Pages</label>

        <div class="col-lg-10">
            <input type="number" class="form-control" name="pages"
                   value="<?php echo Html::encode($model->pages ?: !empty($formData['pages']) ? $formData['pages'] : ''); ?>"
                   id="pages">
        </div>
    </div>
    <div class="form-group">
        <label for="isbn" class="col-lg-2 control-label">ISBN</label>

        <div class="col-lg-10">
            <input type="text" class="form-control" name="ISBN"
                   value="<?php echo Html::encode($model->ISBN ?: !empty($formData['isbn']) ? $formData['isbn'] : ''); ?>"
                   id="isbn">
        </div>
    </div>
    <div class="form-group">
        <label for="year" class="col-lg-2 control-label">Year</label>

        <div class="col-lg-10">
            <input type="number" class="form-control" name="year"
                   value="<?php echo Html::encode($model->year ?: !empty($formData['year']) ? $formData['year'] : ''); ?>"
                   id="year">
        </div>
    </div>
    <div class="form-group">
        <label class="col-lg-2 control-label">Format</label>

        <div class="col-lg-10">
            <div class="row format-select">
                <div class="col-lg-12 hide format-pattern">
                    <span class="book-name"></span>
                    <input type='file' name="file[]" class="hide upload-book">
                    <input type='hidden' name="format[]" class="book-format">
                    <button type="button" class="btn btn-default delete-file">Delete</button>
                </div>
                <div class="col-lg-4 row">
                    <div class="col-lg-7">
                        <select class="selectpicker format form-control"
                                data-live-search="true">
                            <option class="hide empty" selected value="">--</option>
                            <?php $rows = Format::find()->all(); ?>
                            <?php foreach ($rows as $row): ?>
                                <option value="<?php echo $row->id; ?>">
                                    <?php echo $row->name; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-lg-5">
                        <button type="button"
                                data-class="format"
                                data-url="<?php echo URL::toRoute('format/create', true); ?>"
                                data-success-message="Format %name% successfully created"
                                class="btn btn-link add-new">
                            Add new format
                        </button>
                    </div>
                </div>
                <div class="col-lg-8">
                </div>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="col-lg-2">
        </div>
        <div class="col-lg-5">
            <div id="image-wrapper" class="well well-sm" style="width: 220px">
                <img src="" alt="" width="200"/>
            </div>
            <input type='file' id="upload-image" name="image" class="hide" accept=".jpg, .png, .jpeg">
            <button id="image" type="button" class="btn btn-default">Upload Title Image</button>
        </div>
        <div class="col-lg-5">
        </div>
    </div>
    <div class="form-group">
        <div class="col-lg-12 text-right">
            <button type="submit" class="btn btn-default">Submit</button>
        </div>
    </div>
</form>


<div id="add-new-popover" class="hidden">
    <input type="text" class="input-sm name">
    <button type="button" class="btn btn-default btn-sm save hide-popover">
        <span class="glyphicon glyphicon-ok"></span>
    </button>
    <button type="button" class="btn btn-default btn-sm hide-popover">
        <span class="glyphicon glyphicon-remove"></span>
    </button>
</div>

