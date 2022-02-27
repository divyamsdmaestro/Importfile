<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "products".
 *
 * @property int $id
 * @property string|null $product_model
 * @property string|null $product_name
 * @property string|null $created_at
 * @property int $status 1->active,2->inactive,0->delete
 *
 * @property ProductsDetails[] $productsDetails
 */
class Products extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'products';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_at','id'], 'safe'],
            [['status'], 'integer'],
            [['product_model', 'product_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'product_model' => 'Product Model',
            'product_name' => 'Product Name',
            'created_at' => 'Created At',
            'status' => 'Status',
        ];
    }

    /**
     * Gets query for [[ProductsDetails]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProductsDetails()
    {
        return $this->hasMany(ProductsDetails::className(), ['product_id' => 'id']);
    }
}
