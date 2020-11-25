<div class="ps-section__left">
    <aside class="ps-widget--account-dashboard">
        <div class="ps-widget__content">

            <?php

            $active[$this->route['action']] = "class = 'active'";
            ?>


            <ul>
                <li <?=isset($active['index']) ? $active['index'] : ''; ?> ><a href="/buyer/"><i class="icon-user"></i> Аккаунт</a></li>
                <li <?=isset($active['purchases']) ? $active['purchases'] : ''; ?> ><a href="/buyer/purchases/"><i class="icon-share"></i> Мои покупки</a></li>
                <li <?=isset($active['requis']) ? $active['requis'] : ''; ?>><a href="/buyer/requis/"><i class="icon-bubble-text"></i> Реквезиты </a></li>
                <li <?=isset($active['payout']) ? $active['payout'] : ''; ?>><a href="/buyer/payout/"><i class="icon-bag-dollar"></i> Заказать выплату </a></li>
                <li><a href="/user/logout/"><i class="icon-power-switch"></i> Выход</a></li>
            </ul>
        </div>
    </aside>
</div>