<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\ImportForm;
use app\models\Products;
use app\models\ProductsDetails;
use yii\web\UploadedFile;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
//    To import file
    public function actionImportfile()
    {
        $model=new ImportForm;
        if(isset($_POST))
        {
            if(isset($_FILES['import_file']))
            {
                $import_file  = $_FILES['import_file'];
                $file_name = trim($_FILES['import_file']['name']);
                $file_size =$_FILES['import_file']['size'];
                $ext = pathinfo($file_name, PATHINFO_EXTENSION);
                $file_tmp = $_FILES['import_file']['tmp_name'];
                //echo $file_tmp;exit;
                $expensions= array("csv");
                $target="uploads/importproducts/";
                //file format checking(only csv file accepted)
                if(in_array($ext,$expensions)=== false)
                {
                    Yii::$app->session->setFlash('error', 'This file format does not support');
                }
                else
                {
                    $target_file=$target.$file_name;
                    if(is_uploaded_file($_FILES["import_file"]["tmp_name"]))
                    {
                        move_uploaded_file($_FILES["import_file"]["tmp_name"], $target_file);
                        $status=1;
                        $handle = fopen($target_file, "r");
                        $headers = fgetcsv($handle, 0, ",");

                        while (($data = fgetcsv($handle, 0, ",")) !== FALSE)
                        {
                            if(count($headers)==count($data))
                            {
                                if($data[0]!='')
                                {

                                    for ($i=1;$i<count($data);$i++)
                                    {
                                        $exitsproducts  = Products::find()->where(['product_model'=>$data[0]])->one();

                                        if(!empty($exitsproducts))
                                        {
                                            $exitsproductDetails  = ProductsDetails::find()->where(['product_id'=>$exitsproducts->id])->one();

                                            if(!empty($exitsproductDetails))
                                            {
                                                $price = isset($data[$i])?number_format((float)$data[$i], 2, '.', ''):NULL;
                                                ProductsDetails::updateAll(['price'=>$price],['product_id'=>$exitsproducts->id]);
                                            }
                                            else
                                            {
                                                $product  = new ProductsDetails;
                                                $product->product_id =isset($exitsproducts->id)? $exitsproducts->id:'';
                                                $product->price =isset($data[$i])?number_format((float)$data[$i], 2, '.', ''):NULL;
                                                $product->created_at = date('Y-m-d H:i:s');
                                                $product->status = 1;

                                                if(!$product->save())
                                                {
                                                    echo '<pre>';
                                                    print_r($product->getErrors());
                                                    exit;
                                                }
                                            }       
                                        }
                                        else
                                        {
                                           Yii::$app->session->setFlash('failed', 'Product Model does not match');
                                        }
                                    }                            
                                }
                            }
                        }
                        fclose($handle);
                        unlink($target_file);
                        Yii::$app->session->setFlash('success', 'Product Updated successfully');
                        return $this->redirect(['importfile']);
                    }
                }
            }
      
        }
       return $this->render('importproduct_form',[
        'model'=>$model,
        ]);
    }
}
