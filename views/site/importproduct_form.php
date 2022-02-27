
<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use app\models\ProductsDetails;
?>
<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
 <div class="row">
    <div class="col-sm-12">
        <h4 class="font-medium text-uppercase mb-4">Import Product File</h4>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <p class="help-block">Fields with <span class="required">*</span> are required.</p>
                        <div class="row">
                            <div class="col-md-6 form-group">
                               <?= $form->field($model, 'file')->fileInput(['class'=>'form-control','name'=>'import_file']) ?>
                            </div>
                        </div>
                        <?= Html::submitButton('Submit', ['class' => 'btn btn-primary btn-inline btn-rounded']) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
<!--product Details-->
<h5>Product Details</h5>
    <?php
        $productsDetails  = ProductsDetails::find()->where(['status'=>1])->all();
        if(!empty($productsDetails) && count($productsDetails)> 0)
           {
    ?>
        <table>
            <tr>
              <th>S.No</th>
              <th>Product Name</th>
              <th>Product Model</th>
              <th>Price</th>
            </tr>
    <?php
            $i=1;
            foreach ($productsDetails as $products) 
            {
    ?>
            <tr>
                <td><?php echo $i++; ?></td>
                <td><?php echo isset($products->product_id)?$products->product->product_name:'--' ?></td>
                <td><?php echo isset($products->product_id)?$products->product->product_model:'--' ?></td>
                <td><?php echo isset($products->price)?$products->price:'--' ?></td>
            </tr>
    <?php
                
            }
    ?>
       
    </table>
    <?php } else { ?>
        <p>No results found!</p>
    <?php } ?>
