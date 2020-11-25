
<main class="ps-page--my-account">
    <div class="ps-breadcrumb">
        <div class="container">
            <ul class="breadcrumb">
                <li><a href="/">Главная</a></li>
                <li>Аккаунт</li>
            </ul>
        </div>
    </div>
    <section class="ps-section--account">
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
            <div class="row">
                <div class="col-lg-4">
                    <?php require_once( 'includes/buyermenu.php' ); ?>
                </div>

                <div class="col-lg-8">
                    <div class="ps-section__right">
                        <div class="ps-section--account-setting">
                            <div class="ps-section__header">
                                <h3>Мои реквезиты</h3>
                            </div>
                            <div class="ps-section__content">
                                <div class="row">
                                    <div class="col-md-8">
                                        <span class="bg-warning">Внимание! Изменение реквезитов возможно только запрос в тех. поддержку</span>
                                        <form action="/buyer/requis/" method="post">
                                            <div class="form-group">
                                                <label>QIWI РФ (Комиссия 1.99%)</label>
                                                <input type="text" name="qiwi" value="<?=(empty($requis['qiwi'])) ? "" : $requis['qiwi'] ?>" placeholder="Введите ваш QIWI" class="form-control">
                                            </div>

                                            <div class="form-group">
                                                <label>Яндекс.Деньги РФ (Комиссия 1.99%)</label>
                                                <input type="text"  name="yamoney" value="<?=(empty($requis['yamoney'])) ? "" : $requis['yamoney'] ?>" placeholder="Введите ваш Яндекс.Деньги" class="form-control">
                                            </div>

                                            <div class="form-group">
                                                <label>Карта VISA РФ (RUB 1.99% + 45.00р.)</label>
                                                <input type="text"  name="cardvisa" value="<?=(empty($requis['card'])) ? "" : $requis['card'] ?>"  placeholder="Введите ваш Номер карты" class="form-control">
                                            </div>

                                            <div class="form-group">
                                                <label>Карта MASTERCARD РФ (RUB 1.99% + 45.00р.)</label>
                                                <input type="text"  name="cardmaster" value="<?=(empty($requis['card'])) ? "" : $requis['card'] ?>"  placeholder="Введите ваш Номер карты" class="form-control">
                                            </div>

                                            <div class="form-group">
                                                <label>Карта MIR РФ (RUB 1.99% + 45.00р. )</label>
                                                <input type="text"  name="cardmir" value="<?=(empty($requis['card'])) ? "" : $requis['card'] ?>"  placeholder="Введите ваш Номер карты" class="form-control">
                                            </div>

                                            <div class="form-group">
                                                <label>Карта Украинского Банка (RUB 3% + 45.00р. )</label>
                                                <input type="text"  name="cardukr" value="<?=(empty($requis['card'])) ? "" : $requis['card'] ?>"  placeholder="Введите ваш Номер карты" class="form-control">
                                            </div>



                                            <button  type="submit" class="btn btn-warning"><i class="icon-checkmark mr-2"></i>СОХРАНИТЬ РЕКВИЗИТЫ</button>


                                        </form>
                                    </div>


                                </div>


                            </div>
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

