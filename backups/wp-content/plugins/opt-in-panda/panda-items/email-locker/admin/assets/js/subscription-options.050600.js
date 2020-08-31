if ( !window.bizpanda ) window.bizpanda = {};
if ( !window.bizpanda.subscriptionOptions ) window.bizpanda.subscriptionOptions = {};

(function($){
    
    window.bizpanda.subscriptionOptions = {
        
        init: function( item ) {
            var self = this;
            
            this.item = $('#opanda_item').val();
            if ( 'email-locker' !== this.item ) return;

            var hasDeliveryChoice = $("#opanda_subscribe_delivery").length;
            
            $("#opanda_subscribe_mode").change(function(){
                var value = $(this).val();
                
                if ( value === 'double-optin' || value === 'quick-double-optin' ) {
                    if ( hasDeliveryChoice ) {
                        $("#opanda-delivery-options").fadeIn();
                        var delivery = $("#opanda_subscribe_delivery").val();
                        if ( delivery === 'wordpress' ) $("#opanda-confirmation-email").fadeIn();
                    }
                    else $("#opanda-confirmation-email").fadeIn();
                } else {
                    $("#opanda-delivery-options").hide();
                    $("#opanda-confirmation-email").hide();
                }

            }).change();
            
            if ( hasDeliveryChoice ) {
                
                $("#opanda_subscribe_delivery").change(function(){
                    var value = $(this).val();

                    if ( value === 'wordpress' && $("#opanda_subscribe_delivery").is(":visible") ) {
                        $("#opanda-confirmation-email").fadeIn();
                    } else {
                        $("#opanda-confirmation-email").hide();
                    }    

                }).change();
            }

            $("#opanda_form_type").change(function(){
                var value = $(this).val();

                if ( value === 'custom-form' ) {
                    $("#opanda-email-form-options").hide();
                    $("#opanda-custom-form-options").fadeIn();     
                    
                    $("#opanda-fields-editor").fieldsEditor("adjustMappingSelectorsWidths");
                } else {
                    $("#opanda-email-form-options").fadeIn(300);
                    $("#opanda-custom-form-options").hide(); 
                }
                
            }).change();

            $("#opanda_subscribe_allow_social").change(function(){
                var value = $(this).is(":checked");
                if ( value ) $("#social-buttons-options").fadeIn();
                else $("#social-buttons-options").hide();
            }).change();
            
            $.bizpanda.filters.add('opanda-preview-options', function( options ){
                var extraOptions = self.getSubscriptionOptions();
                return $.extend(true, options, extraOptions);
            });
            
            $("#opanda_fields").change(function(){
                var data = $.parseJSON( $("#opanda_fields").val() );
                $("#opanda_fields").data('value', data);
            }).change();
        },

        getSubscriptionOptions: function() {

            var connectButtons = [];
            if ( $("#factory-checklist-opanda_subscribe_social_buttons-facebook").is(":checked") ) connectButtons.push('facebook');
            if ( $("#factory-checklist-opanda_subscribe_social_buttons-google").is(":checked") ) connectButtons.push('google'); 
            if ( $("#factory-checklist-opanda_subscribe_social_buttons-linkedin").is(":checked") ) connectButtons.push('linkedin');
            if ( $("#factory-checklist-opanda_subscribe_social_buttons-twitter").is(":checked") ) connectButtons.push('twitter');

            var groups = ( $("#opanda_subscribe_allow_social").is(":checked") && connectButtons.length && $("#opanda_form_type").val() !== 'custom-form' )
                ? ['subscription', 'connect-buttons']
                : ['subscription'];

            var optinMode = $('#opanda_subscribe_mode').val();

            var options = {

                groups: {
                    order: groups
                },

                terms: window.opanda_terms,
                privacyPolicy: window.opanda_privacy_policy,

                connectButtons: {

                    order: connectButtons,

                    text: {
                        message: $("#opanda_subscribe_social_text").val()
                    },

                    twitter: {
                        socialProxy: window.twitter_social_proxy,
                        actions: ['subscribe']
                    },
                    facebook: {
                        socialProxy: window.facebook_social_proxy,
                        actions: ['subscribe']
                    },
                    google: {
                        socialProxy: window.google_social_proxy,
                        actions: ['subscribe']
                    },
                    linkedin: {
                        socialProxy: window.linkedin_social_proxy,
                        actions: ['subscribe']
                    }
                },

                subscription: {
                    form: {
                        preview: true,
                        buttonText: $("#opanda_button_text").val(),
                        noSpamText: $("#opanda_after_button").val(),
                        type: $("#opanda_form_type").val(),
                        fields: $("#opanda_fields").data('fields')
                    }
                },

                subscribeActionOptions: {

                    campaignId: $("#opanda_subscribe_list").length ? $("#opanda_subscribe_list").val() : null,
                    service: window.opanda_subscription_service_name,
                    doubleOptin: $.inArray( optinMode, ['quick-double-optin', 'double-optin'] > -1),
                    confirm: $.inArray( optinMode, ['double-optin'] > -1)
                }
            };

            return options;
        }
    };
    
    $(function(){
        window.bizpanda.subscriptionOptions.init();
    });
    
})(jQuery);

