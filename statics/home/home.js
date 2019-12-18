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
		availFriend: 'api/availableFriend',
		listNeighbor: 'api/getNeighborInfo',
		addOrCancelNeighbor: 'api/addNeighbor',
		acceptOrCancelFriend: 'api/acceptFriend',
		availNeighbor: 'api/avaNeighbor',
		listblock: 'api/getBlockInfo',
		showblock: 'api/checkInBlock',
		joinBlock: 'api/joinBlock',
		leaveBlock: 'api/leaveBlock',
		getProfile: 'api/getProfile',
		parseLoc: 'api/parseLocToAddr',
		getUserInBlock: 'api/getUserInBlock',
		search: 'api/searchKeywords',
		getNoti: 'api/getNotification',
		accOrDenyJoinBlock: 'api/updateBlock',
		updateProfile: 'api/updateProfile',
		unreadNum: 'api/numMessage',
		updateMsgToRead: 'api/msgToRead'
	};

	var homepage = {
		init: function () {
			this.initType();
			$('#msgTplContainer').empty();
			this.initNoti();
			this.initUnreadNum();
			this.getFeed(url.getFriendFeed, 0);
			this.getFeed(url.getNeighborFeed, 0);
			this.getFeed(url.getBlockFeed, 0);
			this.getFeed(url.getHoodFeed, 0);
			this.initBlockMap();
			this.bind();
			this.addFriends();
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
			this.search();
			this.showSide();
			this.showNoti();
			this.accOrCancelFriend();
			this.addOrCancelNeighbor();
			this.accOrDenyJoinBlock();
			this.editIntro();
			this.showUnreadMsg();
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

		initMsg: function (res, isEmpty=false) {
			var bt=baidu.template;
			var html = bt('msgTpl', res);
			if (!isEmpty) {
				$('#msgTplContainer').append(html);
			} else {
				$('#msgTplContainer').empty().append(html);
			}
		},

		initNoti:function () {
			$.ajax({
				url: url.getNoti,
				method: 'GET',
				dataType: 'json',
				success: function (res) {
					// add num
					var totalNum = res.data.totalNum;
					if (totalNum > 0) {
						$('#notiNum').removeClass('hide').html(totalNum);
					}
					var bt = baidu.template;
					var html = bt('notiTpl', res.data.notifications);
					$('#notiBox').empty().append(html);
				},
				error: function () {
					alert('HTTP request error!');
				}
			});
		},

		initUnreadNum: function () {
			$.ajax({
				url: url.unreadNum,
				method: 'GET',
				dataType: 'json',
				success: function (res) {
					var totalNum = res.data.num;
					if (totalNum > 0) {
						$('#msgNum').removeClass('hide').html(totalNum);
					}
				},
				error: function (e) {
					alert('HTTP request error!');
				}
			});
		},

		getFeed: function (feedUrl, unread, empty=false, toRead=false) {
			var me = this;
			var params = {};
			params.unread = unread;
			// if (toRead) {
			// 	me.updateMsgToRead();
			// }
			

			$.ajax({
				url: feedUrl,
				method: 'GET',
				dataType: 'json',
				data: params,
				success: function(res) {
					if (res.status == 0) {

						me.initMsg(res, empty);
						
						me.initMapMarker(res.data, 'statics/common/img/christmas-envelop.png', 'title');
						if (toRead) {
							me.updateMsgToRead();
						}
					} else {
						alert(res.message);
					}
				},
				error: function (e) {
					alert('HTTP request error!');
				}
			});
		},

		updateMsgToRead: function () {
			var me = this;
			$.ajax({
				url: url.updateMsgToRead,
				method: 'GET',
				dataType: 'json',
				success: function (res) {
					me.initUnreadNum();
				},
				error: function (e) {
					alert('HTTP request error!');
				}
			});
		},

		initBlockMap: function () {
			var me = this;
			$.ajax({
				url: url.getUserInBlock,
				method: 'GET',
				dataType: 'json',
				success: function (res) {
					if (res.status == 0) {
						me.initMapMarker(res.data, 'statics/common/img/christmas-person.png', 'firstname');
					}
				},
				error: function () {
					alert('HTTP request error!');
				}
			})
		},

		chooseFeed: function() {
			var me = this;
			$('#collapseFeed').delegate('.side-item', 'click', function() {
				var feedType = $(this).data('feed');
				$('#msgTplContainer').empty();

				// show msgBox
				$('.center-box').addClass('hide');
				$('#msgBox').removeClass('hide');
				
				me.getFeed(url[feedType], 0, false, true);
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
							me.getFeed(url.getFriendFeed, 0, false, true);
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

		search: function () {
			var me = this;
			$('#search').click(function(e) {
				e.preventDefault();

				var params = $('#searchForm').serialize();
				$.ajax({
					url: url.search,
					method: 'GET',
					dataType: 'json',
					data: params,
					success: function (res) {
						if (res.status == 0) {
							// show msgBox
							$('.center-box').addClass('hide');
							$('#msgBox').removeClass('hide');
							me.initMsg(res, true);
						} else {
							alert(res.message);
						}
					},
					error: function () {
						alert('HTTP request error!');
					}
				});

			});
		},

		showSide: function() {
			var me = this;
			$('#sidebar').delegate('.show-part', 'click', function () {
				showId = $(this).data('id');
				$('.center-box').addClass('hide');
				$('#' + showId).removeClass('hide');

				if (showId == 'joinBlockBox') {
					me.listBlocks();
				} else if (showId == 'addNeighborBox') {
					me.listNeighbors();
					me.availableNeighbor();
				} else if (showId == 'addFriendBox') {
					me.listFriends();
					me.availableFriend();
				}
			});
		},

		showNoti:function () {
			var me = this;
			$('#showNoti').click(function() {
				$('.center-box').addClass('hide');
				$('#notiBox').removeClass('hide');
				me.initNoti();
			});
		},

		showUnreadMsg: function () {
			var me = this;
			$('#showUnreadMsg').click(function() {
				$('.center-box').addClass('hide');
				$('#msgBox').removeClass('hide');
				me.getFeed(url.getFriendFeed, 1, true, true);
				me.getFeed(url.getNeighborFeed, 1, false);
				me.getFeed(url.getBlockFeed, 1, false);
				me.getFeed(url.getHoodFeed, 1, false);
			});
		},

		initMapMarker: function (data, dotType, titleName) {

			
			infowindow = new google.maps.InfoWindow();

			$.each(data, function(i , v) {
				if (v.lantitude && v.longitude || v.latitude && v.longitude) {
					marker= new google.maps.Marker({
			            position: new google.maps.LatLng(v.lantitude ? v.lantitude : v.latitude, v.longitude),
			            map: map,
			            icon: {
					      url: dotType
					    }
			        });
			        google.maps.event.addListener(marker, 'click', (function(marker, v) {
				        return function() {
				          	infowindow.setContent(v[titleName]);
				          	infowindow.open(map, marker);
				        }
				    })(marker, v));
				}
			});

		},

		showProfile: function () {
			var me = this;
			$('#profileBtn').click(function() {
				$('.center-box').addClass('hide');
				$('#profileBox').removeClass('hide');
				me.initProfile();
			});
		},

		initProfile: function (res) {
			$.ajax({
				url:url.getProfile,
				method: 'GET',
				dataType: 'json',
				success: function (res) {
					var bt = baidu.template;
					var html = bt('profileTpl', res);
					$('#profileBox').empty().append(html);
				},
				error: function () {
					alert('HTTP request error!');
				}
			});
		},

		clickHome: function () {
			var me = this;
			$('#homeNav').click(function() {
				$('.center-box').addClass('hide');
				$('#msgBox').removeClass('hide');
				me.getFeed(url.getFriendFeed, 0, true, true);
				me.getFeed(url.getNeighborFeed, 0, false);
				me.getFeed(url.getBlockFeed, 0, false);
				me.getFeed(url.getHoodFeed, 0, false);
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
						var bt = baidu.template;
						var html = bt('friendTpl', res);
						$('#lstFriends').empty().append(html);
					}
				},
				error: function (e) {
					alert('HTTP request error!');
				}
			});
		},

		accOrCancelFriend: function(){
			var me = this;
			$('#lstFriends').delegate('.cancel-friend-btn', 'click', function () {
				$this = $(this);
				var params = {
					is_valid: -1,
					friend_uid: $this.parents('.friend-container').data('fid')
				};

				me.addOrCancelFriendAjax(params);
			});

			$('#notiBox').delegate('.friend-noti-btn', 'click', function () {
				$this = $(this);
				var params = {
					is_valid: $this.data('isvalid'),
					friend_uid: $this.parents('.message-container').data('fid')
				};

				me.addOrCancelFriendAjax(params);
			});
		},

		addOrCancelFriendAjax: function (params) {
			var me = this;
			$.ajax({
				url: url.acceptOrCancelFriend,
				method: 'POST',
				dataType: 'json',
				data: params,

				success: function (res) {
					if (res.status == 0){
						me.listFriends();
						me.availableFriend();
						me.initNoti();
					}else{
						alert(res.message);
					}
				},
				error: function (e) {
					alert('HTTP request error!');
				}
			});
		},

		availableFriend: function(){
			$.ajax({
				url: url.availFriend,
				method: 'GET',
				dataType: 'json',

				success: function (res){
					if (res.status == 0){
						var bt=baidu.template;
						var html = bt('RecomTpl', res);
						$('#Recommendation').empty().append(html);
					}
				},
				error: function (e){
					alert('HTTP request error!');
				}

			});
		},

		addFriends: function(){
			var me = this;
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
						if (res.status == 0){
							me.listFriends();
							me.availableFriend();
						} else {
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
			var me = this;
			$.ajax({
				url: url.listblock,
				method: 'GET',
				dataType:'json',

				success: function (res){
					if (res.status == 0){
						if(res.data.length>0){
							var bt=baidu.template;
							var html = bt('blockTpl', res);
							$('#lstBlock').empty().append(html);

							me.showBlock();
						}else{
							alert(res.message);
						}}
				},

				error: function (e){
					alert('HTTP request error!');
				}
			});
		},

		showBlock: function (){
			$.ajax({
				url: url.showblock,
				method: 'POST',
				dataType:'json',

				success: function (res){
					if (res.status == 0){
						$('.block-tip').addClass('hide');
						if (res.data.length == 0) {
							$('#leaveBlock').addClass('hide');
							$('#blockTip3').removeClass('hide');
						} else if (res.data[0].is_approved == "0") {
							$('#blockTip2').removeClass('hide');
							$('#leaveBlock').addClass('hide');
						} else {
							$('#blockTip1').removeClass('hide');
						}
						$('#haochen').empty().append(res.data[0].bname).parents('.now-in-block').data('bid', res.data[0].bid);
						$('#blockId' + res.data[0].bid).remove();
					}else{
						alert(res.message);
					}
				},

				error: function (e){
					alert('HTTP request error!');
				}
			});

		},

		joinBlock: function () {
			var me = this;
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
						} else {
							me.listBlocks();
						}
					},
					error: function (e){
						alert('HTTP request error!');
					}
				})
			});
		},

		leaveBlock: function () {
			var me = this;
			$('#leaveBlock').click(function() {
				$this = $(this);
				var params = {
					bid: $this.parents('.now-in-block').data('bid')
				};

				$.ajax({
					url: url.leaveBlock,
					method: 'POST',
					dataType: 'json',
					data: params,

					success: function (res) {
						if (res.status == 0){
							me.listBlocks();
						} else {
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
						var bt=baidu.template;
						var html = bt('neighTpl', res);
						$('#lstNeighbors').empty().append(html);
					}
				},
				error: function (e){
					alert('HTTP request error!');
				}
			});
		},

		addOrCancelNeighbor: function () {
			var me = this;
			// add neighbor in neighbor page
			$('#Recommendation_n').delegate('.add-neighbor-btn','click',function(){
				var $this = $(this);
				var params = {
					neighbor_uid: $this.parents('.neighbor-container').data('uid'),
					is_valid: 1
				};
				me.addOrCancelNeighborAjax(params);
			});

			// add neighbor in notificaition page
			$('#notiBox').delegate('.neighbor-noti-btn', 'click', function () {
				var $this = $(this);
				var params = {
					neighbor_uid: $this.parents('.message-container').data('neighborid'),
					is_valid: $this.data('isvalid')
				};

				me.addOrCancelNeighborAjax(params);
			});

			// cancel neighbor in neighbor page
			$('#lstNeighbors').delegate('.cancel-neighbor-btn','click',function(){
				var $this = $(this);
				var params = {
					neighbor_uid: $this.parents('.neighbor-container').data('nid'),
					is_valid: -1
				};

				me.addOrCancelNeighborAjax(params);
			});
		},

		addOrCancelNeighborAjax: function (params) {
			var me = this;
			$.ajax({
				url: url.addOrCancelNeighbor,
				method: 'POST',
				dataType: 'json',
				data: params,

				success: function (res) {
					if (res.status != 0){
						alert(res.message);
					} else {
						me.initNoti();
						me.listNeighbors();
						me.availableNeighbor();
					}
				},
				error: function (e){
					alert('HTTP request error!');
				}
			});
		},

		accOrDenyJoinBlock: function (params) {
			var me = this;
			$('#notiBox').delegate('.acc-deny-join-btn', 'click', function() {
				var $this = $(this);
				var params = {
					joinid: $this.parents('.message-container').data('joinid'),
					is_agree: $this.data('isvalid')
				};

				$.ajax({
					url: url.accOrDenyJoinBlock,
					method: 'POST',
					dataType: 'json',
					data: params,
					success: function (res) {
						if (res.status == 0){
							me.initNoti();
						} else {
							alert(res.message);
						}
					},
					error: function () {
						alert('HTTP request error!');
					}

				});
			});
		},

		availableNeighbor: function(){
			$.ajax({
				url: url.availNeighbor,
				method: 'GET',
				dataType: 'json',

				success: function (res){
					if (res.status == 0){
						var bt=baidu.template;
						var html = bt('RecomTpl_n', res);
						$('#Recommendation_n').empty().append(html);
					}
				},
				error: function (e){
					alert('HTTP request error!');
				}

			});
		},

		editIntro: function () {
			var me = this;
			$('#profileBox').delegate('.edit-intro', 'click', function() {
				var $this = $(this);
				var eleId = $this.data('id');
				$this.parents('.profile-card').find('textarea').removeClass('hide').attr('disabled', false);
				$this.parents('.profile-card').find('.submit-intro').removeClass('hide');
			});

			$('#profileBox').delegate('.submit-intro', 'click', function() {
				var $this = $(this);
				var eleId = $this.data('id');


				var params = {};
				params[$this.data('param')] = $('#' + eleId).val();

				$.ajax({
					url: url.updateProfile,
					method: 'POST',
					dataType: 'json',
					data: params,
					success: function (res) {
						if (res.status == 0) {
							me.initProfile();
						} else {
							alert(res.message);
						}
					}, error: function (e) {
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

