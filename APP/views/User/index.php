<div class="ps-page--my-account">
    <div class="ps-breadcrumb">
        <div class="container">
            <ul class="breadcrumb">
                <li><a href="/">Главная</a></li>
                <li>Аккаунт</li>
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


                    <form class="ps-form--account ps-tab-root" action="/user/" method="post">
                        <div class="ps-tabs">
                            <div class="ps-tab active" id="sign-in">
                                <div class="ps-form__content">
                                    <h5>Войти в аккаунт</h5>
                                    <div class="form-group">
                                        <input class="form-control" type="email" name="email"  placeholder="E-mail" required>
                                    </div>
                                    <div class="form-group form-forgot">
                                        <input class="form-control" type="password" name="password" placeholder="Пароль"><a href="">Забыли?</a>
                                    </div>
                                    <div class="form-group">
                                        <div class="ps-checkbox">
                                            <input class="form-control" type="checkbox" checked id="remember-me" name="remember-me">
                                            <label for="remember-me"  >Запомнить меня</label>
                                        </div>
                                    </div>
                                    <div class="form-group submit">
                                        <button class="ps-btn ps-btn--fullwidth">Вход</button>
                                    </div>
                                </div>
                            </div>

                            </form>

                        </div>
                    </form>
                </div>


                <div class="ps-section__right">

                    <figure class="ps-section__desc">
                        <h2>Нет аккаунта?</h2>
                        <p>MartFury Buyer Protection has you covered from click to delivery. Sign up or sign in and you will be able to:</p>
                        <p><a href="/user/register/" class="ps-btn ps-btn--fullwidth">Регистрация</a></p>
                    </figure>

                </div>
            </div>
        </div>
    </div>
</div>