
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
            <div class="row">
                <div class="col-lg-4">
                 <?php require_once( 'includes/buyermenu.php' ); ?>
                </div>


                <div class="col-lg-8">
                    <div class="ps-section__right">


                        <div class="ps-section--account-setting">
                            <div class="ps-section__header">
                                <h3>Мой аккаунт <?=$_SESSION['ulogin']['email']?></h3>
                            </div>
                            <div class="ps-section__content">
                                ДОСТУПНО ДЛЯ ВЫВОДА <b><?=\APP\core\base\Model::getBal()?> ₽</b><hr>


                                <div class="table-responsive">

                                    <b>История выплат</b>
                                    <table class="table ps-table ps-table--notification">
                                        <thead>
                                        <tr>
                                            <th>Дата</th>
                                            <th>Сумма</th>
                                            <th>Комментарий</th>
                                            <th>Статус</th>
                                        </tr>
                                        </thead>
                                        <tbody>

                                        <?php foreach ($balancelog as $key=>$val):?>

                                            <tr>
                                                <td><?=$val['date']?></td>
                                                <td><b><?=$val['sum']?> ₽</b></td>
                                                <td><?=$val['comment']?></td>
                                                <td ><?=paystatus($val['status'])?></td>
                                            </tr>



                                        <?php endforeach; ?>



                                        </tbody>
                                    </table>


                                </div>

                            </div>
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </section>
</main>