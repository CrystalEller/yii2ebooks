<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "publisher".
 *
 * @property integer $id
 * @property string $name
 * @property string $desctiption
 *
 * @property Book[] $books
 */
class Publisher extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'publisher';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'desctiption'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'desctiption' => 'Desctiption',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBooks()
    {
        return $this->hasMany(Book::className(), ['publisherId' => 'id']);
    }
}
