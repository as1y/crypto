<div class="ps-page--product">
    <div class="container">
        <div class="ps-product--detail ps-product--full-content">
            <div class="ps-product__top">
                <div class="ps-product__header">


                    <div class="ps-product__thumbnail" data-vertical="true">

                        <figure>
                            <div class="ps-wrapper">
                                <div class="ps-product__gallery" data-arrow="true">
                                    <div class="item"><a href="<?=$product['picture']?>"><img src="<?=$product['picture']?>" alt=""></a></div>

                                    <?php if (!empty($product['extendedpicture'])): ?>
                                    <div class="item"><a href="/assets_main/img/products/detail/fullwidth/2.jpg"><img src="/assets_main/img/products/detail/fullwidth/2.jpg" alt=""></a></div>
                                    <div class="item"><a href="/assets_main/img/products/detail/fullwidth/3.jpg"><img src="/assets_main/img/products/detail/fullwidth/3.jpg" alt=""></a></div>
                                    <?php endif; ?>


                                </div>
                            </div>
                        </figure>

                        <div class="ps-product__variants" data-item="3" data-md="3" data-sm="3" data-arrow="false">
                            <div class="item"><img src="<?=$product['picture']?>" alt=""></div>
                            <?php if (!empty($product['extendedpicture'])): ?>
                            <div class="item"><img src="/assets_main/img/products/detail/fullwidth/2.jpg" alt=""></div>
                            <div class="item"><img src="/assets_main/img/products/detail/fullwidth/3.jpg" alt=""></div>
                            <?php endif; ?>

                        </div>
                    </div>


                    <div class="ps-product__info">
                        <h1><?=$product['name']?></h1>
                        <div class="ps-product__meta">
                            <p>Производитель:<a href="#"><?=$product['vendor']?></a></p>
                            <div class="ps-product__rating">
                                <select class="ps-rating" data-read-only="true">
                                    <option value="1">1</option>
                                    <option value="1">2</option>
                                    <option value="1">3</option>
                                    <option value="1">4</option>
                                    <option value="2">5</option>
                                </select><span>(1 review)</span>
                            </div>
                        </div>
                        <div class="ps-product__desc">
                            <p>Продавец:<a href="/catalog/?Company=<?=$product->companies['id']?>"><strong> <?=$product->companies['name']?></strong></a></p>
                            <p><img src="<?=$product->companies['logo']?>"></p>
                            <ul class="ps-list--dot">
                                <li><?php echo $product['description'];?></li>

                            </ul>
                        </div>


                        <?php if (!empty($product['param'])): ?>
                            <div class="ps-product__specification">
                                <p><strong>ДОПОЛНИТЕЛЬНО</strong> </p>
                                <p class="tags"><?=$product['param'];?></p>
                            </div>
                        <?php endif;?>




                    </div>
                </div>
                <div class="ps-product__price-right">
                    <?php if ($product['percentdiscount'] >= 10): ?>
                    <h4 class="ps-product__price sale"><?=round($product['price'])?>₽  <del><?=round($product['oldprice'])?>₽</del><small> (-<?=$product['percentdiscount']?>%)</small>
                    </h4>
                    <?php endif;?>

                    <?php if ($product['percentdiscount'] < 10): ?>
                    <h4 class="ps-product__price"><?=round($product['price'])?>₽</h4>
                    <?php endif;?>
                    <div class="ps-product__shopping">



                        <!-- БЕЗ РЕГИСТРАЦИИ> -->
                        <?php if (empty($_SESSION['ulogin']['id'])): ?>
                            <a class="ps-btn ps-btn--gray" target="_blank" href="<?=$product['url']?>">Кешбек от <?=$product['cashback']?> ₽</a>
                            <aside class="widget widget_product widget_features">
                                <p><i class="icon-user"></i> <a href="/user"><b>Войти</b></a></p>
                                <p><i class="icon-register"></i> <a href="/user/register"><b>Регистрация</b></a></p>
                                <p><i class="icon-network"></i> Для получения кешбека нужно войти в систему</p>
                            </aside>
                        <?php endif;?>
                        <!-- БЕЗ РЕГИСТРАЦИИ> -->


                        <!-- БЕЗ РЕГИСТРАЦИИ> -->
                        <?php if (!empty($_SESSION['ulogin']['id'])): ?>
                            <a class="ps-btn" target="_blank" href="/go?product=<?=$product['id']?>">В МАГАЗИН</a>
                            <aside class="widget widget_product widget_features">
                                <p><i class="icon-arrow-up-square"></i>Перейдите в магазин!</p>
                                <p><i class="icon-bag-yen"></i> Совершите покупку</p>
                                <p><i class="icon-3d-rotate"></i> Получите кешбек <br> <b>от <?=$product['cashback']?> ₽ </b></p>
                            </aside>
                        <?php endif;?>
                        <!-- БЕЗ РЕГИСТРАЦИИ> -->




<!--                        <div class="ps-product__actions"><a href="#"><i class="icon-heart"></i> Add to whishlist</a><a href="#"><i class="icon-chart-bars"></i> Compare</a></div>-->



                    </div>




                </div>


            </div>

        </div>




    </div>
</div>