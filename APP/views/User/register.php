<div class="ps-page--my-account">
    <div class="ps-breadcrumb">
        <div class="container">
            <ul class="breadcrumb">
                <li><a href="/">Главная</a></li>
                <li>Регистрация</li>
            </ul>
        </div>
    </div>
    <div class="ps-my-account-2">
        <div class="container">

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


            <div class="ps-section__wrapper">
                <div class="ps-section__left">


                    <div class="ps-tabs">


                        <form class="ps-form--account ps-tab-root" action="/user/register" method="post">
                            <div class="ps-tab active" id="sign-in">
                                <div class="ps-form__content">
                                    <h5>Регистрация</h5>
                                    <div class="form-group">
                                        <input class="form-control" type="email" name="email"  placeholder="E-mail" required>
                                    </div>
                                    <div class="form-group">
                                        <input class="form-control" type="password" name="password" placeholder="Пароль">
                                    </div>
                                    <div class="form-group">
                                        <input class="form-control" type="password" name="password2" placeholder="Повторите пароль">
                                    </div>


                                    <div class="form-group submit">
                                        <button class="ps-btn ps-btn--fullwidth">Регистрация</button>
                                    </div>
                                </div>


                            </div>

                        </form>




                    </div>
                    </form>
                </div>





                <div class="ps-section__right">
                    <div class="ps-section__coupon"><span>$25</span>
                        <aside>
                            <h5>A small gift for your first purchase</h5>
                            <p>Martfury give $25 as a small gift for your first purchase. Welcome to Martfury!</p>
                        </aside>
                    </div>
                    <br>
                    <figure class="ps-section__desc">
                        <p>MartFury Buyer Protection has you covered from click to delivery. Sign up or sign in and you will be able to:</p>
                        <ul class="ps-list">
                            <li><i class="icon-credit-card"></i><span>SPEED YOUR WAY THROUGH CHECKOUT</span></li>
                            <li><i class="icon-clipboard-check"></i><span>TRACK YOUR ORDERS EASILY</span></li>
                            <li><i class="icon-bag2"></i><span>KEEP A RECORD OF ALL YOUR PURCHASES</span></li>
                        </ul>
                    </figure>

                </div>
            </div>
        </div>
    </div>
</div>