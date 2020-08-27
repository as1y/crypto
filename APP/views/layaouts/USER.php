<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">


    <?php \APP\core\base\View::getMeta()?>


    <!-- Global stylesheets -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
    <link href="/global_assets/css/icons/icomoon/styles.min.css" rel="stylesheet" type="text/css">
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="/assets/css/bootstrap_limitless.min.css" rel="stylesheet" type="text/css">
    <link href="/assets/css/layout.min.css" rel="stylesheet" type="text/css">
    <link href="/assets/css/components.min.css" rel="stylesheet" type="text/css">
    <link href="/assets/css/colors.min.css" rel="stylesheet" type="text/css">
    <!-- /global stylesheets -->

    <!-- Core JS files -->
    <script src="/global_assets/js/main/jquery.min.js"></script>
    <script src="/global_assets/js/main/bootstrap.bundle.min.js"></script>
    <script src="/global_assets/js/plugins/loaders/blockui.min.js"></script>
    <!-- /core JS files -->

    <!-- Theme JS files -->

    <?php \APP\core\base\View::getAssets("js");?>

    <script src="/assets/js/app.js"></script>


    <!-- /theme JS files -->

</head>

<body>

<!-- Main navbar -->
<div class="navbar navbar-expand-md navbar-dark">


        <a href="/" class="navbar-nav-link " >
            <b><?=APPNAME?></b>
        </a>








    <div class="d-md-none">
        <ul class="navbar-nav ml-auto">
            <li>

                <a href="/user/register/" type="button" class="btn btn-success"><i class="icon-user-plus mr-2"></i> Регистрация</a>
                <a href="/user/" type="button" class="btn btn-success"><i class="icon-circle-right2 mr-2"></i> Войти</a>

            </li>
        </ul>

    </div>




    <div class="collapse navbar-collapse" id="navbar-mobile">


        <ul class="navbar-nav ml-auto">
        <li>

            <?php if(empty($_SESSION['ulogin'])):?>
            <a href="/user/register/" type="button" class="btn bg-teal-400"><i class="icon-user-plus mr-2"></i> Регистрация</a>
            <a href="/user/" type="button" class="btn btn-success"><i class="icon-circle-right2 mr-2"></i> Войти</a>
            <?php endif;?>


        </li>
        </ul>



    </div>



</div>
<!-- /main navbar -->





<!-- Page content -->
<div class="page-content">

    <!-- Main content -->
    <div class="content-wrapper">

        <?php if(isset($_SESSION['errors'])): ?>
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert"><span>×</span></button>
                <span class="font-weight-semibold">Ошибка!</span> <br><?=$_SESSION['errors']; unset($_SESSION['errors']);?>
            </div>
        <?php endif;?>

        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert"><span>×</span></button>
                <span class="font-weight-semibold">Успех!</span> <?=$_SESSION['success']; unset($_SESSION['success']);?>
            </div>

        <?php endif;?>

        <!-- Content area -->
        <div class="content d-flex justify-content-center align-items-center">


            <?=$content?>


        </div>
        <!-- /content area -->




    </div>
    <!-- /main content -->

</div>
<!-- /page content -->

<!-- Footer -->
<div class="navbar navbar-expand-lg navbar-dark">
    <div class="text-center d-lg-none w-100">
        <button type="button" class="navbar-toggler dropdown-toggle" data-toggle="collapse" data-target="#navbar-footer">
            <i class="icon-unfold mr-2"></i>
            Подвал сайта
        </button>
    </div>

    <div class="navbar-collapse collapse" id="navbar-footer">
			<span class="navbar-text">
				&copy; 2020 <b><a href="/panel/"><?=APPNAME?></a></b> - Биржа удаленных операторов на телефоне.
			</span>

        <ul class="navbar-nav ml-lg-auto">
            <li class="nav-item"><a href="mailto: <?=CONFIG['BASEMAIL']['email']?>" class="navbar-nav-link" target="_blank"><i class="icon-mail-read mr-2"></i> <?=CONFIG['BASEMAIL']['email']?></a></li>


        </ul>
    </div>
</div>
<!-- /footer -->


</body>
</html>








