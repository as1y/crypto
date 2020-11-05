
var arrBrands = [];
var arrType = "";
var arrCategory = "";


function subscribecode(idcoupon) {

    let email = $('[name=emailcode]').val();

    str =  '&email=' + email + '&type=send' + '&idcoupon=' + idcoupon;


    $.ajax(
        {
            url : /subscribe/,
            type: 'POST',
            data: str,
            cache: false,
            success: function(subs ) {

                $('#modalbody').empty();
                $('#modalbody').append('<h5>Промокод отправлен на e-mail!</h5>');

                // $('#modalbody').append(subs);



            }
        }
    );





}

function subscribefooter() {

    let email = $('[name=email]').val();
    str =  '&email=' + email + '&type=footer';

    $.ajax(
        {
            url : /subscribe/,
            type: 'POST',
            data: str,
            cache: false,
            success: function(subs ) {

                $('[name=subscribemodal]').modal('show');
                $('[name=email]').val("");

            }
        }
    );


}

function ChangeFilter() {
    str = getFilterParamsParams();

    let addr;
    getparam = getUrlParams();

    // Конструктор URL

    // Если выбран только бренд

    if (arrBrands != "" && arrCategory == ""){
        addr = '/promocode/' + arrBrands;
    }

    if (arrBrands != "" && arrCategory != ""){
        addr = '/promocode/' + arrBrands + '/' + arrCategory;
    }

    if (arrBrands == "" && arrCategory != ""){
        addr = '/promocode/vse/' + arrCategory;
    }

    if (arrBrands == "" && arrCategory == ""){
        addr= '/promocode/vse/';
    }


    if (arrType != ""){

        addr = addr + "?type=" + arrType;

    }

    window.location.href = addr;

    return true;

}

function changePage(page){


    str = getFilterParamsParams ();


    $('#CouponContainer').empty();

    $.ajax(
        {
            url : document.location.pathname,
            type: 'POST',
            data: str + '&page=' + page,
            cache: false,
            success: function( coupons ) {

                $('#CouponContainer').append(coupons);
                window.scrollTo(0, 0);


            }
        }
    );


}

function clck(couponid)
{
    // // Устанавливаем промокод в сессиию
    document.cookie = "runmodal="+couponid;
    window.open("#");
}


function getFilterParamsParams() {
    arrBrands = "";
    arrType = "";
    arrCategory = "";

    arrBrands = $('select[name=companies]').val();
    arrType = $('select[name=type]').val();
    arrCategory = $('select[name=category]').val();
    str =  '&arrBrands=' + arrBrands + '&arrType=' + arrType + '&arrCategory=' + arrCategory;
    return str;

}

function getUrlParams(url = location.search){
    var regex = /[?&]([^=#]+)=([^&#]*)/g, params = {}, match;
    while(match = regex.exec(url)) {
        params[match[1]] = match[2];
    }
    return params;
}

 


