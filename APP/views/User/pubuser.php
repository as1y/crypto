<div class="col-md-12">

    <div class="col-xl-3 col-sm-6">
        <div class="card card-body text-center">
            <div class="mb-3">
                <h6 class="font-weight-semibold mb-0 mt-1"><?= $_SESSION['ulogin']['username'] ?></h6>

                <span class="d-block text-muted"><?= rendertypeaccount($_SESSION['ulogin']['role']) ?></span>


            </div>



            <a href="#" class="d-inline-block mb-3">
                <img src="<?=$_SESSION['ulogin']['avatar']?>" class="rounded-round"
                     width="150" height="150" alt="">
            </a>

            <a href="#" type="button" class=" btn btn-info"><i class="icon-eye mr-2"></i> Посмотреть профиль</a>
            <br>
            <a href="/panel/settings/" type="button" class=" btn btn-warning"><i class="icon-cog5 mr-2"></i> Настройки аккаунта</a>


        </div>


        <ul class="list-group  border-top">

            <a href="#" class="list-group-item list-group-item-action">
                <span class="font-weight-semibold">
										<i class="icon-grid mr-2"></i>
										Рейтинг
									</span>
                <span class="badge bg-success ml-auto">0</span>
            </a>


            <a href="#" class="list-group-item list-group-item-action">
									<span class="font-weight-semibold">
										<i class="icon-grid mr-2"></i>
										Всего звонков
									</span>
                <span class="badge bg-success ml-auto">0</span>
            </a>


            <a href="#" class="list-group-item list-group-item-action">
									<span class="font-weight-semibold">
										<i class="icon-grid mr-2"></i>
										Успешных
									</span>
                <span class="badge bg-success ml-auto">0</span>
            </a>


        </ul>


    </div>


</div>