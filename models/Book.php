<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "book".
 *
 * @property integer $id
 * @property string $title
 * @property string $description
 * @property double $raiting
 * @property string $tags
 * @property integer $publisherId
 * @property string $year
 * @property string $ISBN
 * @property integer $pages
 * @property integer $categoryId
 * @property string $created
 * @property string $views
 * @property string $downloads
 * @property integer $languageId
 *
 * @property Category $category
 * @property Language $language
 * @property Publisher $publisher
 */
class Book extends ActiveRecord
{
    public $newLinkTags;
    public $newLinkAuthors;
    public $newLinkFormats;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'book';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'categoryId', 'languageId', 'publisherId'], 'required'],

            [['description'], 'string', 'max' => 2000],
            [['title'], 'string', 'max' => 255],
            [['ISBN'], 'string', 'max' => 45],

            [['publisherId', 'pages', 'categoryId', 'languageId', 'year'], 'integer'],
            [['year'], 'number', 'max' => 9999, 'min' => 1],
            [['raiting'], 'number', 'max' => 5, 'min' => 1],

            ['publisherId', 'exist', 'targetClass' => 'app\models\Publisher', 'targetAttribute' => 'id'],
            ['categoryId', 'exist', 'targetClass' => 'app\models\Category', 'targetAttribute' => 'id'],
            ['languageId', 'exist', 'targetClass' => 'app\models\Language', 'targetAttribute' => 'id'],

            ['newLinkTags', 'exist', 'targetClass' => 'app\models\Tag', 'targetAttribute' => 'id', 'allowArray' => true],
            ['newLinkAuthors', 'exist', 'targetClass' => 'app\models\Author', 'targetAttribute' => 'id', 'allowArray' => true],
            ['newLinkFormats', 'exist', 'targetClass' => 'app\models\Format', 'targetAttribute' => 'id', 'allowArray' => true],

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'description' => 'Description',
            'raiting' => 'Raiting',
            'tags' => 'Tags',
            'publisherId' => 'Publisher ID',
            'year' => 'Year',
            'isbn' => 'Isbn',
            'pages' => 'Pages',
            'categoryId' => 'Category ID',
            'created' => 'Created',
            'views' => 'Views',
            'downloads' => 'Downloads',
            'languageId' => 'Language ID',
        ];
    }


    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $this->created = date('Y-m-d H:i:s');
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthors()
    {
        return $this->hasMany(Author::className(), ['id' => 'authorId'])
            ->viaTable('author_book', ['bookId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'categoryId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLanguage()
    {
        return $this->hasOne(Language::className(), ['id' => 'languageId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPublisher()
    {
        return $this->hasOne(Publisher::className(), ['id' => 'publisherId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFormats()
    {
        return $this->hasMany(Format::className(), ['id' => 'formatId'])
            ->viaTable('book_format', ['bookId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTags()
    {
        return $this->hasMany(Tag::className(), ['id' => 'tagId'])
            ->viaTable('book_tag', ['bookId' => 'id']);
    }
}
