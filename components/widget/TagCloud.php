<?php

namespace app\components\widget;


use yii\base\Widget;

class TagCloud extends Widget
{
    public $maxTags;

    public function init()
    {
        parent::init();
        if(empty($this->maxTags)){
            $this->maxTags= 10;
        }
    }

    public function run()
    {
        return $this->render('@widget/view/tag-cloud');
    }
}