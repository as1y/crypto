<div class="card">
    <div class="card-header bg-dark text-white header-elements-inline">
        <h5 class="card-title">ДОБАВЛЕНИЕ КУПОНА</h5>

    </div>

    <div class="card-body">


        <form action="/panel/addcoupon/" method="post" data-fouc>

                <div class="row">

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>КОМПАНИЯ: <span class="text-danger">*</span> </label>
                            <select name="company"  data-placeholder="Выберете компанию" class="form-control select-search required" data-fouc>
                                <option></option>

                                <?php foreach ($ADDINFO['companies'] as $key=>$val):?>
                                    <option value="<?=$val['id']?>"><?=$val['name']?></option>
                                <?php endforeach;?>

                            </select>
                        </div>
                    </div>


                    <div class="col-md-6">
                        <div class="form-group">
                            <label>КАТЕГОРИЯ КУПОНА: <span class="text-danger">*</span> </label>
                            <select name="category"  data-placeholder="Выберете категорию" class="form-control select-search required" data-fouc>
                                <option></option>

                                <?php foreach ($ADDINFO['categorycoupons'] as $key=>$val):?>
                                    <option value="<?=$val['id']?>"><?=$val['name']?></option>
                                <?php endforeach;?>

                            </select>
                        </div>
                    </div>



                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Название: <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control required" placeholder="Скидки до 90% на детские товары!">
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group">
                            <label>СКИДКА (со знаком % или Рубля): <span class="text-danger">*</span></label>
                            <input type="text" name="discount" value="NULL" class="form-control required" placeholder="10">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>ПРОМОКОД: <span class="text-danger">*</span></label>
                            <input type="text" name="promocode" value="NULL"  class="form-control required" placeholder="НЕ НУЖЕН">
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Описание: <span class="text-danger">*</span></label>
                            <input type="text" name="short_name" class="form-control required" placeholder="Скидки до 90% на детские товары!">
                        </div>
                    </div>




                        <div class="col-md-12">
                            <div class="form-group">
                                <label>URL рекламируемого сайта (заполнять если страница отличается от главной): <span class="text-danger">*</span></label>
                                <input type="text" name="url" class="form-control required" value="NULL" placeholder="https://kupivip.ru">
                            </div>
                        </div>







                </div>



<hr>


            <button type="submit" class="btn btn-danger btn-labeled btn-labeled-left btn-lg">
                <b><i class="icon-notification2"></i></b>
                ПОЕХАЛИ!
            </button>


        </form>







    </div>



</div>


<script>


    function generatekeywords() {

        let  company = "";
        let coupon = "";

        company = $('select[name=company]').val();
        coupon = $('select[name=coupon]').val();

        str =  '&company=' + company + '&coupon=' + coupon + '&type=generatekeywords';

        $.ajax(
            {
                url : "/panel/addflow/",
                type: 'POST',
                data: str,
                cache: false,
                success: function( keywords ) {

                    $("#keywords").val(keywords);


                }
            }
        );




    }

    function generateads() {

        let  company = "";
        let keywords = "";
        let traffictype = "";

        traffictype = $('select[name=traffictype]').val();

        company = $('select[name=company]').val();
        keywords = $('#keywords').val();

        if (company === ""){
            alert("Выберите компанию");
            return false;
        }

        if (traffictype === ""){
            alert("Выберите типа трафика");
            return false;
        }


        if (keywords === ""){
            alert("Заполните ключевые слова");
            return false;
        }




        str =  '&company=' + company + '&keywords=' + keywords + '&traffictype=' + traffictype + '&type=generateads';


        $.ajax(
            {
                url : "/panel/addflow/",
                type: 'POST',
                data: str,
                cache: false,
                success: function( ads ) {

                    $("#ads").append(ads);
                    $("#go").show();

                }
            }
        );




    }




</script>