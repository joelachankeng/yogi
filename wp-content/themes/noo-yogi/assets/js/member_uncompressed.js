;(function($){
    "use strict";
	$(document).ready(function(){
		$('form#noo-ajax-login-form').on('submit', function (e) {
			e.stopPropagation();
			e.preventDefault();
			var _this = $(this);
			_this.find('.noo-ajax-result').show().html(nooMemberL10n.loadingmessage);
			$.ajax({
                type: 'POST',
                dataType: 'json',
                url: nooMemberL10n.ajax_url,
                data: {
                    action: 'noo_ajax_login',
                    log: _this.find('#log').val(),
                    pwd: _this.find('#pwd').val(),
                    remember: (_this.find('#rememberme').is(':checked') ? true : false),
                    security: _this.find('#security').val(),
                    redirect_to: _this.find('#redirect_to').length ? _this.find('#redirect_to').val() : ''
                },
                success: function (data) {
                	_this.find('.noo-ajax-result').show().html(data.message);
                    if (data.loggedin == true) {
                        if (data.redirecturl == null) {
                            document.location.reload();
                        }
                        else {
                            document.location.href = data.redirecturl;
                        }
                    }
                },
                complete: function () {

                },
                error: function () {
                	_this.off('submit');
                	_this.submit();
                }
			});
		});
        $('form#noo-ajax-register-form').on('submit', function (e) {
            e.stopPropagation();
            e.preventDefault();
            var _this = $(this);
            if(_this.find("#account_reg_term").length && !_this.find("#account_reg_term").is(':checked')){
                _this.find("#account_reg_term").tooltip('show');
                _this.find('.noo-ajax-result').hide();
                return false;
            }else{
                _this.find("#account_reg_term").tooltip('hide');
                _this.find('.noo-ajax-result').show().html(nooMemberL10n.loadingmessage);
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: nooMemberL10n.ajax_url,
                    data: {
                        action: 'noo_ajax_register',
                        user_login: _this.find('#user_login').val(),
                        user_email: _this.find("#user_email").val(),
                        user_password: _this.find("#user_password").val(),
                        cuser_password: _this.find("#cuser_password").val(),
                        security: _this.find('#security').val(),
                        user_role: _this.find('#user_role').val()
                    },
                    success: function (data) {
                        _this.find('.noo-ajax-result').show().html(data.message);
                        if (data.success == true) {
                            if (data.redirecturl == null) {
                                document.location.reload();
                            }
                            else {
                                document.location.href = data.redirecturl;
                            }
                        }
                    },
                    complete: function () {
    
                    },
                    error: function () {
                        _this.off('submit');
                        _this.submit();
                    }
                });
            }
        });
		$('form#noo-ajax-subscribe-form').on('submit', function (e) {
			e.stopPropagation();
			e.preventDefault();
			var _this = $(this);
			if(_this.find("#account_reg_term").length && !_this.find("#account_reg_term").is(':checked')){
				_this.find("#account_reg_term").tooltip('show');
				_this.find('.noo-ajax-result').hide();
				return false;
			}else{
				_this.find("#account_reg_term").tooltip('hide');
				_this.find('.noo-ajax-result').show().html(nooMemberL10n.loadingmessage);

                var data = _this.serializeArray();
				$.ajax({
	                type: 'POST',
	                dataType: 'json',
	                url: nooMemberL10n.ajax_url,
	                data: data,
	                success: function (data) {
	                	_this.find('.noo-ajax-result').show().html(data.message);
	                    if (data.success == true) {
	                        if (data.redirecturl == null) {
	                            document.location.reload();
	                        }
	                        else {
	                            document.location.href = data.redirecturl;
	                        }
	                    }
	                },
	                complete: function () {
	
	                },
	                error: function () {
	                	_this.off('submit');
	                	_this.submit();
	                }
				});
			}
		});
	});
})(jQuery);