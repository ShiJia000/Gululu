
(function($) {
	var url = {
		signup: 'api/signUp'
	};

	var signup = {
		init:function(){
			this.clickSignUp();
		},

		clickSignUp: function(){
			$('#signUpBtn').click(function (e) {
				e.preventDefault();
				var params = $('#signUpForm').serialize();

				$.ajax({
					url: url.signup,
					method: 'POST',
					dataType: 'json',
					data: params,

					success: function (res) {
						if (res.status == 0) {
							window.location.replace("login");
						} else {
							alert(res.message);
						}
					},
					error: function (e) {
						alert('HTTP request error!');
					}

				})

			});
		}
	};

	$(function () {
		signup.init();
	})

})(window.jQuery);