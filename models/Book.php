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
 * @property integer $publisherId
 * @property integer $year
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
    public $tags;
    public $authors;
    public $formats;
    public $imageFile;
    public $bookFile;

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
            ['year', 'number', 'max' => 9999, 'min' => 1],
            ['raiting', 'number', 'max' => 5, 'min' => 1],

            ['imageFile', 'file',
                'extensions' => 'jpeg, jpg, png',
                'maxSize' => 2 * pow(10, 6)],
            ['bookFile', 'file',
                'extensions' => array_column(Format::find()->asArray()->all(), 'name'),
                'maxSize' => 150 * pow(10, 6)],

            ['publisherId', 'exist', 'targetClass' => 'app\models\Publisher', 'targetAttribute' => 'id'],
            ['categoryId', 'exist', 'targetClass' => 'app\models\Category', 'targetAttribute' => 'id'],
            ['languageId', 'exist', 'targetClass' => 'app\models\Language', 'targetAttribute' => 'id'],

            ['tags', 'exist', 'targetClass' => 'app\models\Tag', 'targetAttribute' => 'id', 'allowArray' => true],
            ['authors', 'exist', 'targetClass' => 'app\models\Author', 'targetAttribute' => 'id', 'allowArray' => true],
            ['formats', 'exist', 'targetClass' => 'app\models\Format', 'targetAttribute' => 'id', 'allowArray' => true],

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

    public function beforeDelete()
    {
        if (parent::beforeDelete()) {
            Tag::updateAllCounters(
                ['frequency' => -1],
                ['id' => $this->getBookTags()->select(['id'])->asArray()->all()]
            );

            return true;
        } else {
            return false;
        }
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

    public function afterSave($insert, $changedAttributes)
    {
        if (!$insert) {
            Yii::$app->db->createCommand()
                ->delete('author_book', ['bookId' => $this->id])
                ->execute();
            Yii::$app->db->createCommand()
                ->delete('book_format', ['bookId' => $this->id])
                ->execute();
            Yii::$app->db->createCommand()
                ->delete('book_tag', ['bookId' => $this->id])
                ->execute();
        }

        if (!empty($this->authors)) {
            $authorInsert = [];
            foreach ($this->authors as $author) {
                array_push($authorInsert, [$author, $this->id]);
            }

            Yii::$app->db->createCommand()
                ->batchInsert('author_book', ['authorId', 'bookId'], $authorInsert)
                ->execute();
        }

        if (!empty($this->formats)) {
            $formatInsert = [];
            foreach ($this->formats as $format) {
                array_push($formatInsert, [$format, $this->id]);
            }

            Yii::$app->db->createCommand()
                ->batchInsert('book_format', ['formatId', 'bookId'], $formatInsert)
                ->execute();
        }

        if (!empty($this->tags)) {
            $tagInsert = [];
            foreach ($this->tags as $tag) {
                array_push($tagInsert, [$tag, $this->id]);
            }

            Yii::$app->db->createCommand()
                ->batchInsert('book_tag', ['tagId', 'bookId'], $tagInsert)
                ->execute();

            Tag::updateAllCounters(['frequency' => 1], ['id' => array_map('intval', $this->tags)]);
        }

        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBookAuthors()
    {
        return $this->hasMany(Author::className(), ['id' => 'authorId'])
            ->viaTable('author_book', ['bookId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBookCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'categoryId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBookLanguage()
    {
        return $this->hasOne(Language::className(), ['id' => 'languageId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBookPublisher()
    {
        return $this->hasOne(Publisher::className(), ['id' => 'publisherId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBookFormats()
    {
        return $this->hasMany(Format::className(), ['id' => 'formatId'])
            ->viaTable('book_format', ['bookId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBookTags()
    {
        return $this->hasMany(Tag::className(), ['id' => 'tagId'])
            ->viaTable('book_tag', ['bookId' => 'id']);
    }
}
