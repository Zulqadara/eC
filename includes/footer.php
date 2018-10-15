</div>
	
	<footer class="text-center" id="footer">&copy; Copyright 2017 Ecom Website</footer>

<script>
jQuery(window).scroll(function(){
	var vscroll = jQuery(this).scrollTop();
	jQuery('#logotext').css({
	"transform" : "translate(0px, "+vscroll/2+"px)"	
	});
	jQuery('#back-flower').css({
	"transform" : "translate("+0+vscroll/5+"px, -"+vscroll/12+"px)"
	});
	jQuery('#fore-flower').css({
	"transform" : "translate(0px, -"+vscroll/2+"px)"	
	});
	
});

function detailsmodal(id){
	//"id":id = i want id from function bracket from above and it should be equal to id in quotes like below under
	var data = {"id" : id};
	jQuery.ajax({
		url : '/ecom/includes/detailsmodal.php',
		method : "post",
		data : data,
		success : function(data){
			jQuery('body').prepend(data);
			jQuery('#details-modal').modal('toggle');
		},
		error : function(){
			alert("Something went wrong");
		}
	});
}

function update_cart(mode,edit_id,edit_size){
	var data = {"mode" : mode, "edit_id" : edit_id, "edit_size" : edit_size};
	jQuery.ajax({
		url : '/ecom/admin/parsers/update_cart.php',
		method : "post",
		data : data,
		success : function(){
			location.reload();
		},
		error : function(){alert("Something went wrong");},
	});
}

function add_to_cart(){
	jQuery('#modal_errors').html('');
	var size = jQuery('#size').val();
	var quantity = parseInt(jQuery('#quantity').val());
	//var quantity = jQuery('#quantity').val();
	var available = parseInt(jQuery('#available').val());
	//var available = jQuery('#available').val();
	var error = '';
	var data = jQuery('#add_product_form').serialize(); //Searializse takes values and converts them to GET/POST values
	if(size == '' || quantity == '' || quantity <= 0){
		error += '<p class="text-danger text-center">You must choose a size and quantity</p>';
		jQuery('#modal_errors').html(error);
		return;
	}else if(quantity > available){
		error += '<p class="text-danger text-center">There are only '+ available + ' available</p>';
		jQuery('#modal_errors').html(error);
		return;
	}else{
		jQuery.ajax({
			url : '/ecom/admin/parsers/add_cart.php',
			method : 'post',
			data : data,
			success : function(){
				location.reload();
				
			},
			error : function(){alert("Something went wrong!");}
		});
	}
	
}
</script>
<script>
window.setTimeout(function () {
    $(".alert-success").fadeTo(500, 0).slideUp(500, function () {
        $(this).remove();
    });
}, 1000);
</script>
  </body>
 </html>