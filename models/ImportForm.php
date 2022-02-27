<?php

namespace app\models;
use Yii;
use yii\base\Model;

class ImportForm extends Model
{
    public $file;

    public function rules()
    {
        return [
            // username and password are both required
            [['file'], 'required'],
            [['file'], 'string', 'max' => 255],

        ];
    }  
}
