<script>
    let ref = document.referrer;
    console.log(ref);
</script>

<div class="category-wrapper">
    <div class="container">


        <div class="row">
            <div class="col-md-3">
                <div class="cbx-sidebar">
                    <div class="widget widget-couponz-category-filter">

                        <ul>

                            <?php foreach (\APP\models\Panel::getCategory(13) as $val): ?>

                                <li>
                                    <a href="//<?=CONFIG['DOMAIN']?>/promocode/vse/<?=$val['url']?>">
                                        <div class="category-thumb text-center">
                                            <i class="fa fa-percent"></i>
                                        </div>

                                        <span>  <?=obrezanie($val['name'], 23)?></span>
                                    </a>
                                </li>




                            <?php  endforeach; ?>


                        </ul>

                    </div>

 


                </div>
            </div>


            <div class="col-md-9">
                <div class="row">

                    <?=generetuCouponinCode($coupons, $PAGESLIST['ViewPage'], $PAGESLIST['CouponsPerPage'])?>


                </div>





            </div>
        </div>
    </div>
</div>
