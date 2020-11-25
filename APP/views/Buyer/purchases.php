
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
                                <h3>Мои попкупки</h3>
                            </div>
                            <div class="ps-section__content">
                                <div class="table-responsive">
                                    <table class="table ps-table ps-table--notification">
                                        <thead>
                                        <tr>
                                            <th>Покупка</th>
                                            <th>Дата</th>
                                            <th>Статус</th>
                                        </tr>
                                        </thead>
                                        <tbody>

                                    <?php foreach ($moipokupki as $key=>$val):?>

                                        <tr>
                                            <td><?=$val['offer']?></td>
                                            <td><?=$val['date']?></td>
                                            <td> <?=statuscashback($val['status']);?>
                                            </td>
                                        </tr>

                                    <?php endforeach;?>



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