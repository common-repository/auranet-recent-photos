jQuery(document).ready(function($){
		$('.slider').on('keydown, change', function(){
			var val = this.value;
			$('#val_'+this.id).html(val);
			$('input[name="aura_recent_photos_img_'+this.id+'"]').val(val);
		});	
	});
