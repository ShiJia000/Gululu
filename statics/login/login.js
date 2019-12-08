/**
 * @login.js
 * @author Jia Shi(js11182@nyu.edu)
 */

(function($) {
	var url = {
		login: 'api/signIn'
	};

	var login = {
		init: function () {
			this.clickSignIn();
		},

		clickSignIn: function () {
			$('#signInBtn').click(function(e) {
				e.preventDefault();
				var params = $('#signInForm').serialize();

				$.ajax({
					url: url.login,
					method: 'POST',
					dataType: 'json',
					data: params,
					success: function (res) {
						if (res.status == 0) {
							$.cookie('uid', res.data.uid, {expires: 1});
							window.location.replace("home");
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
		login.init();
	})
})(window.jQuery);