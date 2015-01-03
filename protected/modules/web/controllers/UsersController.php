<?php

class UsersController extends Controller {
    /**
     * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
     * using two-column layout. See 'protected/views/layouts/column2.php'.
     */

    /**
     * @return array action filters
     */
    public function filters() {
        return array(
            'accessControl', // perform access control for CRUD operations
            'postOnly + delete', // we only allow deletion via POST request
                /* "https +array('changePass','setNewPass')", */
                /* "http + array(activate','register','updateProfile','updateProfile','forgot','productReview','customerHistory','orderDetail','print','customerDetail')" */
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules() {
        return array(
            array('allow', // allow all users to perform 'index' and 'view' actions
                'actions' => array('login', 'register', 'activate', 'setNewPass', 'forgot'),
                'users' => array('?'),
            ),
            array('allow', // allow authenticated user to perform 'create' and 'update' actions
                'actions' => array(
                    'updateProfile', 'ChangePass',),
                'users' => array('@'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }

    /**
     * 
     * @param type $url
     * @param type $pluggin : this pluggin var. usually should be pluggin name sent in curl call from the client server where pluggin is installed
     */
    public function actionRegister($url = '', $pluggin = '') {
        $this->layout = "//layouts/column2";
        $model = new Users;

        $model->_url = $url;
        $model->_pluggin = $pluggin;

        /* when user is being registered from the remote client system or curl call or to purchase pluggin plan */
        if (!is_numeric($pluggin) && !empty($pluggin)) {
            $pluggin_id = Pluggin::model()->getPlugginId($pluggin);
            $model->_pluggin = $pluggin_id;
        }

        if (isset($_POST['Users'])) {

            $model->attributes = $_POST['Users'];

            $model->activation_key = sha1(mt_rand(10000, 99999) . time() . $model->email);
            $activation_url = $this->createUrl('/web/users/activate', array('key' => $model->activation_key));

            if ($model->save()) {

                //Sending email part - For activation

                $subject = "Your Activation Link";
                $message = "Please click this below to activate your account <br /><br />" . $this->createAbsoluteUrl('/web/users/activate', array('key' => $model->activation_key, 'id' => $model->id)) . "<br /><br /> Thanks you ";

                $email['FromName'] = Yii::app()->params['systemName'];
                $email['From'] = Yii::app()->params['adminEmail'];
                $email['To'] = $model->email;
                $email['Subject'] = "Your Activation Link";
                $body = "You are now registered on " . Yii::app()->name . ", please validate your email <br/>" . $message;
                //$body.=" going to this url: <br /> \n" . $model->getActivationUrl();
                $email['Body'] = $body;
                $email['Body'] = $this->renderPartial('//users/_email_template', array('email' => $email), true, false);

                $this->sendEmail2($email);
                Yii::app()->user->setFlash('registration', 'Thank you for Registration...Please activate your account by visiting your email account.');
                PlugginSiteInfo::model()->updateSitinfoUser($model->id, $model->_url, $model->_pluggin);
                $this->redirect($this->createUrl('/web/default/index'));  ///take him to login page....
            }
        }

        $this->render('//users/register', array(
            'model' => $model,
        ));
    }

    /**
     * This will be responsible for logging in the user to the system
     */
    public function actionLogin($url = '', $pluggin = '') {
        $this->layout = "//layouts/column2";

        $model = new LoginForm;

        // if it is ajax validation request
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'login-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }

        // collect user input data
        if (isset($_POST['LoginForm'])) {
            $model->attributes = $_POST['LoginForm'];


            $pluggin = Pluggin::model()->getPlugginId($pluggin);
            $model->_url = $url;
            $model->_pluggin = $pluggin;


            // validate user input and redirect to the previous page if valid
            if ($model->validate() && $model->login()) {


                PlugginSiteInfo::model()->updateSitinfoUser(Yii::app()->user->id, $model->_url, $model->_pluggin);


                if (!empty(Yii::app()->user->returnUrl)) {
                    $this->redirect($this->createUrl("/web/default/index"));
                }
                $this->redirect(Yii::app()->user->returnUrl);
            }
        }

        $this->render("//users/_login", array("login_model" => $model));
    }

    public function actionActivate() {
        $this->layout = "//layouts/column2";
        //Yii::app()->user->SiteSessions;
        $id = $_GET['id'];
        $activation_key = $_GET['key'];
        $criteria = new CDbCriteria;
        $criteria->select = '*';
        $criteria->condition = "id='" . $id . "'";
        $obj = Users::model()->find($criteria);

        if (!empty($obj)) {
            if ($obj->is_active == '1') {
                //already activated
                Yii::app()->user->setFlash('login', 'Your account is already activated. Please try login or if you have missed your login information then go to forgot password section. Thank You');
                $this->redirect($this->createUrl('/web/users/login'));
            } else if ($obj->activation_key != $activation_key) {
                Yii::app()->user->setFlash('error', 'Your activation key not registered. Please resend activation key and activate your account. Thank You');
                $this->redirect($this->createUrl('/web/users/login'));
            }

            Yii::app()->user->setFlash('success', 'Thank You ! Your account has been activated....Now Please Login');

            $modelUsers = new Users();
            $modelUsers->updateByPk($id, array('is_active' => '1'));
            $this->redirect($this->createUrl('/web/users/login'));
        } else {
            Yii::app()->user->setFlash('error', 'User does not exist. Please signup and get activation link.');
            $this->redirect($this->createUrl('/web/users/login'));
        }
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer $id the ID of the model to be loaded
     * @return Users the loaded model
     * @throws CHttpException
     */
    public function loadModel($id) {
        $model = Users::model()->findByPk($id);
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }

    /**
     * 
     * @param type $id
     */
    public function actionUpdateProfile() {
        $this->layout = "//layouts/column2";
        $model = UpdateUser::model()->findByPk(Yii::app()->user->id);

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if (isset($_POST['UpdateUser'])) {
            $model->attributes = $_POST['UpdateUser'];
            if ($model->save()) {
                Yii::app()->user->setFlash('success', 'Your profile has been updated');
                $this->redirect(array('/web/users/updateProfile',));
            }
        }

        $this->render('//users/update_profile', array(
            'model' => $model,
        ));
    }

    public function actionForgot() {
        $this->layout = "//layouts/column2";
        //Yii::app()->user->SiteSessions;
        if (isset($_POST['Users'])) {
            $record = Users::model()->find(array(
                'select' => '*',
                'condition' => "email='" . $_POST['Users']['email'] . "'"
                    )
            );
            if ($record === null) {
                Yii::app()->user->setFlash('incorrect_email', 'Email does not exists...Please try correct email address');
            } else {

                $pass_new = substr(str_shuffle(str_repeat("0123456789abcdefghijklmnopqrstuvwxyz", 7)), 0, 9);
                $body = "Your New Password : " . $pass_new;
                $email['FromName'] = Yii::app()->params['systemName'];
                $email['From'] = Yii::app()->params->adminEmail;
                $email['To'] = $record->email;
                $email['Subject'] = "Your New Password";
                $email['Body'] = $body;

                $email['Body'] = $this->renderPartial('//users/_email_template', array('email' => $email), true, false);
                $this->sendEmail2($email);

                $id = $record->id;

                $modelUsers = new Users;
                $pass_new = md5($pass_new);
                if ($modelUsers->updateByPk($id, array('password' => "$pass_new"))) {

                    Yii::app()->user->setFlash('password_reset', 'Your passowrd has been sent to your Email.Please get your new password form your email account');
                }
            }
        }

        $this->render('//users/forgot_password', array('model' => Users::model()));
    }

    /*
     * 
     * @return method for change users password.
     */

    public function actionChangePass() {
        $this->layout = "//layouts/column2";

        $model = new ChangePassword;
        if (Yii::app()->user->id) {
            if (isset($_POST['ChangePassword'])) {
                $model->attributes = $_POST['ChangePassword'];
                if ($model->validate()) {
                    $user = $model->_model;
                    $user->password = $model->password;
                    $user->save(false);
                    Yii::app()->user->setFlash("success", "Your Password change Successfully");
                    $this->redirect($this->createUrl('/web/users/changePass'));
                }
            }
            $this->render('//users/change_password', array('model' => $model));
        }
    }

    /**
     * Set New Password here
     * from when special users request 
     * is made here
     */
    public function actionSetNewPass() {
        $this->layout = "//layouts/column2";
        //Yii::app()->user->SiteSessions;
        //Yii::app()->controller->layout = '//layouts/main';

        if (isset($_GET['key']) && $_GET['id']) {
            $model = new NewPassword();

            if (isset($_POST['NewPassword'])) {
                $model->attributes = $_POST['NewPassword'];
                if ($model->validate()) {
                    if ($model->updatePassword($_GET['id'])) {
                        /*
                         * here we will add sending email module to inform user for password change..
                         */
                        $this->redirect($this->createUrl('/web/users/login'));
                    }
                }
            }

            $this->render('//users/new_password', array('model' => $model));
        }
    }

    /**
     * Performs the AJAX validation.
     * @param Users $model the model to be validated
     */
    protected function performAjaxValidation($model) {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'users-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

}
