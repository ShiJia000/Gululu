/**
 * @home.js
 * @author Jia Shi(js11182@nyu.edu)
 */

(function($) {
	var url = {
		getTypes: 'api/getTypes',
		sendMsgToOne: 'api/sendMsgToOnePerson',
		sendMsgToAll: 'api/sendMsgToAll',
		getFriendFeed: 'api/getFriendFeed',
		getBlockFeed: 'api/getBlockFeed',
		getHoodFeed: 'api/getHoodFeed',
		getNeighborFeed: 'api/getNeighborFeed',
		sendReply: 'api/sendReply',
		signOut: 'api/signOut',
		listFriends: 'api/getFriendInfo',
		addFriend: 'api/addFriend',
		cancelFriend: 'api/acceptFriend',
		availFriend: 'api/availableFriend',
		listNeighbor: 'api/getNeighborInfo',
		addNeighbor: 'api/addNeighbor',
		availNeighbor: 'api/avaNeighbor',
		listblock: 'api/getBlockInfo',
		joinBlock: 'api/joinBlock',
		leaveBlock: 'api/leaveBlock',
		getProfile: 'api/getProfile',
		parseLoc: 'api/parseLocToAddr'

	};

	var homepage = {
		init: function () {
			this.initType();
			$('#msgTplContainer').empty();
			this.getFeed(url.getFriendFeed, 0);
			this.getFeed(url.getNeighborFeed, 0);
			this.getFeed(url.getBlockFeed, 0);
			this.getFeed(url.getHoodFeed, 0);
			this.initMapMarker();
			this.bind();
			this.listFriends();
			this.cancelFriends();
			this.addFriends();
			this.availableFriend();
			this.listNeighbors();
			this.cancelNeighbor();
			this.availableNeighbor();
			this.addNeighbor();
			this.listBlocks();
			this.joinBlock();
			this.leaveBlock();
		},

		bind: function () {
			this.typeChange();
			this.sendMsg();
			this.chooseFeed();
			this.sendReply();
			this.showProfile();
			this.clickHome();
			this.addLocation();
			this.signOut();
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

		// the select dropdown menu of friends
		initReceiver: function (typeId) {
			var thisUrl = '';
			var uidName = '';
			if (typeId == '1') {
				thisUrl = url.listFriends;
				uidName = 'friend_id';
			} else {
				thisUrl = url.listNeighbor;
				uidName = 'neighbor_id';
			}

			$.ajax({
				url: thisUrl,
				method: 'GET',
				dataType: 'json',
				success: function (res) {
					if (res.status == 0) {
						var $receiver = $('#inputReceiver');
						var template = '<option selected>Choose the person</option>';
							
						$.each(res.data, function(i , v) {
							template += '<option value="' + v[uidName] + '">' + v.firstname + ' ' + v.lastname + '</option>';
						});

						$receiver.empty().append(template);
					} else {
						alert(res.message);
					}
				},
				error: function () {
					alert('HTTP request error!');
				}
			});
		},

		initMsg: function (res) {
			if (res.data.length > 0) {
				var bt=baidu.template;
				var html = bt('msgTpl', res);
				$('#msgTplContainer').append(html);
			}
		},

		getFeed: function (feedUrl, unread) {
			var me = this;
			var params = {};
			params.unread = unread;

			$.ajax({
				url: feedUrl,
				method: 'GET',
				dataType: 'json',
				data: params,
				success: function(res) {
					if (res.status == 0) {
						me.initMsg(res);
					} else {
						alert(res.message);
					}
				},
				error: function (e) {
					alert('HTTP request error!');
				}
			});
		},

		chooseFeed: function() {
			var me = this;
			$('#collapseFeed').delegate('.side-item', 'click', function() {
				var feedType = $(this).data('feed');
				$('#msgTplContainer').empty();

				// show msgBox
				$('.center-box').addClass('hide');
				$('#msgBox').removeClass('hide');
				
				me.getFeed(url[feedType], 0);
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
				params += '&lat=' + $('.location').data('lat');
				params += '&lng=' + $('.location').data('lng');
				params += '&addr=' + $('.location').data('addr');

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

		sendReply: function () {
			var me = this;
			$('#msgTplContainer').delegate('.send-reply', 'click', function() {
				var $this = $(this);
				var $replyText = $this.parents('.reply-input').find('.reply-text');
				var mid = $this.parents('.message-container').data('mid');
				var content = $replyText.val();
				var params = {
					mid: mid,
					content: content
				}

				$.ajax({
					url: url.sendReply,
					method: 'POST',
					dataType: 'json',
					data: params,
					success: function (res) {
						if (res.status == 0) {
							$replyText.val('');
							$('#msgTplContainer').empty();
							me.getFeed(url.getFriendFeed, 0);
							me.getFeed(url.getNeighborFeed, 0);
							me.getFeed(url.getBlockFeed, 0);
							me.getFeed(url.getHoodFeed, 0);
							alert(res.message);
						} else {
							alert(res.message);
						}
					},
					error: function (e) {
						alert('HTTP request error!');
					}
				});
			});
		},

		typeChange: function () {
			var me = this;
			$('#inputSelectType').change(function() {
				var $receiverRow = $('#receiverRow');
				var typeId = $(this).val();
				if (typeId == '1' || typeId == '2') {
					$receiverRow.removeClass('hide');
					$('#inputReceiver').attr('required', true);
					
					// init receivers
					me.initReceiver(typeId);
					
				} else {
					$receiverRow.addClass('hide');
					$('#inputReceiver').removeAttr('required');
				}
			});
		},

		signOut: function () {
			$('#signOut').click(function () {
				$.ajax({
					url: url.signOut,
					method: 'POST',
					dataType: 'json',

					success: function (res) {
						if (res.status == 0) {
							$.cookie('uid', null);
							window.location.replace('login');
						} else {
							alert(res.message);
						}
					},
					error: function (e) {
						alert('HTTP request error!');
					}
				});
			});
		},

		initMapMarker: function () {
			var markersArray = [];
			var latLng = new google.maps.LatLng(40.690750, -73.983820);
			// var tooltip = "some text";
			var marker;
			marker= new google.maps.Marker({
	            position: latLng,
	            map: map
	            // title:tooltip
	        });
			marker= new google.maps.Marker({
	            position: latLng,
	            map: map,
	            icon: {
			      url: "http://maps.google.com/mapfiles/ms/icons/pink-dot.png"
			    }
	            // title:tooltip
	        });
			var infowindow = new google.maps.InfoWindow();
			google.maps.event.addListener(marker, 'click', (function(marker, i) {
		        return function() {
		          infowindow.setContent("hahahahahah");
		          infowindow.open(map, marker);
		        }
		      })(marker));

		},

		showProfile: function () {
			$('#profileBtn').click(function() {
				$('.center-box').addClass('hide');
				$('#profileBox').removeClass('hide');
				$.ajax({
					url:url.getProfile,
					method: 'GET',
					dataType: 'json',
					success: function (res) {
						var bt = baidu.template;
						var html = bt('profileTpl', res);
						$('#profileBox').append(html);
					},
					error: function () {
						alert('HTTP request error!');
					}
				});
			});
		},

		clickHome: function () {
			var me = this;
			$('#homeNav', '').click(function() {
				$('.center-box').addClass('hide');
				$('#msgBox').removeClass('hide');
				me.getFeed(url.getFriendFeed, 0);
				me.getFeed(url.getNeighborFeed, 0);
				me.getFeed(url.getBlockFeed, 0);
				me.getFeed(url.getHoodFeed, 0);
			});
		},

		addLocation: function () {
			$('#addLocation').click(function() {
				navigator.geolocation.getCurrentPosition(function(position) {
					var params = {
	                    lat: position.coords.latitude,
	                    lng: position.coords.longitude
	                };

	                $.ajax({
	                	url: url.parseLoc,
	                	method: 'GET',
	                	dataType: 'json',
	                	data: params,
	                	success: function(res) {
	                		if (res.status == 0) {
	                			$location = $('#addLocation').find('.location');
	                			$location.empty().append(res.data);
	                			$location.data('lat', params.lat);
	                			$location.data('lng', params.lng);
	                			$location.data('addr', res.data);
	                		} else {
	                			alert('get location error!');
	                		}
	                	},
	                	error: function () {
	                		alert('HTTP request error!');

	                	}
	                })
				});
				

			});
		},
		
		listFriends: function () {
			$.ajax({
				url: url.listFriends,
				method: 'GET',
				dataType: 'json',

				success: function (res) {
					if (res.status == 0){
						if(res.data.length>0){
							var bt = baidu.template;
							var html = bt('friendTpl', res);
							$('#lstFriends').append(html);
						}
					}else{
						// alert(res.message);
					}
				},
				error: function (e) {
					alert('HTTP request error!');
				}
			});
		},

		cancelFriends: function(){
			$('#lstFriends').delegate('.cancel-friend-btn', 'click', function () {
				$this = $(this);
				var params = {
					is_valid: -1,
					friend_uid: $this.parents('.friend-container').data('fid')
				};

				$.ajax({
					url: url.cancelFriend,
					method: 'POST',
					dataType: 'json',
					data:params,

					success: function (res) {
						if (res.status == 0){
							pass;
						}else{
							alert(res.message);
						}
					},
					error: function (e) {
						alert('HTTP request error!');
					}
				})
			});
		},

		availableFriend: function(){
			$.ajax({
				url: url.availFriend,
				method: 'GET',
				dataType: 'json',

				success: function (res){
					if (res.status == 0){
						if(res.data.length>0){

							var bt=baidu.template;
							var html = bt('RecomTpl', res);
							$('#Recommendation').append(html);
						}
					}else{
						// alert(res.message);
					}
				},
				error: function (e){
					alert('HTTP request error!');
				}

			});
		},

		addFriends: function(){
			$('#Recommendation').delegate('.add-friend-btn','click',function(){
				$this = $(this);
				var params = {
					friend_uid: $this.parents('.friend-container').data('uid')
				};

				$.ajax({
					url: url.addFriend,
					method: 'POST',
					dataType: 'json',
					data: params,

					success: function (res){
						if (res.status != 0){
							alert(res.message);
						}
					},
					error: function (e){
						alert('HTTP request error!');
					}
				})
			});
		},

		listBlocks: function (){
			$.ajax({
				url: url.listblock,
				method: 'GET',
				dataType:'json',

				success: function (res){
					if (res.status == 0){
						if(res.data.length>0){

							var bt=baidu.template;
							var html = bt('blockTpl', res);
							$('#lstBlock').append(html);	
						}else{
							alert(res.message);
						}}
				},

				error: function (e){
					alert('HTTP request error!');
				}
			});
		},

		joinBlock: function () {
			$('#lstBlock').delegate('.join-block-btn','click',function(){
				$this = $(this);
				var params = {
					bid:$this.parents('.block-container').data('bid')
				};

			$.ajax({
				url: url.joinBlock,
				method: 'POST',
				dataType: 'json',
				data: params,

				success: function (res) {
					if (res.status != 0){
						alert(res.message);
					}
				},
				error: function (e){
					alert('HTTP request error!');
				}
			})
		});
		},

		leaveBlock: function () {
			$('#lstBlock').delegate('.leave-block-btn','click',function(){
				$this = $(this);
				var params = {
					bid:$this.parents('.block-container').data('bid')
				};

			$.ajax({
				url: url.leaveBlock,
				method: 'POST',
				dataType: 'json',
				data: params,

				success: function (res) {
					if (res.status != 0){
						alert(res.message);
					}
				},
				error: function (e){
					alert('HTTP request error!');
				}
			})
		});
		},

		listNeighbors: function () {
			$.ajax({
				url: url.listNeighbor,
				method: 'GET',
				dataType: 'json',

				success: function (res){
					if (res.status == 0){
						if(res.data.length>0){

							var bt=baidu.template;
							var html = bt('neighTpl', res);
							$('#lstNeighbors').append(html);	

						}else{
							alert(res.message);
						}}
					},
					error: function (e){
						alert('HTTP request error!');
					}
			});
		},

		cancelNeighbor: function () {
			$('#lstNeighbors').delegate('.cancel-neighbor-btn','click',function(){
				$this = $(this);
				var params = {
					neighbor_uid: $this.parents('.neighbor-container').data('nid'),
					is_valid: -1
				};

			$.ajax({
				url: url.addNeighbor,
				method: 'POST',
				dataType: 'json',
				data: params,

				success: function (res) {
					if (res.status != 0){
						alert(res.message);
					}
				},
				error: function (e){
					alert('HTTP request error!');
				}
			})
		});
		},

		availableNeighbor: function(){
			$.ajax({
				url: url.availNeighbor,
				method: 'GET',
				dataType: 'json',

				success: function (res){
					if (res.status == 0){
						if(res.data.length>0){

							var bt=baidu.template;
							var html = bt('RecomTpl_n', res);
							$('#Recommendation_n').append(html);
						}
					}else{
						// alert(res.message);
					}
				},
				error: function (e){
					alert('HTTP request error!');
				}

			});
		},

		addNeighbor: function () {
			$('#Recommendation_n').delegate('.add-neighbor-btn','click',function(){
				$this = $(this);
				var params = {
					neighbor_uid: $this.parents('.neighbor-container').data('uid'),
					is_valid: 1
				};

				$.ajax({
					url: url.addNeighbor,
					method: 'POST',
					dataType: 'json',
					data: params,

					success: function (res) {
						if (res.status != 0){
							alert(res.message);
						}
					},
					error: function (e){
						alert('HTTP request error!');
					}
				});
			});
		}
	};

	$(function () {
		homepage.init();
	});
})(window.jQuery, window.google);

