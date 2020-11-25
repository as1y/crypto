<!DOCTYPE html>
<html lang="ru">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="format-detection" content="telephone=no">
    <?php \APP\core\base\View::getMeta()?>
    <meta name="verify-admitad" content="12e6d21bf0" />

    <link href="https://fonts.googleapis.com/css?family=Work+Sans:300,400,500,600,700&amp;amp;subset=latin-ext" rel="stylesheet">
    <link rel="stylesheet" href="/assets_main/plugins/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="/assets_main/fonts/Linearicons/Linearicons/Font/demo-files/demo.css">
    <link rel="stylesheet" href="/assets_main/plugins/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets_main/plugins/owl-carousel/assets/owl.carousel.min.css">
    <link rel="stylesheet" href="/assets_main/plugins/owl-carousel/assets/owl.theme.default.min.css">
    <link rel="stylesheet" href="/assets_main/plugins/slick/slick/slick.css">
    <link rel="stylesheet" href="/assets_main/plugins/nouislider/nouislider.min.css">
    <link rel="stylesheet" href="/assets_main/plugins/lightGallery-master/dist/css/lightgallery.min.css">
    <link rel="stylesheet" href="/assets_main/plugins/jquery-bar-rating/dist/themes/fontawesome-stars.css">
    <link rel="stylesheet" href="/assets_main/plugins/select2/dist/css/select2.min.css">
    <link rel="stylesheet" href="/assets_main/css/style.css">
    <link rel="stylesheet" href="/assets_main/css/market-place-2.css">



<?php gtmHEAD();?>
</head>
<body>
<?php gtmBODY();?>



<header class="header header--standard header--market-place-2" data-sticky="true">
    <div class="header__top">
        <div class="container">
            <div class="header__left">
                <p>Добро пожаловать на <b>Discount Market</b> маркетплейс с повышенном кэшбеком</p>
            </div>
            <div class="header__right">
                <ul class="header__top-links">
                    <li><a href="#">Проверить статус кэшбека</a></li>
                    <li><a href="#"><img src="/assets_main/img/flag/russia.png" alt=""> Россия</a></li>



                </ul>
            </div>
        </div>
    </div>
    <div class="header__content">
        <div class="container">
            <div class="header__content-left"><a class="ps-logo" href="/"><img src="/logo.png" alt=""></a>
                <div class="menu--product-categories">
                    <div class="menu__toggle"><i class="icon-menu"></i><span> Категории товаров</span></div>
                    <div class="menu__content">
                        <ul class="menu--dropdown">


                            <?php foreach (\APP\models\Panel::getCategory(15) as $val): ?>
                                <li><a href="/catalog/?Category=<?=$val['id']?>"> <?=obrezanie($val['name'], 20)?></a></li>
                            <?php endforeach;?>



                        </ul>
                    </div>
                </div>
            </div>

            <div class="header__content-center">
                <form class="ps-form--quick-search" action="#" method="get">
                    <input class="form-control" type="text" placeholder="Поиск товаров с кешбеком...">
                    <button>Найти</button>
                </form>

                <?php $topproduct = \APP\core\base\Model::getTopProduct()?>

                <p>
                <?php foreach ($topproduct as $val): ?>
                    <a href="/product/<?=$val['id']?>/<?=$val['uri']?>" target="_blank"><?=obrezanie($val['name'], 30)?></a>
                <?php endforeach; ?>
                </p>



            </div>


            <div class="header__content-right">
                <div class="header__actions">

                    <a class="header__extra" href="#"><i class="icon-eye"></i><span><i>0</i></span></a>

                    <?php if (empty($_SESSION['ulogin']['id'])):?>
                        <div class="ps-block--user-header">
                            <div class="ps-block__left"><i class="icon-user"></i></div>
                            <div class="ps-block__right"><a href="/user">Войти</a><a href="/user/register/">Регистрация</a></div>
                        </div>
                    <?endif;?>

                    <?php if (!empty($_SESSION['ulogin']['id'])):?>

                        <div class="ps-block--user-header">
                            <div class="ps-block__left"><a href="/buyer/"><i class="icon-user"></i></a></div>
                            <div class="ps-block__right">
                                Баланс <b><?=\APP\core\base\Model::getBal()?></b> ₽
                                <a href="/buyer/">Мой кабинет</a>
                            </div>
                        </div>
                    <?endif;?>




                </div>
            </div>
        </div>
    </div>
    <nav class="navigation">
        <div class="container">
            <ul class="menu menu--market-2">


            <?php foreach (\APP\models\Panel::getCategory(5) as $val): ?>
               <li><a href="//<?=CONFIG['DOMAIN']?>/catalog/?Category=<?=$val['id']?>"><?=obrezanie($val['name'], 40)?></a></li>
            <?php  endforeach; ?>


            </ul>

        </div>
    </nav>
</header>
<header class="header header--mobile" data-sticky="true">
    <div class="header__top">
        <div class="header__left">
            <p>Welcome to Martfury Online Shopping Store !</p>
        </div>
        <div class="header__right">
            <ul class="navigation__extra">
                <li><a href="#">Sell on Martfury</a></li>
                <li><a href="#">Tract your order</a></li>
                <li>
                    <div class="ps-dropdown"><a href="#">US Dollar</a>
                        <ul class="ps-dropdown-menu">
                            <li><a href="#">Us Dollar</a></li>
                            <li><a href="#">Euro</a></li>
                        </ul>
                    </div>
                </li>
                <li>
                    <div class="ps-dropdown language"><a href="#"><img src="/assets_main/img/flag/en.png" alt="">English</a>
                        <ul class="ps-dropdown-menu">
                            <li><a href="#"><img src="/assets_main/img/flag/germany.png" alt=""> Germany</a></li>
                            <li><a href="#"><img src="/assets_main/img/flag/fr.png" alt=""> France</a></li>
                        </ul>
                    </div>
                </li>
            </ul>
        </div>
    </div>
    <div class="navigation--mobile">
        <div class="navigation__left"><a class="ps-logo" href="/"><img src="/assets_main/img/logo_light.png" alt=""></a></div>
        <div class="navigation__right">
            <div class="header__actions">
                <div class="ps-cart--mini"><a class="header__extra" href="#"><i class="icon-bag2"></i><span><i>0</i></span></a>
                    <div class="ps-cart__content">
                        <div class="ps-cart__items">
                            <div class="ps-product--cart-mobile">
                                <div class="ps-product__thumbnail"><a href="#"><img src="/assets_main/img/products/clothing/7.jpg" alt=""></a></div>
                                <div class="ps-product__content"><a class="ps-product__remove" href="#"><i class="icon-cross"></i></a><a href="product-default.html">MVMTH Classical Leather Watch In Black</a>
                                    <p><strong>Sold by:</strong> YOUNG SHOP</p><small>1 x $59.99</small>
                                </div>
                            </div>
                            <div class="ps-product--cart-mobile">
                                <div class="ps-product__thumbnail"><a href="#"><img src="/assets_main/img/products/clothing/5.jpg" alt=""></a></div>
                                <div class="ps-product__content"><a class="ps-product__remove" href="#"><i class="icon-cross"></i></a><a href="product-default.html">Sleeve Linen Blend Caro Pane Shirt</a>
                                    <p><strong>Sold by:</strong> YOUNG SHOP</p><small>1 x $59.99</small>
                                </div>
                            </div>
                        </div>
                        <div class="ps-cart__footer">
                            <h3>Sub Total:<strong>$59.99</strong></h3>
                            <figure><a class="ps-btn" href="shopping-cart.html">View Cart</a><a class="ps-btn" href="checkout.html">Checkout</a></figure>
                        </div>
                    </div>
                </div>
                <div class="ps-block--user-header">
                    <div class="ps-block__left"><i class="icon-user"></i></div>
                    <div class="ps-block__right"><a href="my-account.html">Login</a><a href="my-account.html">Register</a></div>
                </div>
            </div>
        </div>
    </div>
    <div class="ps-search--mobile">
        <form class="ps-form--search-mobile" action="index.html" method="get">
            <div class="form-group--nest">
                <input class="form-control" type="text" placeholder="Search something...">
                <button><i class="icon-magnifier"></i></button>
            </div>
        </form>
    </div>
</header>
<div class="ps-panel--sidebar" id="cart-mobile">
    <div class="ps-panel__header">
        <h3>Shopping Cart</h3>
    </div>
    <div class="navigation__content">
        <div class="ps-cart--mobile">
            <div class="ps-cart__content">
                <div class="ps-product--cart-mobile">
                    <div class="ps-product__thumbnail"><a href="#"><img src="/assets_main/img/products/clothing/7.jpg" alt=""></a></div>
                    <div class="ps-product__content"><a class="ps-product__remove" href="#"><i class="icon-cross"></i></a><a href="product-default.html">MVMTH Classical Leather Watch In Black</a>
                        <p><strong>Sold by:</strong> YOUNG SHOP</p><small>1 x $59.99</small>
                    </div>
                </div>
            </div>
            <div class="ps-cart__footer">
                <h3>Sub Total:<strong>$59.99</strong></h3>
                <figure><a class="ps-btn" href="shopping-cart.html">View Cart</a><a class="ps-btn" href="checkout.html">Checkout</a></figure>
            </div>
        </div>
    </div>
</div>
<div class="ps-panel--sidebar" id="navigation-mobile">
    <div class="ps-panel__header">
        <h3>Categories</h3>
    </div>
    <div class="ps-panel__content">
        <ul class="menu--mobile">
            <li><a href="#">Hot Promotions</a>
            </li>
            <li class="menu-item-has-children has-mega-menu"><a href="#">Consumer Electronic</a><span class="sub-toggle"></span>
                <div class="mega-menu">
                    <div class="mega-menu__column">
                        <h4>Electronic<span class="sub-toggle"></span></h4>
                        <ul class="mega-menu__list">
                            <li><a href="#">Home Audio &amp; Theathers</a>
                            </li>
                            <li><a href="#">TV &amp; Videos</a>
                            </li>
                            <li><a href="#">Camera, Photos &amp; Videos</a>
                            </li>
                            <li><a href="#">Cellphones &amp; Accessories</a>
                            </li>
                            <li><a href="#">Headphones</a>
                            </li>
                            <li><a href="#">Videosgames</a>
                            </li>
                            <li><a href="#">Wireless Speakers</a>
                            </li>
                            <li><a href="#">Office Electronic</a>
                            </li>
                        </ul>
                    </div>
                    <div class="mega-menu__column">
                        <h4>Accessories &amp; Parts<span class="sub-toggle"></span></h4>
                        <ul class="mega-menu__list">
                            <li><a href="#">Digital Cables</a>
                            </li>
                            <li><a href="#">Audio &amp; Video Cables</a>
                            </li>
                            <li><a href="#">Batteries</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </li>
            <li><a href="#">Clothing &amp; Apparel</a>
            </li>
            <li><a href="#">Home, Garden &amp; Kitchen</a>
            </li>
            <li><a href="#">Health &amp; Beauty</a>
            </li>
            <li><a href="#">Yewelry &amp; Watches</a>
            </li>
            <li class="menu-item-has-children has-mega-menu"><a href="#">Computer &amp; Technology</a><span class="sub-toggle"></span>
                <div class="mega-menu">
                    <div class="mega-menu__column">
                        <h4>Computer &amp; Technologies<span class="sub-toggle"></span></h4>
                        <ul class="mega-menu__list">
                            <li><a href="#">Computer &amp; Tablets</a>
                            </li>
                            <li><a href="#">Laptop</a>
                            </li>
                            <li><a href="#">Monitors</a>
                            </li>
                            <li><a href="#">Networking</a>
                            </li>
                            <li><a href="#">Drive &amp; Storages</a>
                            </li>
                            <li><a href="#">Computer Components</a>
                            </li>
                            <li><a href="#">Security &amp; Protection</a>
                            </li>
                            <li><a href="#">Gaming Laptop</a>
                            </li>
                            <li><a href="#">Accessories</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </li>
            <li><a href="#">Babies &amp; Moms</a>
            </li>
            <li><a href="#">Sport &amp; Outdoor</a>
            </li>
            <li><a href="#">Phones &amp; Accessories</a>
            </li>
            <li><a href="#">Books &amp; Office</a>
            </li>
            <li><a href="#">Cars &amp; Motocycles</a>
            </li>
            <li><a href="#">Home Improments</a>
            </li>
            <li><a href="#">Vouchers &amp; Services</a>
            </li>
        </ul>
    </div>
</div>
<div class="navigation--list">
    <div class="navigation__content"><a class="navigation__item ps-toggle--sidebar" href="#menu-mobile"><i class="icon-menu"></i><span> Menu</span></a><a class="navigation__item ps-toggle--sidebar" href="#navigation-mobile"><i class="icon-list4"></i><span> Categories</span></a><a class="navigation__item ps-toggle--sidebar" href="#search-sidebar"><i class="icon-magnifier"></i><span> Search</span></a><a class="navigation__item ps-toggle--sidebar" href="#cart-mobile"><i class="icon-bag2"></i><span> Cart</span></a></div>
</div>
<div class="ps-panel--sidebar" id="search-sidebar">
    <div class="ps-panel__header">
        <form class="ps-form--search-mobile" action="index.html" method="get">
            <div class="form-group--nest">
                <input class="form-control" type="text" placeholder="Search something...">
                <button><i class="icon-magnifier"></i></button>
            </div>
        </form>
    </div>
    <div class="navigation__content"></div>
</div>
<div class="ps-panel--sidebar" id="menu-mobile">
    <div class="ps-panel__header">
        <h3>Menu</h3>
    </div>
    <div class="ps-panel__content">
        <ul class="menu--mobile">
            <li class="current-menu-item menu-item-has-children"><a href="index.html">Home</a><span class="sub-toggle"></span>
                <ul class="sub-menu">
                    <li><a href="index.html">Marketplace Full Width</a>
                    </li>
                    <li><a href="homepage-2.html">Home Auto Parts</a>
                    </li>
                    <li><a href="homepage-10.html">Home Technology</a>
                    </li>
                    <li><a href="homepage-9.html">Home Organic</a>
                    </li>
                    <li><a href="homepage-3.html">Home Marketplace V1</a>
                    </li>
                    <li><a href="homepage-4.html">Home Marketplace V2</a>
                    </li>
                    <li><a href="homepage-5.html">Home Marketplace V3</a>
                    </li>
                    <li><a href="homepage-6.html">Home Marketplace V4</a>
                    </li>
                    <li><a href="homepage-7.html">Home Electronic</a>
                    </li>
                    <li><a href="homepage-8.html">Home Furniture</a>
                    </li>
                    <li><a href="homepage-kids.html">Home Kids</a>
                    </li>
                    <li><a href="homepage-photo-and-video.html">Home photo and picture</a>
                    </li>
                    <li><a href="home-medical.html">Home Medical</a>
                    </li>
                </ul>
            </li>
            <li class="menu-item-has-children has-mega-menu"><a href="shop-default.html">Shop</a><span class="sub-toggle"></span>
                <div class="mega-menu">
                    <div class="mega-menu__column">
                        <h4>Catalog Pages<span class="sub-toggle"></span></h4>
                        <ul class="mega-menu__list">
                            <li><a href="shop-default.html">Shop Default</a>
                            </li>
                            <li><a href="shop-default.html">Shop Fullwidth</a>
                            </li>
                            <li><a href="shop-categories.html">Shop Categories</a>
                            </li>
                            <li><a href="shop-sidebar.html">Shop Sidebar</a>
                            </li>
                            <li><a href="shop-sidebar-without-banner.html">Shop Without Banner</a>
                            </li>
                            <li><a href="shop-carousel.html">Shop Carousel</a>
                            </li>
                        </ul>
                    </div>
                    <div class="mega-menu__column">
                        <h4>Product Layout<span class="sub-toggle"></span></h4>
                        <ul class="mega-menu__list">
                            <li><a href="product-default.html">Default</a>
                            </li>
                            <li><a href="product-extend.html">Extended</a>
                            </li>
                            <li><a href="product-full-content.html">Full Content</a>
                            </li>
                            <li><a href="product-box.html">Boxed</a>
                            </li>
                            <li><a href="product-sidebar.html">Sidebar</a>
                            </li>
                            <li><a href="product-default.html">Fullwidth</a>
                            </li>
                        </ul>
                    </div>
                    <div class="mega-menu__column">
                        <h4>Product Types<span class="sub-toggle"></span></h4>
                        <ul class="mega-menu__list">
                            <li><a href="product-default.html">Simple</a>
                            </li>
                            <li><a href="product-default.html">Color Swatches</a>
                            </li>
                            <li><a href="product-image-swatches.html">Images Swatches</a>
                            </li>
                            <li><a href="product-countdown.html">Countdown</a>
                            </li>
                            <li><a href="product-multi-vendor.html">Multi-Vendor</a>
                            </li>
                            <li><a href="product-instagram.html">Instagram</a>
                            </li>
                            <li><a href="product-affiliate.html">Affiliate</a>
                            </li>
                            <li><a href="product-on-sale.html">On sale</a>
                            </li>
                            <li><a href="product-video.html">Video Featured</a>
                            </li>
                            <li><a href="product-groupped.html">Grouped</a>
                            </li>
                            <li><a href="product-out-stock.html">Out Of Stock</a>
                            </li>
                        </ul>
                    </div>
                    <div class="mega-menu__column">
                        <h4>Woo Pages<span class="sub-toggle"></span></h4>
                        <ul class="mega-menu__list">
                            <li><a href="shopping-cart.html">Shopping Cart</a>
                            </li>
                            <li><a href="checkout.html">Checkout</a>
                            </li>
                            <li><a href="whishlist.html">Whishlist</a>
                            </li>
                            <li><a href="compare.html">Compare</a>
                            </li>
                            <li><a href="order-tracking.html">Order Tracking</a>
                            </li>
                            <li><a href="my-account.html">My Account</a>
                            </li>
                            <li><a href="checkout-2.html">Checkout 2</a>
                            </li>
                            <li><a href="shipping.html">Shipping</a>
                            </li>
                            <li><a href="payment.html">Payment</a>
                            </li>
                            <li><a href="payment-success.html">Payment Success</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </li>
            <li class="menu-item-has-children has-mega-menu"><a href="#">Pages</a><span class="sub-toggle"></span>
                <div class="mega-menu">
                    <div class="mega-menu__column">
                        <h4>Basic Page<span class="sub-toggle"></span></h4>
                        <ul class="mega-menu__list">
                            <li><a href="about-us.html">About Us</a>
                            </li>
                            <li><a href="contact-us.html">Contact</a>
                            </li>
                            <li><a href="faqs.html">Faqs</a>
                            </li>
                            <li><a href="comming-soon.html">Comming Soon</a>
                            </li>
                            <li><a href="404-page.html">404 Page</a>
                            </li>
                        </ul>
                    </div>
                    <div class="mega-menu__column">
                        <h4>Vendor Pages<span class="sub-toggle"></span></h4>
                        <ul class="mega-menu__list">
                            <li><a href="become-a-vendor.html">Become a Vendor</a>
                            </li>
                            <li><a href="vendor-store.html">Vendor Store</a>
                            </li>
                            <li><a href="vendor-dashboard-free.html">Vendor Dashboard Free</a>
                            </li>
                            <li><a href="vendor-dashboard-pro.html">Vendor Dashboard Pro</a>
                            </li>
                            <li><a href="store-list.html">Store List</a>
                            </li>
                            <li><a href="store-list.html">Store List 2</a>
                            </li>
                            <li><a href="store-detail.html">Store Detail</a>
                            </li>
                        </ul>
                    </div>
                    <div class="mega-menu__column">
                        <h4>Account Pages<span class="sub-toggle"></span></h4>
                        <ul class="mega-menu__list">
                            <li><a href="user-information.html">User Information</a>
                            </li>
                            <li><a href="addresses.html">Addresses</a>
                            </li>
                            <li><a href="invoices.html">Invoices</a>
                            </li>
                            <li><a href="invoice-detail.html">Invoice Detail</a>
                            </li>
                            <li><a href="notifications.html">Notifications</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </li>
            <li class="menu-item-has-children has-mega-menu"><a href="#">Blogs</a><span class="sub-toggle"></span>
                <div class="mega-menu">
                    <div class="mega-menu__column">
                        <h4>Blog Layout<span class="sub-toggle"></span></h4>
                        <ul class="mega-menu__list">
                            <li><a href="blog-grid.html">Grid</a>
                            </li>
                            <li><a href="blog-list.html">Listing</a>
                            </li>
                            <li><a href="blog-small-thumb.html">Small Thumb</a>
                            </li>
                            <li><a href="blog-left-sidebar.html">Left Sidebar</a>
                            </li>
                            <li><a href="blog-right-sidebar.html">Right Sidebar</a>
                            </li>
                        </ul>
                    </div>
                    <div class="mega-menu__column">
                        <h4>Single Blog<span class="sub-toggle"></span></h4>
                        <ul class="mega-menu__list">
                            <li><a href="blog-detail.html">Single 1</a>
                            </li>
                            <li><a href="blog-detail-2.html">Single 2</a>
                            </li>
                            <li><a href="blog-detail-3.html">Single 3</a>
                            </li>
                            <li><a href="blog-detail-4.html">Single 4</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</div>


<?=$content ?>



<footer class="ps-footer ps-footer--3">
    <div class="container">

        <div class="ps-block--site-features ps-block--site-features-2">
            <div class="ps-block__item">
                <div class="ps-block__left"><i class="icon-rocket"></i></div>
                <div class="ps-block__right">
                    <h4>Free Delivery</h4>
                    <p>For all oders over $99</p>
                </div>
            </div>
            <div class="ps-block__item">
                <div class="ps-block__left"><i class="icon-sync"></i></div>
                <div class="ps-block__right">
                    <h4>90 Days Return</h4>
                    <p>If goods have problems</p>
                </div>
            </div>
            <div class="ps-block__item">
                <div class="ps-block__left"><i class="icon-credit-card"></i></div>
                <div class="ps-block__right">
                    <h4>Secure Payment</h4>
                    <p>100% secure payment</p>
                </div>
            </div>
            <div class="ps-block__item">
                <div class="ps-block__left"><i class="icon-bubbles"></i></div>
                <div class="ps-block__right">
                    <h4>24/7 Support</h4>
                    <p>Dedicated support</p>
                </div>
            </div>
        </div>
        <div class="ps-footer__widgets">
            <aside class="widget widget_footer widget_contact-us">
                <div class="widget_content">

                    <a class="ps-logo"   href="/"><img src="/logo.png" style="width: 200px" alt=""></a>
                    <p align="center">Связаться с нами</p>

                </div>
            </aside>
            <aside class="widget widget_footer">
                <h4 class="widget-title">КАТЕГОРИИ</h4>
                <ul class="ps-list--link">

                    <?php foreach (\APP\models\Panel::getCategory(10) as $val): ?>
                        <li><a href="//<?=CONFIG['DOMAIN']?>/catalog/<?=$val['url']?>"><?=obrezanie($val['name'], 40)?></a></li>
                    <?php  endforeach; ?>

                </ul>
            </aside>
            <aside class="widget widget_footer">
                <h4 class="widget-title">МАГАЗИНЫ</h4>
                <ul class="ps-list--link">
                    <li><a href="about-us.html">About Us</a></li>
                    <li><a href="#">Affilate</a></li>
                    <li><a href="#">Career</a></li>
                    <li><a href="contact-us.html">Contact</a></li>
                </ul>
            </aside>
            <aside class="widget widget_footer">
                <h4 class="widget-title">ПОДРОБНЕЕ</h4>
                <ul class="ps-list--link">
                    <li><a href="#">Our Press</a></li>
                    <li><a href="checkout.html">Checkout</a></li>
                    <li><a href="my-account.html">My account</a></li>
                    <li><a href="shop-default.html">Shop</a></li>
                </ul>
            </aside>
        </div>




        <div class="ps-footer__copyright">
            <p>© 2020 Discount.Market Все права защищены</p>
            <p><span>Выплачиваем кешбек на:</span>
                <img src="/assets/visa.png" width="50px" alt="">
                <img src="/assets/master.png" alt="">
                <img src="/assets/mir.png" alt="">
                <img src="/assets/ya.png" height="28px" alt="">
                <img src="/assets/qiwi.png" height="28px" alt="">

            </p>
        </div>
    </div>
</footer>
<div id="back2top"><i class="pe-7s-angle-up"></i></div>
<div class="ps-site-overlay"></div>

<div class="ps-search" id="site-search"><a class="ps-btn--close" href="#"></a>
    <div class="ps-search__content">
        <form class="ps-form--primary-search" action="do_action" method="post">
            <input class="form-control" type="text" placeholder="Search for...">
            <button><i class="aroma-magnifying-glass"></i></button>
        </form>
    </div>
</div>



<div class="modal fade" id="product-quickview" tabindex="-1" role="dialog" aria-labelledby="product-quickview" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content"><span class="modal-close" data-dismiss="modal"><i class="icon-cross2"></i></span>
            <article class="ps-product--detail ps-product--fullwidth ps-product--quickview">
                <div class="ps-product__header">
                    <div class="ps-product__thumbnail" data-vertical="false">
                        <div class="ps-product__images" data-arrow="true">
                            <div class="item"><img src="/assets_main/img/products/detail/fullwidth/1.jpg" alt=""></div>
                            <div class="item"><img src="/assets_main/img/products/detail/fullwidth/2.jpg" alt=""></div>
                            <div class="item"><img src="/assets_main/img/products/detail/fullwidth/3.jpg" alt=""></div>
                        </div>
                    </div>
                    <div class="ps-product__info">
                        <h1>Marshall Kilburn Portable Wireless Speaker</h1>
                        <div class="ps-product__meta">
                            <p>Brand:<a href="shop-default.html">Sony</a></p>
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
                        <h4 class="ps-product__price">$36.78 – $56.99</h4>
                        <div class="ps-product__desc">
                            <p>Sold By:<a href="shop-default.html"><strong> Go Pro</strong></a></p>
                            <ul class="ps-list--dot">
                                <li> Unrestrained and portable active stereo speaker</li>
                                <li> Free from the confines of wires and chords</li>
                                <li> 20 hours of portable capabilities</li>
                                <li> Double-ended Coil Cord with 3.5mm Stereo Plugs Included</li>
                                <li> 3/4″ Dome Tweeters: 2X and 4″ Woofer: 1X</li>
                            </ul>
                        </div>
                        <div class="ps-product__shopping"><a class="ps-btn ps-btn--black" href="#">Add to cart</a><a class="ps-btn" href="#">Buy Now</a>
                            <div class="ps-product__actions"><a href="#"><i class="icon-heart"></i></a><a href="#"><i class="icon-chart-bars"></i></a></div>
                        </div>
                    </div>
                </div>
            </article>
        </div>
    </div>
</div>



<script src="/assets_main/plugins/jquery.min.js"></script>
<script src="/assets_main/plugins/nouislider/nouislider.min.js"></script>
<script src="/assets_main/plugins/popper.min.js"></script>
<script src="/assets_main/plugins/owl-carousel/owl.carousel.min.js"></script>
<script src="/assets_main/plugins/bootstrap/js/bootstrap.min.js"></script>
<script src="/assets_main/plugins/imagesloaded.pkgd.min.js"></script>
<script src="/assets_main/plugins/masonry.pkgd.min.js"></script>
<script src="/assets_main/plugins/isotope.pkgd.min.js"></script>
<script src="/assets_main/plugins/jquery.matchHeight-min.js"></script>
<script src="/assets_main/plugins/slick/slick/slick.min.js"></script>
<script src="/assets_main/plugins/jquery-bar-rating/dist/jquery.barrating.min.js"></script>
<script src="/assets_main/plugins/slick-animation.min.js"></script>
<script src="/assets_main/plugins/lightGallery-master/dist/js/lightgallery-all.min.js"></script>
<script src="/assets_main/plugins/sticky-sidebar/dist/sticky-sidebar.min.js"></script>
<script src="/assets_main/plugins/select2/dist/js/select2.full.min.js"></script>
<script src="/assets_main/plugins/gmap3.min.js"></script>
<!-- custom scripts-->
<script src="/assets_main/js/main.js"></script>
<script src="/coupons_script.js"></script>


</body>

</html>