<?php

class DefaultController extends Controller {

    /**
     * for mem cache
     * @var type
     */
    public $cache_key, $_slug;

    /**
     * Declares class-based actions.
     */
    public function actions() {
        return array(
            // captcha action renders the CAPTCHA image displayed on the contact page
            'captcha' => array(
                'class' => 'CCaptchaAction',
                'backColor' => 0xFFFFFF,
                'foreColor' => 0x454545,
                'height' => 40,
            ),
            // page action renders "static" pages stored under 'protected/views/site/pages'
            // They can be accessed via: index.php?r=site/page&view=FileName
            'page' => array(
                'class' => 'CViewAction',
            ),
        );
    }

    /**
     * cache settings
     * @return type
     */
//    public function filters() {
//
//        return array(
//            array(
//                'DTOutputCache -quranReader',
//                'duration' => Yii::app()->params['cacheTime'],
//                'varyByRoute' => true,
//                'is_cached_allowd' => true,
//           ),
//        );
//    }

    /**
     * redirect site to for having www  lazmi
     * @param type $action
     * @return type
     */
    public function beforeAction($action) {

        return parent::beforeAction($action);
    }

    /**
     * home page
     */
    public function actionIndex() {
        $this->layout = "//layouts/frontend";


        $this->render('//default/index');
    }

    /**
     *  contact us page
     */
    public function actionContactUs() {
        $this->layout = "//layouts/frontend";
        //SeoMeta::model()->configureSeoFields("contact-us", $this);
        //SeoOpenGraph::model()->configureSeoOpenGraphFields("contact-us", $this);
        $model = new ContactForm();
        if (isset($_POST['ContactForm'])) {
            $model->attributes = $_POST['ContactForm'];
            if ($model->save()) {

                $email['To'] = Yii::app()->params['adminEmail'];
                $email['FromName'] = $model->name;
                $email['From'] = $model->email;
                $email['Subject'] = $model->subject . ' From Mr/Mrs: ' . $model->name;
                $email['Body'] = $model->body;
                $email['Body'] = $this->renderPartial('//common/_email_template', array('email' => $email));
                //die($email['Body']);
                $this->sendEmail2($email);

                Yii::app()->user->setFlash('contact', 'Thank you ! for your feedback ');
                $this->redirect($this->createUrl("/web/default/contactUs"));
            }
        }
        $this->render('//default/contactus', array("slug" => "", "model" => $model));
    }

}
