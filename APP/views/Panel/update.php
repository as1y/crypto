<div class="card">
    <div class="card-header bg-dark text-white header-elements-inline">
        <h5 class="card-title">ОБНОВЛЕНИЕ КУПОНОВ</h5>

    </div>

    <div class="card-body">




        <a href="/update/?action=updatecompany" type="button" target="_blank" class="btn btn-info"><i class="icon-check mr-2"></i>ОБНОВИТЬ ВРУЧНУЮ</a>
        <br>
        Дата последнего обновления компаний: <?=$updatestatus[1]['date']?> <br>
         <hr>

        <a href="/update/?action=updatecoupons" target="_blank" type="button" class="btn btn-info"><i class="icon-check mr-2"></i>ОБНОВИТЬ ВРУЧНУЮ</a>
        <br>
        Дата последнего обновления купонов: <?=$updatestatus[2]['date']?> <br>
        <hr>

        <a href="/update/?action=updatebanners" target="_blank" type="button" class="btn btn-info"><i class="icon-check mr-2"></i>ОБНОВИТЬ ВРУЧНУЮ</a>
        <br>
        Дата последнего обновления баннеров: <?=$updatestatus[3]['date']?> <br>
        <hr>

        <a href="/update/?action=seopages" target="_blank" type="button" class="btn btn-info"><i class="icon-check mr-2"></i>ОБНОВИТЬ ВРУЧНУЮ</a>
        <br>
        Дата последнего обновления seo страниц: <?=$updatestatus[3]['date']?> <br>


    </div>



</div>
