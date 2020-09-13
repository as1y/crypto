<div class="card">
    <div class="card-header bg-dark text-white header-elements-inline">
        <h5 class="card-title">СТАТИСТИКА СЕГОДНЯ</h5>

    </div>



    <div class="card-body">

            <table  class="table datatable-basic text-center">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Компания</th>
                    <th>Заработок</th>
                    <th>Конверсий</th>
                    <th>Кликов</th>
                    <th>Действия</th>

                </tr>
                </thead>
                <tbody>

                <?php
                $allzarabotok=0;
                foreach ($companiestoday as $key=>$value):?>
                    <tr>
                        <td><?=$key?></td>
                        <td><?=$value['name']?></td>
                        <td><b><?=$value['zarabotok']?></b> руб.</td>

                        <td><?=$value['conversion']?></td>
                        <td><?=$value['clicks']?></td>
                        <td><a href="/panel/stat?company=<?=$key?>">ДЕТАЛЬНО</a></td>
                    </tr>
                <?php
                $allzarabotok = $allzarabotok + $value['zarabotok'];
                endforeach;?>
                </tbody>
            </table>

        <hr>
        <b>ОБЩИЙ ЗАРАБОТОК: </b> <?=$allzarabotok?>

    </div>



</div>
