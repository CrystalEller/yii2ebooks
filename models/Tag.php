<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tag".
 *
 * @property integer $id
 * @property string $name
 * @property integer $frequency
 */
class Tag extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tag';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['frequency'], 'integer'],
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
            'frequency' => 'Frequency',
        ];
    }

    public static function findTagWeights($limit = 20)
    {
        $models = Tag::find()->orderBy(['frequency'=>SORT_DESC])->limit($limit)->all();

        $total = 0;
        foreach ($models as $model) {
            $total += $model->frequency;
        }

        $tags = array();

        if ($total > 0) {
            foreach ($models as $model) {
                if($model->frequency>0) {
                    $tags[$model->name] = 8 + (int)(16 * $model->frequency / ($total + 10));
                } else {
                    $tags[$model->name]=0;
                }
            }
            ksort($tags);
        }

        return $tags;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBookTags()
    {
        return $this->hasMany(BookTag::className(), ['tagId' => 'id']);
    }
}
