<div class="navbar navbar-inverse navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container">
            <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </a>

            <!-- Be sure to leave the brand out there if you want it shown -->
            <a class="brand" href="javascript:void(0)">Plugin Server 
                <small>
                    <?php
                    echo CHtml::image(Yii::app()->theme->baseUrl . "/images/logo.jpg");
                    ?>
                </small>
            </a>

            <div class="nav-collapse">
                <?php
                $items = array(
                    array('label' => 'Home', 'url' => array('/web/default/index')),
                    array('label' => 'My Pluggins', 'url' => array('/web/userPluggin/index',), 'visible' => !Yii::app()->user->isGuest),
                    array('label' => 'Login', 'url' => array('/web/users/login'), 'visible' => Yii::app()->user->isGuest),
                    array('label' => 'Register', 'url' => array('/web/users/register'), 'visible' => Yii::app()->user->isGuest),
                    array('label' => 'Forget Password', 'url' => array('/web/users/forgot'), 'visible' => Yii::app()->user->isGuest),
                    array('label' => 'My Account <span class="caret"></span>', 'url' => '#', 'itemOptions' => array('class' => 'dropdown', 'tabindex' => "-1"), 'linkOptions' => array('class' => 'dropdown-toggle', 'data-toggle' => "dropdown"),
                        'items' => array(
                            array('label' => 'Update Profile', 'url' => array('/web/users/updateProfile')),
                            array('label' => 'Change Password', 'url' => array('/web/users/changePass')),
                            array('label' => 'Logout (' . Yii::app()->user->name . ')', 'url' => array('/site/logout'), 'visible' => !Yii::app()->user->isGuest),
                        ), 'visible' => !Yii::app()->user->isGuest)
                );
                if (strstr(Yii::app()->request->hostInfo, "localhost")) {
                    $extra_items = array(array('label' => 'Graphs & Charts',
                            'url' => array('/site/page', 'view' => 'graphs')),
                        array('label' => 'Forms', 'url' => array('/site/page', 'view' => 'forms')),
                        array('label' => 'Tables', 'url' => array('/site/page', 'view' => 'tables')),
                        array('label' => 'Interface', 'url' => array('/site/page', 'view' => 'interface')),
                        array('label' => 'Typography', 'url' => array('/site/page', 'view' => 'typography')),
                        /* array('label'=>'Gii generated', 'url'=>array('customer/index')), */
                        array('label' => 'My Account <span class="caret"></span>', 'url' => '#', 'itemOptions' => array('class' => 'dropdown', 'tabindex' => "-1"), 'linkOptions' => array('class' => 'dropdown-toggle', 'data-toggle' => "dropdown"),
                            'items' => array(
                                array('label' => 'My Messages <span class="badge badge-warning pull-right">26</span>', 'url' => '#'),
                                array('label' => 'My Tasks <span class="badge badge-important pull-right">112</span>', 'url' => '#'),
                                array('label' => 'My Invoices <span class="badge badge-info pull-right">12</span>', 'url' => '#'),
                                array('label' => 'Separated link', 'url' => '#'),
                                array('label' => 'One more separated link', 'url' => '#'),
                    )));
                    $items = array_merge($items, $extra_items);
                }
                $this->widget('zii.widgets.CMenu', array(
                    'htmlOptions' => array('class' => 'pull-right nav'),
                    'submenuHtmlOptions' => array('class' => 'dropdown-menu'),
                    'itemCssClass' => 'item-test',
                    'encodeLabel' => false,
                    'items' => $items
                ));
                ?>
            </div>
        </div>
    </div>
</div>

<div class="subnav navbar navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container">

            <div class="style-switcher pull-left">
                <a href="javascript:chooseStyle('none', 60)" checked="checked"><span class="style" style="background-color:#0088CC;"></span></a>
                <a href="javascript:chooseStyle('style2', 60)"><span class="style" style="background-color:#7c5706;"></span></a>
                <a href="javascript:chooseStyle('style3', 60)"><span class="style" style="background-color:#468847;"></span></a>
                <a href="javascript:chooseStyle('style4', 60)"><span class="style" style="background-color:#4e4e4e;"></span></a>
                <a href="javascript:chooseStyle('style5', 60)"><span class="style" style="background-color:#d85515;"></span></a>
                <a href="javascript:chooseStyle('style6', 60)"><span class="style" style="background-color:#a00a69;"></span></a>
                <a href="javascript:chooseStyle('style7', 60)"><span class="style" style="background-color:#a30c22;"></span></a>
            </div>
            <form class="navbar-search pull-right" action="">

                <input type="text" class="search-query span2" placeholder="Search">

            </form>
        </div><!-- container -->
    </div><!-- navbar-inner -->
</div><!-- subnav -->