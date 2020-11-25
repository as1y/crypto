
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
                                <h3>Вывод кэшбека</h3>
                            </div>
                            <div class="ps-section__content">
                                <div class="row">
                                    <div class="col-md-8">
                                        <form action="/buyer/payout" method="post">
                                            К выводу доступно <b><?=\APP\core\base\Model::getBal()?></b> рублей<br>
                                            <span class="bg-info">Выплаты производятся в понедельник. Срок вывода может занимать до 72х часов.</span>
                                            <hr>

                                            <div class="form-group">
                                                <label>СУММА <span class="text-danger">*</span></label>
                                                <input type="text" name="summa" placeholder="Сумма на вывод" class="form-control">
                                            </div>

                                            <div class="form-group">
                                                <label>Способ вывода: <span class="text-danger">*</span> </label>
                                                <select data-placeholder="Выберите способ вывода" name="sposob" class="form-control form-control-select2 required" data-fouc>

                                                    <?php

                                                    foreach ($requis as $key=>$val){

                                                        if (empty($val)) continue;

                                                        if ($key == "qiwi") $name = "QIWI";
                                                        if ($key == "yamoney") $name = "Яндекс.Деньги";
                                                        if ($key == "cardvisa") $name = "Карта банка VISA";
                                                        if ($key == "cardmaster") $name = "Карта банка MASTERCARD";
                                                        if ($key == "cardmir") $name = "Карта банка MIR";
                                                        if ($key == "cardukr") $name = "Карта Украинского банка";
                                                        ?>

                                                        <option  value="<?=$key?>" ><?=$name."-".$val?></option>

                                                        <?php


                                                    }

                                                    ?>
                                                </select>

                                            </div>

                                            <button type="submit"  class="btn btn-success"><i class="icon-credit-card mr-2"></i>ЗАКАЗАТЬ ВЫВОД</button>
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

