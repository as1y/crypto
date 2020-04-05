
<div class="card">
    <div class="card-header bg-white header-elements-inline">
        <h6 class="card-title">Статистика <b><?=$company['name']?></b></h6>
        <div class="header-elements">
            <div class="list-icons">
                <a class="list-icons-item" data-action="collapse"></a>
                <a class="list-icons-item" data-action="remove"></a>
            </div>
        </div>
    </div>
    <div class="card-body justify-content-center">

        <div class="row">
            <div class="col-sm-6 col-xl-3">


                <a href="#">

                    <div class="card card-body bg-indigo-400 has-bg-image">
                        <div class="media">
                            <div class="mr-3 align-self-center">
                                <i class="icon-phone2 icon-3x opacity-75"></i>
                            </div>

                            <div class="media-body text-right">
                                <h3 class="mb-0">0</h3>
                                <span class="text-uppercase font-size-xs">Звонков</span>
                            </div>
                        </div>
                    </div>

                </a>


            </div>
            <div class="col-sm-6 col-xl-3">

                <a href="#">

                    <div class="card card-body bg-success-400 has-bg-image">
                        <div class="media">
                            <div class="mr-3 align-self-center">
                                <i class="icon-target2 icon-3x opacity-75"></i>
                            </div>

                            <div class="media-body text-right">
                                <h3 class="mb-0">0</h3>
                                <span class="text-uppercase font-size-xs">Результатов</span>
                            </div>
                        </div>
                    </div>

                </a>

            </div>
            <div class="col-sm-6 col-xl-3">

                <a href="#">

                    <div class="card card-body bg-danger-400 has-bg-image">
                        <div class="media">
                            <div class="media-body">
                                <h3 class="mb-0">0</h3>
                                <span class="text-uppercase font-size-xs">На модерации</span>
                            </div>

                            <div class="ml-3 align-self-center">
                                <i class="icon-crown icon-3x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </a>



            </div>
            <div class="col-sm-6 col-xl-3">

                <a href="">

                    <div class="card card-body bg-warning-400 has-bg-image">
                        <div class="media">
                            <div class="media-body">
                                <h3 class="mb-0">0</h3>
                                <span class="text-uppercase font-size-xs">Конверсия</span>
                            </div>

                            <div class="ml-3 align-self-center">
                                <i class="icon-percent icon-3x opacity-75"></i>
                            </div>
                        </div>
                    </div>


                </a>

            </div>
        </div>

    </div>
</div>




    <div class="card">
        <div class="card-header bg-white header-elements-inline">
            <h6 class="card-title">Управление <b><?=$company['name']?></b></h6>

            <div class="header-elements">
                <div class="list-icons">
                    <a class="list-icons-item" data-action="collapse"></a>
                    <a class="list-icons-item" data-action="remove"></a>
                </div>
            </div>


        </div>
        <div class="card-body justify-content-center">

            <div class="row">
                <div class="col-sm-6 col-xl-3">


                    <div class="card-body">



                        <? if($status['company'] === true):?>

                        <i class="icon-cross2 icon-2x text-danger border-danger border-3 rounded-round p-3 mb-3"></i>


                                <?if ($company['status'] == 2):?>
                                    <h5 class="card-title">ЗВОНКИ НЕ ИДУТ</h5>
                                    <p class="mb-3">Выполните условия и запустите проект</p>
                                <?endif;?>

                                <?if ($company['status'] == 3):?>
                                <h5 class="card-title">НЕ РАБОЧЕЕ ВРЕМЯ</h5>
                                <p class="mb-3">Звонки сегодня запрещены</p>

                                <?endif;?>

                                <?if ($company['status'] == 4):?>
                                <h5 class="card-title">НЕ РАБОЧЕЕ ВРЕМЯ</h5>
                                <p class="mb-3">Звонки в данное время запрещены</p>

                                <?endif;?>

                                <?if ($company['status'] == 5):?>

                                <h5 class="card-title">ДОСТИГНУТ ДНЕВНОЙ ЛИМИТ</h5>
                                <p class="mb-3">Достигнут дневной лимит</p>

                                <?endif;?>


                        <a href="/project/?id=<?=$_GET['id']?>&action=play" class="btn bg-success"><i class="icon-play4 ml-2"></i> ЗАПУСК</a>



                        <?else:?>

                            <i class="icon-checkmark icon-2x text-success border-success border-3 rounded-round p-3 mb-3"></i>

                            <h5 class="card-title">ПРОЕКТ АКТИВЕН</h5>
<!--                            <p class="mb-3">Изменить статус проекта</p>-->



                            <a href="/project/?id=<?=$_GET['id']?>&action=stop" class="btn bg-danger"><i class="icon-play4 ml-2"></i> ОСТАНОВИТЬ ПРОЕКТ</a>




                        <?endif;?>









                    </div>


                </div>


                <div class="col-sm-6 col-xl-3">


                    <div class="card-body">
                        <i class="icon-cross2 icon-2x text-danger border-danger border-3 rounded-round p-3 mb-3"></i>
                        <h5 class="card-title">Center alignment</h5>
                        <p class="mb-3">Use <code>.text-center</code> alignment utility class to center content horizontally. Responsive options are also available</p>

                        <a href="#" class="btn bg-success">Read more <i class="icon-arrow-right14 ml-2"></i></a>
                    </div>


                </div>
                <div class="col-sm-6 col-xl-3">


                    <div class="card-body">
                        <i class="icon-cross2 icon-2x text-danger border-danger border-3 rounded-round p-3 mb-3"></i>
                        <h5 class="card-title">Center alignment</h5>
                        <p class="mb-3">Use <code>.text-center</code> alignment utility class to center content horizontally. Responsive options are also available</p>

                        <a href="#" class="btn bg-success">Read more <i class="icon-arrow-right14 ml-2"></i></a>
                    </div>


                </div>
                <div class="col-sm-6 col-xl-3">


                    <div class="card-body">
                        <i class="icon-cross2 icon-2x text-danger border-danger border-3 rounded-round p-3 mb-3"></i>
                        <h5 class="card-title">Center alignment</h5>
                        <p class="mb-3">Use <code>.text-center</code> alignment utility class to center content horizontally. Responsive options are also available</p>

                        <a href="#" class="btn bg-success">Read more <i class="icon-arrow-right14 ml-2"></i></a>
                    </div>


                </div>
            </div>

        </div>
    </div>


