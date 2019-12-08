/**
 * @home.js
 * @author Jia Shi(js11182@nyu.edu)
 */

(function($) {
	var url = {
		getTypes: 'api/getTypes',
		sendMsgToOne: 'api/sendMsgToOnePerson',
		sendMsgToAll: 'api/sendMsgToAll'
	};

	var homepage = {
		init: function () {
			this.initType();
			this.bind();
		},

		bind: function () {
			this.typeChange();
			this.sendMsg();
		},

		initType: function () {
			$.ajax({
				url: url.getTypes,
				method: 'GET',
				dataType: 'json',
				data: {},
				success: function (res) {
					if (res.status == 0) {
						var $types = $('#inputSelectType');
						var template = '<option selected>Choose to send to friends, neighbors or block</option>';
						
						$.each(res.data, function(i , v) {
							template += '<option value="' + v.tid + '">' + v.type_name + '</option>';
						});

						$types.empty().append(template);

					} else {
						alert(res.message);
					}
				},
				error: function(e) {
					alert('HTTP request error!');
				}

			});
		},

		sendMsg: function () {
			$('#sendMsgBtn').click(function() {
				var tid = $('#inputSelectType').val();
				var thisUrl = url.sendMsgToAll;

				if (tid == '1' || tid == '2') {
					thisUrl = url.sendMsgToOne;
				}
				
				var params = $('#sendMsgForm').serialize();

				$.ajax({
					url: thisUrl,
					method: 'POST',
					dataType: 'json',
					data: params,
					success: function(res) {
						if (res.status == 0) {
							alert(res.message);
							$('#sendMsgForm')[0].reset();
							$('#msgModal').modal('hide');
						} else {
							alert(res.message);
						}
					},
					error: function (e) {
						alert('HTTP request error!');
					}
				})
				
			});
		},

		typeChange: function () {
			$('#inputSelectType').change(function() {
				var $receiverRow = $('#receiverRow');
				var typeId = $(this).val();
				if (typeId == '1' || typeId == '2') {
					$receiverRow.removeClass('hide');
					$('#inputReceiver').attr('required', true);
				} else {
					$receiverRow.addClass('hide');
					$('#inputReceiver').removeAttr('required');
				}
			});
		}
	};

	$(function () {
		homepage.init();
	})
})(window.jQuery);