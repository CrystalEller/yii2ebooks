<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "format".
 *
 * @property integer $id
 * @property string $name
 *
 * @property BookFormat[] $bookFormats
 */
class Format extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'format';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 45]
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
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBookFormats()
    {
        return $this->hasMany(BookFormat::className(), ['formatId' => 'id']);
    }
}
