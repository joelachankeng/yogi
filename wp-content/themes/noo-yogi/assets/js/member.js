!function(a){"use strict";a(document).ready(function(){a("form#noo-ajax-login-form").on("submit",function(b){b.stopPropagation(),b.preventDefault();var c=a(this);c.find(".noo-ajax-result").show().html(nooMemberL10n.loadingmessage),a.ajax({type:"POST",dataType:"json",url:nooMemberL10n.ajax_url,data:{action:"noo_ajax_login",log:c.find("#log").val(),pwd:c.find("#pwd").val(),remember:c.find("#rememberme").is(":checked")?!0:!1,security:c.find("#security").val(),redirect_to:c.find("#redirect_to").length?c.find("#redirect_to").val():""},success:function(a){c.find(".noo-ajax-result").show().html(a.message),1==a.loggedin&&(null==a.redirecturl?document.location.reload():document.location.href=a.redirecturl)},complete:function(){},error:function(){c.off("submit"),c.submit()}})}),a("form#noo-ajax-register-form").on("submit",function(b){b.stopPropagation(),b.preventDefault();var c=a(this);return c.find("#account_reg_term").length&&!c.find("#account_reg_term").is(":checked")?(c.find("#account_reg_term").tooltip("show"),c.find(".noo-ajax-result").hide(),!1):(c.find("#account_reg_term").tooltip("hide"),c.find(".noo-ajax-result").show().html(nooMemberL10n.loadingmessage),a.ajax({type:"POST",dataType:"json",url:nooMemberL10n.ajax_url,data:{action:"noo_ajax_register",user_login:c.find("#user_login").val(),user_email:c.find("#user_email").val(),user_password:c.find("#user_password").val(),cuser_password:c.find("#cuser_password").val(),security:c.find("#security").val(),user_role:c.find("#user_role").val()},success:function(a){c.find(".noo-ajax-result").show().html(a.message),1==a.success&&(null==a.redirecturl?document.location.reload():document.location.href=a.redirecturl)},complete:function(){},error:function(){c.off("submit"),c.submit()}}),void 0)}),a("form#noo-ajax-subscribe-form").on("submit",function(b){b.stopPropagation(),b.preventDefault();var c=a(this);if(c.find("#account_reg_term").length&&!c.find("#account_reg_term").is(":checked"))return c.find("#account_reg_term").tooltip("show"),c.find(".noo-ajax-result").hide(),!1;c.find("#account_reg_term").tooltip("hide"),c.find(".noo-ajax-result").show().html(nooMemberL10n.loadingmessage);var d=c.serializeArray();a.ajax({type:"POST",dataType:"json",url:nooMemberL10n.ajax_url,data:d,success:function(a){c.find(".noo-ajax-result").show().html(a.message),1==a.success&&(null==a.redirecturl?document.location.reload():document.location.href=a.redirecturl)},complete:function(){},error:function(){c.off("submit"),c.submit()}})})})}(jQuery);