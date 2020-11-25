
<div style="position: initial" class="col-md-12">
    <div class="pagination-wrapper">
        <ul class="pagination">
            <?php if($PAGESLIST['ViewPage'] > 1): ?>
                <li><a href="#" onclick="changePage(<?=($PAGESLIST['ViewPage']-1)?>)">Назад</a></li>
            <?php endif;?>



            <?php for ($page=$starpage; $page <= $endpage; $page++) : ?>
                <li  class="<?= ($page == $PAGESLIST['ViewPage']) ? "active" : "" ?>"><a href="#" onclick="changePage(<?=$page?>)"><?=$page?></a></li>
            <?php endfor;?>


            <?php if($PAGESLIST['ViewPage'] < $endpage): ?>
                <li class="page-item"><a class="page-link" href="#" onclick="changePage(<?=($PAGESLIST['ViewPage']+1)?>)">Вперед <i class="fa fa-angle-right"></i></a></li>
            <?php endif;?>





        </ul>
    </div>
</div>
