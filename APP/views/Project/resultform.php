<div class="row">



</div>





<div class="card">
    <div class="card-header header-elements-inline">
        <h5 class="card-title">ТЕКУЩАЯ ФОРМЫ РЕЗУЛЬТАТА</h5>
        <div class="header-elements">
            <div class="list-icons">
                <a class="list-icons-item" data-action="collapse"></a>
                <a class="list-icons-item" data-action="reload"></a>
            </div>
        </div>
    </div>


    <div class="row">
       <div class="card-body justify-content-center">




           <?php


           $company['formresult'] = '[{"NAME":"ИМЯ","TYPE":1}]';



           $FORMRESULT = json_decode($company['formresult'],TRUE);


           show($FORMRESULT);


           renderform();



           //    showmass($MASS, 1);


           ?>






    </div>





</div>





</div>




<script>

 
   $("#clone").click(function(){	 	
 	var NAME = $("#NAME").val();
 	var TYPE = $("#TYPE").val();
	var url = 'wform';
	var name = 'addpole';
	var idc = <?=$idc?>;
 	var bd = $('[title = "rezt"]').length;
 	
 	
 	if (NAME.length < 2 ) {
	alert('В поле должно быть более 2-х символов');
	return;
	}
		
var str = '&idc=' + idc + '&NAME=' + NAME + '&TYPE=' + TYPE

	$.ajax(

	{
		url : '/' + url,
		type: 'POST',
		data: name + '_f=1' + str,
		cache: false,
		success: function( result ) {

			obj = jQuery.parseJSON( result );
		
			if ( obj.message == "done" ){
		$("#NAME").val('');
				$("#formb").append('<div id="polosa'+bd+'"  ><label class="col-md-3 control-label">'+NAME+'<span class="required" aria-required="true"> * </span></label><div class="form-inline"><input title="rezt" name="'+bd+'" ttype="'+TYPE+'" tname="'+NAME+'" readonly="" type="text" class="form-control" placeholder="'+NAME+'" maxlength="50"><button class="btn btn-danger" kto="'+bd+'"  onclick="delc('+bd+')"><i class="fa fa-trash"></i></button></div></div>');
				
			} else {
				alert( obj.message );
			}


	}


	}

	);
	
	
	
		
	

	
	
	
	
		
	});


function delc(kto){
	
  var bd = $('[title = "rezt"]').length;
  var url = 'wform';
  var name = 'delpole';
  var idc = <?=$idc?>;
   
  if (bd < 2){
  	  	alert('Должно остатся хотябы 1 поле');
	exit();	
  }

var str = '&idc=' + idc + '&kto=' + kto


	
	$.ajax(

	{
		url : '/' + url,
		type: 'POST',
		data: name + '_f=1' + str,
		cache: false,
		success: function( result ) {

			obj = jQuery.parseJSON( result );
		
			if ( obj.message == "done" ){
		
				$("#polosa"+kto).remove();
				
			} else {
				alert( obj.message );
			}
		



	}


	}

	);
	
	
	
	
	
}
	
	


	
</script>


