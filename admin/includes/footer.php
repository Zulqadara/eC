</div>
	
	<footer class="text-center" id="footer">&copy; Copyright 2017 Ecom Website</footer>

	<!-- Listner to listen to selected option, which will fire the get_child_options function which will get the id and post it to the child_categories 
	page the variable will be called parentID and on success the options with id child will be changed -->
	<script>
		function get_child_options(selected){
			var parentID = jQuery('#parent').val();
			if(typeof selected === 'undefined'){
				var selected = '';
				}
			jQuery.ajax({
				url: '/ecom/admin/parsers/child_categories.php',
				type: 'POST',
				data: {parentID:parentID, selected:selected},
				success: function(data){
					jQuery('#child').html(data);
				},
				error: function(){alert("Something went wrong with the child options.")},
			});
		}
		jQuery('select[name="parent"]').change(function(){
			get_child_options();
		});
	</script>
	
	<script>
		function updatesize(){
			var sizeString = '';
			for(var i=1; i<=12; i++ ){
				if(jQuery('#size'+i).val()!=''){
					sizeString += jQuery('#size'+i).val()+':'+jQuery('#qty'+i).val()+':'+jQuery('#threshold'+i).val()+',';
				}
			}
			jQuery('#sizes').val(sizeString);
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