<?php
	
	namespace Common;

	/**
	 * Class Common\Dialog
	*/
	class Dialog{
		

		public function showDialog($text) {
			$id = $this->randomPassword(10);
			$dialog = "<dialog open id=\"{$id}\"><p>{$text}</p><button class=\"closeButton\" autofocus>Bezár</button></dialog>";
			
			return "
				<script>
					$('body').append('{$dialog}');
					$('body').on('click', '.closeButton', function() {
						var dialogId = $(this).closest('dialog').attr('id');
						var dialog = $('#' + dialogId);
						dialog.remove();
					});
				</script>
			";
		}
	}