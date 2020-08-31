(function($){
    
    var settings = {
        
        init: function() {
            
            this.basic.init();
            this.social.init();
            this.subscription.init();
            this.lock.init();
            this.notifications.init();
            this.permissions.init();
            this.text.init();    
        },
        
        /** ---
         * Basic Options
         */
        
        basic: {
            
            init: function() {
                

            }
        },
        
        /** ---
         * Social Options
         */
        
        social: {
            
            init: function() {

                var $wrapSocialApps = $("#own_social_apps_wrap");
                var $wrapSigninApps = $("#own_signin_apps_wrap");

                $("#opanda_own_apps_for_permissions").change(function(){

                    if ( $(this).is(":checked") ) $wrapSocialApps.fadeIn();
                    else $wrapSocialApps.fadeOut();

                }).change();

                $("#opanda_own_apps_to_signin").change(function(){

                    if ( $(this).is(":checked") ) $wrapSigninApps.fadeIn();
                    else $wrapSigninApps.fadeOut();

                }).change();

                var linkedFiles = {
                    'opanda_facebook_app_id': {},
                    'opanda_facebook_app_secret': {},
                    'opanda_google_client_id': {},
                    'opanda_google_client_secret': {}
                };

                for ( var prop in linkedFiles ) {
                    if ( !linkedFiles.hasOwnProperty(prop) ) continue;
                    linkedFiles[prop] = $('[name="' + prop + '"]');

                    linkedFiles[prop].on('change', function(){

                        var changed = this;
                        var value = $(this).val();
                        var name = $(this).attr('name');

                        linkedFiles[name].each(function(){
                            var current = this;
                            if ( changed === current ) return;
                            $(this).val(value);
                        });
                    });
                }
            }   
        },
        
        /** ---
         * Subscription Options
         */
        
        subscription: {
            
            init: function() {
                
                $("#opanda_subscription_service").change(function(){
                    
                    var value = $(this).val();
                    var $options = $("#opanda-" + value + "-options");
                    
                    $(".opanda-mail-service-options").hide();
                    $options.fadeIn();
                    
                    if ( 'none' !== value ) {
                        $("#opanda-all-services-options").fadeIn();
                    }
                    
                }).change();
            }  
        },
        
        /** ---
         * Lock Options
         */
        
        lock: {
            
            init: function() {
                var $passcode = $("#opanda_passcode");
                var $passcodeUrl = $(".factory-control-passcode .opanda-url");
                var $passcodeExample = $(".factory-control-passcode .opanda-example");
                
                var checkPasscode = function() {
                    var value = $passcode.val();
                    if ( $.trim( value ) ) $passcodeExample.show();
                    else $passcodeExample.hide();
                };

                checkPasscode();
                
                $("#opanda_passcode").keyup(function(){
                    var value = $.trim( $(this).val() );
                    $(".opanda-passcode").text( '?' + value );
                    $passcodeUrl.attr('href', $passcodeUrl.text());
                    checkPasscode();
                }).keyup();
                
                $("#opanda_in_app_browsers").change(function(){
                    
                    if ( $(this).val() === 'visible_with_warning' ) { 
                        $("#in_app_browsers_warning").fadeIn();
                    } else {
                        $("#in_app_browsers_warning").hide();
                    }
                }).change();
                
                $("#opanda_forbid_temp_emails").change(function(){

                    if ( $(this).is(":checked") ) { 
                        $("#temp_domains_list").fadeIn();
                    } else {
                        $("#temp_domains_list").hide();
                    }
                }).change();                
                
                $("#opanda_adblock").change(function(){
                    
                    if ( $(this).val() === 'show_error' ) { 
                        $("#adblock_error").fadeIn();
                    } else {
                        $("#adblock_error").hide();
                    }
                }).change();  
            }  
        },    
        
        /** ---
         * Notifications Options
         */
        
        notifications: {
            
            init: function() {
               
                $("#opanda_notify_leads").change(function(){
                    
                    if ( $(this).is(":checked") ) { 
                        $("#opanda_notify_leads-options").fadeIn();
                    } else {
                        $("#opanda_notify_leads-options").hide();
                    }
                }).change();
                
                $("#opanda_notify_unlocks").change(function(){
                    
                    if ( $(this).is(":checked") ) { 
                        $("#opanda_notify_unlocks-options").fadeIn();
                    } else {
                        $("#opanda_notify_unlocks-options").hide(); 
                    }
                }).change();                
            }
        },
        
        /** ---
         * Permissions Options
         */
        
        permissions: {
            init: function() {
                var self = this;
                $('input[id^="opanda_user_role_"]').each(function(){
                    self.toggle.call(this);
                });

                $('input[id^="opanda_user_role_"]').change(function(){
                    self.toggle.call(this);
                });

                //fix checkbox
                $('input[id^="opanda_allow_user_role_"]').change(function(){
                   $(this).val( $(this).is(':checked') ? 1 : 0 );
                });
                
                $(".permissions-set .help-block").click(function(){
                    var $checkbox = $(this).prev();
                    $checkbox.click();
                });
            },

            toggle: function() {
                var changeGroupId = $(this).attr('id');

                if( $(this).is(':checked') )
                    $('#' + changeGroupId + '_options_group').fadeIn(200);
                else {
                    $('#' + changeGroupId + '_options_group').find('input[id^="opanda_allow_user_role_"]')
                        .prop('checked', false);
                    $('#' + changeGroupId + '_options_group').fadeOut(200);
                }
            }
        },
        
        /** ---
         * Terms & Policies Options
         */
        
        text: {
            
            init: function() {
               
                $("#opanda_terms_enabled").change(function(){
               
                    if ( $(this).is(":checked") ) { 
                        $("#page-opanda-terms-enabled-options").fadeIn();
                        $("#no-page-opanda-terms-enabled-options").fadeIn();   
                        $(".factory-control-terms_use_pages").fadeIn();
                    } else {
                        $("#page-opanda-terms-enabled-options").hide();
                        $("#no-page-opanda-terms-enabled-options").hide();  
                        
                        if ( !$("#opanda_privacy_enabled").is(":checked") ) {
                            $(".factory-control-terms_use_pages").hide();    
                        }
                    }
                }).change();

                $("#opanda_privacy_enabled").change(function(){
                    
                    if ( $(this).is(":checked") ) { 
                        $("#page-opanda-privacy-enabled-options").fadeIn();
                        $("#no-page-opanda-privacy-enabled-options").fadeIn();
                        $(".factory-control-terms_use_pages").fadeIn();
                    } else {
                        $("#page-opanda-privacy-enabled-options").hide();
                        $("#no-page-opanda-privacy-enabled-options").hide();
                        
                        if ( !$("#opanda_terms_enabled").is(":checked") ) {
                            $(".factory-control-terms_use_pages").hide();    
                        }
                    }
                }).change();  
                
                $("#opanda_terms_use_pages").change(function(){
                    
                    if ( $(this).is(":checked") ) { 
                        $("#opanda-nopages-options").hide();            
                        $("#opanda-pages-options").fadeIn();
                    } else {
                        $("#opanda-nopages-options").fadeIn(); 
                        $("#opanda-pages-options").hide();
                    }
                }).change();  
            }
        }
    };
    
    $(function(){
        settings.init();
    });
    
})(jQuery)