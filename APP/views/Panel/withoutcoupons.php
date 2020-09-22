<div class="card">
    <div class="card-header bg-dark text-white header-elements-inline">
        <h5 class="card-title">ДОБАВЛЕНИЕ КУПОНА</h5>

    </div>

    <div class="card-body">

    <?php
        foreach ($companywithoutcoupons as $key=>$company){

            echo $company['name']."<br>";
        }


    ?>







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