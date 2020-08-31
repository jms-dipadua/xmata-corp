<?php

/**
 * Registers options for the subscription services.
 * 
 * @see the 'opanda_subscription_services_options' action
 * 
 * @since 1.1.3
 * @return mixed[]
 */
function optinpanda_subscription_services_options( $options ) {
    
    // mailchimp
        
    $options[] = array(
        'type'      => 'div',
        'id'        => 'opanda-mailchimp-options',
        'class'     => 'opanda-mail-service-options opanda-hidden',
        'items'     => array(

            array(
                'type' => 'separator'
            ),
            array(
                'type'      => 'textbox',
                'name'      => 'mailchimp_apikey',
                'after'     => sprintf( __( '<a href="%s" class="btn btn-default" target="_blank">Get API Key</a>', 'optinpanda' ), 'http://kb.mailchimp.com/accounts/management/about-api-keys#Finding-or-generating-your-API-key' ),
                'title'     => __( 'API Key', 'optinpanda' ),
                'hint'      => __( 'The API key of your MailChimp account.', 'optinpanda' ),
            ),
            array(
                'type'      => 'checkbox',
                'way'       => 'buttons',
                'name'      => 'mailchimp_welcome',
                'title'     => __( 'Send "Welcome" Email', 'optinpanda' ),
                'default'   => true,
                'hint'      => __( 'Sends the Welcome Email configured in your MailChimp account after subscription (works only if the Single Opt-In set).', 'optinpanda' )
            )           
        )
    );

    // aweber

    if( !get_option('opanda_aweber_consumer_key', false) ) {

        $options[] = array(
            'type'      => 'div',
            'id'        => 'opanda-aweber-options',
            'class'     => 'opanda-mail-service-options opanda-hidden',
            'items'     => array(

                array(
                    'type' => 'separator'
                ),
                array(
                    'type'      => 'html',
                    'html'      => 'opanda_aweber_html'
                ),
                array(
                    'type'      => 'textarea',
                    'name'      => 'aweber_auth_code',
                    'title'     => __( 'Authorization Code', 'optinpanda' ),
                    'hint'      => __( 'The authorization code you will see after log in to your Aweber account.', 'optinpanda' )
                )
            )
        );    

    } else {

        $options[] = array(
            'type'      => 'div',
            'id'        => 'opanda-aweber-options',
            'class'     => 'opanda-mail-service-options opanda-hidden',
            'items'     => array(
                array(
                    'type' => 'separator'
                ),
                array(
                    'type'      => 'html',
                    'html'      => 'opanda_aweber_html'
                )                    
            )
        );
    }

    // getresponse

    $options[] = array(
        'type'      => 'div',
        'id'        => 'opanda-getresponse-options',
        'class'     => 'opanda-mail-service-options opanda-hidden',
        'items'     => array(

            array(
                'type' => 'separator'
            ),
            array(
                'type'      => 'textbox',
                'name'      => 'getresponse_apikey',
                'title'     => __( 'API Key', 'optinpanda' ),
                'after'     => sprintf( __( '<a href="%s" class="btn btn-default" target="_blank">Get API Key</a>', 'optinpanda' ), 'http://support.getresponse.com/faq/where-i-find-api-key' ),
                'hint'      => __( 'The API key of your GetResponse account.', 'optinpanda' ),
            )
        )
    );

    // mymail

    $options[] = array(
        'type'      => 'div',
        'id'        => 'opanda-mymail-options',
        'class'     => 'opanda-mail-service-options opanda-hidden',
        'items'     => array(


            array(
                'type' => 'html',
                'html' => 'opanda_show_mymail_html'
            ),
            array(
                'type' => 'separator'
            ),
            array(
                'type'      => 'checkbox',
                'way'       => 'buttons',
                'name'      => 'mymail_redirect',
                'title'     => __( 'Redirect To Locker', 'optinpanda' ),
                'hint'      => sprintf( __( 'Set On to redirect the user after the email confirmation to the page where the locker located.<br />If Off, the MyMail will redirect the user to the page specified in the option <a href="%s" target="_blank">Newsletter Homepage</a>.', 'optinpanda' ), admin_url('options-general.php?page=newsletter-settings&settings-updated=true#frontend') )
            )
        )
    );
    
    // mailster

    $options[] = array(
        'type'      => 'div',
        'id'        => 'opanda-mailster-options',
        'class'     => 'opanda-mail-service-options opanda-hidden',
        'items'     => array(


            array(
                'type' => 'html',
                'html' => 'opanda_show_mailster_html'
            ),
            array(
                'type' => 'separator'
            ),
            array(
                'type'      => 'checkbox',
                'way'       => 'buttons',
                'name'      => 'mailster_redirect',
                'title'     => __( 'Redirect To Locker', 'optinpanda' ),
                'hint'      => sprintf( __( 'Set On to redirect the user after the email confirmation to the page where the locker located.<br />If Off, the Mailster will redirect the user to the page specified in the option <a href="%s" target="_blank">Newsletter Homepage</a>.', 'optinpanda' ), admin_url('edit.php?post_type=newsletter&page=mailster_settings#frontend') )
            )
        )
    );

    // mailpoet

    $options[] = array(
        'type'      => 'div',
        'id'        => 'opanda-mailpoet-options',
        'class'     => 'opanda-mail-service-options opanda-hidden',
        'items'     => array(

            array(
                'type' => 'html',
                'html' => 'opnada_show_mailpoet_html'
            )   
        )
    );

    // acumbamail

    $options[] = array(
        'type'      => 'div',
        'id'        => 'opanda-acumbamail-options',
        'class'     => 'opanda-mail-service-options opanda-hidden',
        'items'     => array(

            array(
                'type' => 'separator'
            ),
            array(
                'type'      => 'textbox',
                'name'      => 'acumbamail_customer_id',
                'title'     => __( 'Customer ID', 'optinpanda' ),
                'after'     => sprintf( __( '<a href="%s" class="btn btn-default" target="_blank">Get ID & Token</a>', 'optinpanda' ), 'https://acumbamail.com/apidoc/' ),
                'hint'      => __( 'The customer ID of your Acumbamail account.', 'optinpanda' )
            ),
            array(
                'type'      => 'textbox',
                'name'      => 'acumbamail_api_token',
                'title'     => __( 'API Token', 'optinpanda' ),
                'hint'      => __( 'The API token of your Acumbamail account.', 'optinpanda' )
            )
        )
    );

    // knews

    $options[] = array(
        'type'      => 'div',
        'id'        => 'opanda-knews-options',
        'class'     => 'opanda-mail-service-options opanda-hidden',
        'items'     => array(

            array(
                'type' => 'html',
                'html' => 'opanda_show_knews_html'
            ),
            array(
                'type' => 'separator'
            ),
            array(
                'type'      => 'checkbox',
                'way'       => 'buttons',
                'name'      => 'knews_redirect',
                'title'     => __( 'Redirect To Locker', 'optinpanda' ),
                'hint'      => __( 'Set On to redirect the user after the email confirmation to the page where the locker located.<br />If Off, the K-news will redirect the user to the home page.', 'optinpanda' )
            )   
        )
    );   

    // freshmail

    $options[] = array(
        'type'      => 'div',
        'id'        => 'opanda-freshmail-options',
        'class'     => 'opanda-mail-service-options opanda-hidden',
        'items'     => array(

            array(
                'type' => 'separator'
            ),
            array(
                'type'      => 'textbox',
                'name'      => 'freshmail_apikey',
                'title'     => __( 'API Key', 'optinpanda' ),
                'after'     => sprintf( __( '<a href="%s" class="btn btn-default" target="_blank">Get API Keys</a>', 'optinpanda' ), 'https://app.freshmail.com/en/settings/integration/' ),
                'hint'      => __( 'The API Key of your FreshMail account.', 'optinpanda' )
            ),
            array(
                'type'      => 'textbox',
                'name'      => 'freshmail_apisecret',
                'title'     => __( 'API Secret', 'optinpanda' ),
                'hint'      => __( 'The API Sercret of your FreshMail account.', 'optinpanda' )
            )
        )
    );

    // sendy

    $options[] = array(
        'type'      => 'div',
        'id'        => 'opanda-sendy-options',
        'class'     => 'opanda-mail-service-options opanda-hidden',
        'items'     => array(

            array(
                'type' => 'separator'
            ),
            array(
                'type'      => 'textbox',
                'name'      => 'sendy_apikey',
                'title'     => __( 'API Key', 'optinpanda' ),
                'hint'      => __( 'The API key of your Sendy application, available in Settings.', 'optinpanda' ),
            ),
            array(
                'type'      => 'textbox',
                'name'      => 'sendy_url',
                'title'     => __( 'Installation', 'optinpanda' ),
                'hint'      => __( 'An URL for your Sendy installation, <strong>http://your_sendy_installation</strong>', 'optinpanda' ),
            )
        )
    );
    
    // smartemailing
    
    $options[] = array(
        'type'      => 'div',
        'id'        => 'opanda-smartemailing-options',
        'class'     => 'opanda-mail-service-options opanda-hidden',
        'items'     => array(

            array(
                'type' => 'separator'
            ),
            array(
                'type'      => 'textbox',
                'name'      => 'smartemailing_username',
                'title'     => __( 'Username', 'optinpanda' ),
                'hint'      => __( 'Enter your username on SmartEmailing. Usually it is a email.', 'optinpanda' ),
            ),          
            array(
                'type'      => 'textbox',
                'name'      => 'smartemailing_apikey',
                'after'     => sprintf( __( '<a href="%s" class="btn btn-default" target="_blank">Get API Key</a>', 'optinpanda' ), 'https://app.smartemailing.cz/userinfo/show/api' ),
                'title'     => __( 'API Key', 'optinpanda' ),
                'hint'      => __( 'The API key of your SmartEmailing account.', 'optinpanda' ),
            )
        )
    );
    
    // sendinblue

    $options[] = array(
        'type'      => 'div',
        'id'        => 'opanda-sendinblue-options',
        'class'     => 'opanda-mail-service-options opanda-hidden',
        'items'     => array(

            array(
                'type' => 'separator'
            ),
            array(
                'type'      => 'textbox',
                'name'      => 'sendinblue_apikey',
                'title'     => __( 'API Key', 'optinpanda' ),
                'after'     => sprintf( __( '<a href="%s" class="btn btn-default" target="_blank">Get API Key</a>', 'optinpanda' ), 'https://my.sendinblue.com/advanced/apikey' ),
                'hint'      => __( 'The API Key (version 2.0) of your Sendinblue account.', 'optinpanda' )
            )
        )
    );
    
    // activecampaign

    $options[] = array(
        'type'      => 'div',
        'id'        => 'opanda-activecampaign-options',
        'class'     => 'opanda-mail-service-options opanda-hidden',
        'items'     => array(

            array(
                'type' => 'separator'
            ),
            array(
                'type'      => 'textbox',
                'name'      => 'activecampaign_apiurl',
                'title'     => __( 'API Url', 'optinpanda' ),
                'after'     => sprintf( __( '<a href="%s" class="btn btn-default" target="_blank">Get API Url</a>', 'optinpanda' ), 'http://www.activecampaign.com/help/using-the-api/' ),
                'hint'      => __( 'The API Url of your ActiveCampaign account.', 'optinpanda' )
            ),
            array(
                'type'      => 'textbox',
                'name'      => 'activecampaign_apikey',
                'title'     => __( 'API Key', 'optinpanda' ),
                'after'     => sprintf( __( '<a href="%s" class="btn btn-default" target="_blank">Get API Key</a>', 'optinpanda' ), 'http://www.activecampaign.com/help/using-the-api/' ),
                'hint'      => __( 'The API Key of your ActiveCampaign account.', 'optinpanda' )
            )
        )
    );    
    
    // sendgrid
        
    $options[] = array(
        'type'      => 'div',
        'id'        => 'opanda-sendgrid-options',
        'class'     => 'opanda-mail-service-options opanda-hidden',
        'items'     => array(

            array(
                'type' => 'separator'
            ),
            array(
                'type'      => 'textbox',
                'name'      => 'sendgrid_apikey',
                'after'     => sprintf( __( '<a href="%s" class="btn btn-default" target="_blank">Get API Key</a>', 'optinpanda' ), 'https://app.sendgrid.com/settings/api_keys' ),
                'title'     => __( 'API Key', 'optinpanda' ),
                'hint'      => __( 'Your SendGrid API key. Grant <strong>Full Access</strong> in settings of your API key.', 'optinpanda' ),
            ),
            array(
                'type'      => 'dropdown',
                'name'      => 'sendgrid_api_version',
                'title'     => __( 'API version', 'bizpanda' ),
                'data'      => array(
                    array( 'auto', __('Auto', 'bizpanda') ),
                    array( 'marketing-v3', __('Marketing API v3.0', 'bizpanda') ),
                    array( 'legacy-v3', __('Legacy API v3.0', 'bizpanda') ),
                ),
                'default'   => 'transparence',
                'hint'      => sprintf( __( 'If you get the error "access denied" or see that your lists are empty, try to change the API version.', 'bizpanda' ), opanda_get_settings_url('text') )
            )
        )
    );
    
    // sg autorepondeur
        
    $options[] = array(
        'type'      => 'div',
        'id'        => 'opanda-sgautorepondeur-options',
        'class'     => 'opanda-mail-service-options opanda-hidden',
        'items'     => array(

            array(
                'type' => 'separator'
            ),
            array(
                'type'      => 'textbox',
                'name'      => 'sg_apikey',
                'after'     => sprintf( __( '<a href="%s" class="btn btn-default" target="_blank">Get Code</a>', 'optinpanda' ), 'http://sg-autorepondeur.com/membre_v2/compte-options.php' ),
                'title'     => __( 'Activation Code', 'optinpanda' ),
                'hint'      => __( 'The Activation Code from your SG Autorepondeur account (<i>Mon compte -> Autres Options -> Informations administratives</i>).', 'optinpanda' ),
            ),
            array(
                'type'      => 'textbox',
                'name'      => 'sg_memberid',
                'title'     => __( 'Member ID', 'optinpanda' ),
                'hint'      => __( 'The Memeber ID of your SG Autorepondeur account (<i>available on the home page below the SG logo, for example, 9059</i>).', 'optinpanda' ),
            )
        )
    );

    return $options;
}

add_action('opanda_subscription_services_options', 'optinpanda_subscription_services_options');

/**
 * Shows HTML for Aweber.
 * 
 * @since 1.1.3
 * @return void
 */
function opanda_aweber_html() {
    
    if( !get_option('opanda_aweber_consumer_key', false) ) {
    ?>

    <div class="form-group">
        <label class="col-sm-2 control-label"></label>
        <div class="control-group controls col-sm-10 opanda-aweber-steps">
            <p><?php _e( 'To connect your Aweber account:', 'optinpanda' ) ?></p>
            <ul>
                <li><?php _e( '<span>1.</span> <a href="https://auth.aweber.com/1.0/oauth/authorize_app/92c68137" class="button" target="_blank">Click here</a> <span>to open the authorization page and log in.</span>', 'optinpanda' ) ?></li>
                <li><?php _e( '<span>2.</span> Copy and paste the authorization code in the field below.', 'optinpanda' ) ?></li>
            </ul>
        </div>
    </div>

    <?php } else { ?>

    <div class="form-group">
        <label class="col-sm-2 control-label"></label>
        <div class="control-group controls col-sm-10 opanda-aweber-steps">
            <p><strong><?php _e( 'Your Aweber Account is connected.', 'optinpanda' ) ?></strong></p>
            <ul>
                <li><?php _e( '<a href="' . admin_url('admin.php?page=settings-bizpanda&opanda_screen=subscription&opanda_action=disconnectAweber') . '" class="button onp-sl-aweber-oauth-logout">Click here</a> <span>to disconnect.</span>', 'optinpanda' ) ?></li>                    
            </ul>
        </div>
    </div>

    <?php  
    }
}

/**
 * Shows HTML for MyMail.
 * 
 * @since 1.1.3
 * @return void
 */
function opanda_show_mymail_html() {
    
    if ( !defined('MYMAIL_VERSION') ) {
        ?>
        <div class="form-group">
            <label class="col-sm-2 control-label"></label>
            <div class="control-group controls col-sm-10">
                <p><strong><?php _e('The MyMail plugin is not found on your website. Emails will not be saved.', 'opanda') ?></strong></p>
            </div>
        </div>
        <?php
    } else {
        ?>
        <div class="form-group">
            <label class="col-sm-2 control-label"></label>
            <div class="control-group controls col-sm-10">
                <p><?php _e('You can set a list where the subscribers should be added in the settings of a particular locker.', 'opanda') ?></p>
            </div>
        </div>
    <?php
    }
}

/**
 * Shows HTML for Mailster.
 * 
 * @since 1.1.3
 * @return void
 */
function opanda_show_mailster_html() {
    
    if ( !defined('MAILSTER_VERSION') ) {
        ?>
        <div class="form-group">
            <label class="col-sm-2 control-label"></label>
            <div class="control-group controls col-sm-10">
                <p><strong><?php _e('The Mailster plugin is not found on your website. Emails will not be saved.', 'opanda') ?></strong></p>
            </div>
        </div>
        <?php
    } else {
        ?>
        <div class="form-group">
            <label class="col-sm-2 control-label"></label>
            <div class="control-group controls col-sm-10">
                <p><?php _e('You can set a list where the subscribers should be added in the settings of a particular locker.', 'opanda') ?></p>
            </div>
        </div>
    <?php
    }
}
    
/**
 * Shows HTML for MailPoet.
 * 
 * @since 1.1.3
 * @return void
 */
function opnada_show_mailpoet_html() {

    if ( !defined('WYSIJA') && !defined('MAILPOET_VERSION') ) {
        ?>
        <div class="form-group">
            <label class="col-sm-2 control-label"></label>
            <div class="control-group controls col-sm-10">
                <p><strong><?php _e('The MailPoet plugin is not found on your website. Emails will not be saved.', 'opanda') ?></strong></p>
            </div>
        </div>
        <?php
    } else {
        ?>
        <div class="form-group">
            <label class="col-sm-2 control-label"></label>
            <div class="control-group controls col-sm-10">
                <p><?php _e('You can set a list where the subscribers should be added in the settings of a particular locker.', 'opanda') ?></p>
            </div>
        </div>
    <?php
    }
}
    
/**
 * Shows HTML for Knews.
 * 
 * @since 1.1.3
 * @return void
 */
function opanda_show_knews_html() {

    if ( !class_exists("KnewsPlugin") ) {
        ?>
        <div class="form-group">
            <label class="col-sm-2 control-label"></label>
            <div class="control-group controls col-sm-10">
                <p><strong><?php _e('The K-news plugin is not found on your website. Emails will not be saved.', 'opanda') ?></strong></p>
            </div>
        </div>
        <?php
    } else {
        ?>
        <div class="form-group">
            <label class="col-sm-2 control-label"></label>
            <div class="control-group controls col-sm-10">
                <p><?php _e('You can set a list where the subscribers should be added in the settings of a particular locker.', 'opanda') ?></p>
            </div>
        </div>
    <?php
    }
}

function opanda_on_saving_aweber_options( $caller ) {

    $service = isset( $_POST['opanda_subscription_service'] ) 
            ? $_POST['opanda_subscription_service']:
            null;

    $authCode = isset( $_POST['opanda_aweber_auth_code'] )
            ? trim( $_POST['opanda_aweber_auth_code'] ):
            null;

    unset( $_POST['opanda_aweber_auth_code'] );

    if ( 'aweber' !== $service || get_option('opanda_aweber_consumer_key', false) ) return;

    // if the auth code is empty, show the error

    if ( empty( $authCode ) ) {
        return $caller->showError( __('Unable to connect to Aweber. The Authorization Code is empty.', 'optinpanda' ) );    
    }

    // try to get credential via api, shows the error if the exception occurs

    require_once OPANDA_BIZPANDA_DIR.'/admin/includes/subscriptions.php';
    $aweber = OPanda_SubscriptionServices::getService('aweber');

    try {
        $credential = $aweber->getCredentialUsingAuthorizeKey( $authCode ); 
    } catch (Exception $ex) {
        return $caller->showError( $ex->getMessage() );
    }

    // saves the credential

    if ( $credential && sizeof($credential) ) {
        foreach( $credential as $key => $value ) {
            update_option('opanda_aweber_'.$key, $value);
        }
    }
}
add_action('opanda_on_saving_subscription_settings', 'opanda_on_saving_aweber_options');

function opanda_on_saving_sendgrid_options( $caller ) {

    // resets a legacy flag to check if we need to use legacy API
    delete_option('opanda_sendgrid_is_legacy');
}
add_action('opanda_on_saving_subscription_settings', 'opanda_on_saving_sendgrid_options');
    
/**
 * Adds scripts and styles in the admin area.
 * 
 * @see the 'admin_enqueue_scripts' action
 * 
 * @since 1.0.0
 * @return void
 */
function optinpanda_icon_admin_assets( $hook ) { 
        ?>
        <style>
            #menu-posts-opanda-item a[href*="premium-optinpanda"] {
                color: #43b8e3 !important;
            }
        </style>
        <?php
        
        return;
    

}

add_action('admin_enqueue_scripts', 'optinpanda_icon_admin_assets');



// ---
// Help
//

/**
 * Registers a help section for the Connect Locker.
 * 
 * @since 1.0.0
 */
function optinpanda_register_help( $pages ) {
    global $opanda_help_cats;
    if ( !$opanda_help_cats ) $opanda_help_cats = array();
    
    $items = array(
        array(
            'name' => 'email-locker',
            'title' => __('Email Locker', 'optinpanda'),
            'hollow' => true,

            'items' => array(
                array(
                    'name' => 'what-is-email-locker',
                    'title' => __('What is it?', 'optinpanda')
                ),
                array(
                    'name' => 'usage-example-email-locker',
                    'title' => __('Quick Start Guide', 'optinpanda')
                ), 
                array(
                    'name' => 'gdpr-email-locker',
                    'title' => __('GDPR Compatibility', 'optinpanda')
                )
            )
        )
    );
    
    array_unshift($pages, array(
        'name' => 'optinpanda',
        'title' => __('Plugin: Opt-In Panda', 'optinpanda'),
        'items' => $items
    ));
        unset( $pages[1]);
    

    
    return $pages;
}

add_filter('opanda_help_pages', 'optinpanda_register_help');

function opanda_help_page_optinpanda( $manager ) {
    require OPTINPANDA_DIR . '/plugin/admin/pages/help/optinpanda.php';
}

add_action('opanda_help_page_optinpanda', 'opanda_help_page_optinpanda');


/**
 * Makes internal page "License Manager" for the Opt-in Panda
 * 
 * @since 1.0.0
 * @return bool true
 */
function optinpanda_make_internal_license_manager( $internal ) {
    
    if ( BizPanda::isSinglePlugin() ) return $internal;
    return true;
}

add_filter('factory_page_is_internal_license-manager-optinpanda', 'optinpanda_make_internal_license_manager');


// ---
// Menu
//

/**
 * Changes the menu title if the Opt-In Panda is onle the plugin installed from the BizPanda Collection.
 *
 * @since 1.0.0
 * @return string
 */
function opanda_change_menu_title( $title ) {
    if ( !BizPanda::isSinglePlugin() ) return $title;
    return __('Opt-In Panda', 'opanda');
}

add_filter('opanda_menu_title', 'opanda_change_menu_title');

/**
 * Changes the menu icon if the Opt-In Panda is only the plugin installed.
 *
 * @since 1.0.0
 * @return string
 */
function opanda_change_menu_icon( $icon ) {
    if ( !BizPanda::isSinglePlugin() ) return $icon;
    return 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyMCAyMCIgZmlsbD0iIzllYTNhOCIgc2hhcGUtcmVuZGVyaW5nPSJnZW9tZXRyaWNQcmVjaXNpb24iPjxwYXRoIGQ9Ik0xNi4yMDM1MywzLjk0MDg5YTEuNzU5NDMsMS43NTk0MywwLDAsMC0yLjQ3OTY0LjAxODQ5bC0xLjUxMDEsMS41MTAxYTUuMDYxODcsNS4wNjE4NywwLDAsMSwxLjUxMDEsMS4wMzg0Miw0LjM0MzQ4LDQuMzQzNDgsMCwwLDEsLjM4MzY3LjQ2LDUuMjI5MjMsNS4yMjkyMywwLDAsMSwxLjAwMzM3LDQuMDg4ODFsLTAuMDA4MjguMDQyMjVhNC43ODMxOSw0Ljc4MzE5LDAsMCwxLS42ODk1NiwxLjY4MDEyLDUuMDg2NzgsNS4wODY3OCwwLDAsMS01LjM1NjQzLDIuMjIxNzEsNC43ODY3Miw0Ljc4NjcyLDAsMCwxLTEuNjgxOTEtLjcwMjY5QTUuMDcyMTcsNS4wNzIxNywwLDAsMSw1LjA2OTI3LDEwLjA0MjRsMC4wMDAwNi0uMDIyMjNhNS4wMDU5Miw1LjAwNTkyLDAsMCwxLC45NDMxOS0yLjgzMyw1LjQ4NjYsNS40ODY2LDAsMCwxLC41NDE3LTAuNjc5MzgsNS4wNjI3Myw1LjA2MjczLDAsMCwxLDEuNTEtMS4wMzgzNGwtMS41MTAxLTEuNTEwMWExLjc2MjQ4LDEuNzYyNDgsMCwwLDAtMi40ODY5MS0uMDExMjksOC41NzQ5NCw4LjU3NDk0LDAsMCwwLTIuNDcwNDYsNy4xMzE3MUE4LjQ3NDc1LDguNDc0NzUsMCwwLDAsMy4zNzEsMTUuMzU2Nyw4LjYwMzg4LDguNjAzODgsMCwxLDAsMTYuMjAzNTMsMy45NDA4OVoiIGZpbGw9IiM5ZWEzYTgiLz48cGF0aCBkPSJNNi4zMDYxOSw4LjMwMzhMNi4xNzc2LDguNTc5MTUsNy42NjYsMTAuMDY3NThsMS4yMjQsMS4yMjRhMS43NTksMS43NTksMCwwLDAsMS4wNzk3NS41MDkyN3EwLjA4NDQ2LDAuMDA4MDcuMTY5MzMsMC4wMDgwOWExLjc2MTM1LDEuNzYxMzUsMCwwLDAsMS4yNDktLjUxNzM2bDEuMjI0LTEuMjI0LDEuNDg4Ni0xLjQ4ODYtMC4xMjg1OS0uMjc0OTFhNC4xODcsNC4xODcsMCwwLDAtMi4xMDEtMi4wNzE2MmwtMC4yODk5NC0uMTMwMjMtMS40NDIsMS40NDIwNS0xLjQ0Mi0xLjQ0Mi0wLjI4OTgzLjEzMDE4QTQuMTg3NjIsNC4xODc2MiwwLDAsMCw2LjMwNjE5LDguMzAzOFoiLz48L3N2Zz4=';
}

add_filter('opanda_menu_icon', 'opanda_change_menu_icon');

/**
 * Returns an URL of page "Go Premium".
 */
function onp_op_get_premium_page_url( $url, $name, $campaign = 'na' ) {
    if ( !empty( $name ) && !in_array( $name, array('email-locker') )) return $url;
    
    if ( get_option('onp_op_skip_trial', false) ) {
        return onp_sl_get_premium_url( $campaign );
    } else {
        return admin_url('edit.php?post_type=opanda-item&page=premium-optinpanda');
    }
}

add_filter('opanda_premium_url', 'onp_op_get_premium_page_url', 10, 3);

/**
 * Returns an URL where the user can purchaes the plugin.
 */
function onp_op_get_url_to_purchase( $campaign = 'na' ) {
    global $optinpanda; 
    return onp_licensing_325_get_purchase_url( $optinpanda, $campaign );
}

/**
 * 
 */
function onp_op_plugins_suggestions( $suggestions ) {
    if ( empty( $suggestions ) ) return $suggestions;

    foreach( $suggestions as $index => $item ) {
        
        if ( 'optinpanda' === $item['name'] ) {
            $suggestions[$index]['description'] = '<p>Get more email subscribers the most organic way without tiresome popups.</p><p>Custom fields, export in CSV, content blurring, advanced options and more.</p>';
            continue;
        }
        
        if ( 'sociallocker' === $item['name'] ) {
            $suggestions[$index]['description'] = '<p>Helps to attract social traffic and improve spreading your content in social networks.</p><p>Also extends the Sign-In Locker by adding social actions you can set up to be performed.</p>';
        }
    }
    
    return $suggestions;
}

add_filter('opanda_plugins_suggestions', 'onp_op_plugins_suggestions');

/**
 * Adds extra options after list selection.
 */
function onp_op_extra_list_subscription_options( $options, $service ) {
    if ( !isset( $service['name'] ) || $service['name'] !== 'mailchimp') return $options;

    $options[] = array(
        'type' => 'hidden',
        'name' => 'mailchimp_groups'
    );
    
    $options[] = array(
        'type' => 'html',
        'html' => 'onp_op_mailchimp_groups_select'
    );
    
    return $options;
}

add_filter('opanda_extra_list_subscription_options', 'onp_op_extra_list_subscription_options', 10, 2);

/**
 * Displays extra options for the MailChimp Lists.
 */
function onp_op_mailchimp_groups_select() {
    ?>
        <div class='form-group form-group-dropdown factory-control-mailchimp-groups'>
            <label class='col-sm-2 control-label'><?php _e('Groups', 'bizpanda') ?></label>
            <div class='control-group col-sm-10 mailchimp_groups'>
                <div class="factory-ajax-loader"></div>
                <div id="opanda-mailchimp-groups-holder"></div>
                <script>
                    jQuery(function($){
                        
                        var groupSelectWidget = {
                            
                            init: function() {
                                var self = this;
                                
                                $("#opanda_subscribe_list").on('factory-loaded change', function(){
                                    self.refresh();
                                });
                            },
                            
                            refresh: function() {
                                var self = this;
                                
                                self.showLoader();
                                
                                $.ajax({
                                    url: '<?php echo admin_url('admin-ajax.php') ?>',
                                    data: {
                                        action: 'opanda_get_mailchimp_groups',
                                        opanda_list_id: $("#opanda_subscribe_list").val()
                                    },
                                    dataType: 'json',
                                    success: function( response  ){
                                        if ( response.error ) return self.showError( response.error  );
                                        self.fill( response.items );
                                    },
                                    error: function( response ) {

                                        if ( console && console.log ) {
                                            console.log( response.responseText );
                                        }

                                        self.showError('Unexpected error occurred during the ajax request.');
                                    },
                                    complete: function() {
                                        self.hideLoader();
                                    }
                                });
                            },
                            
                            showLoader: function() {
                                $("#opanda-mailchimp-groups-holder").html("");
                                $(".mailchimp_groups .factory-ajax-loader").show();
                            },
                            
                            hideLoader: function() {
                                $(".mailchimp_groups .factory-ajax-loader").hide();
                            },
                            
                            fill: function( items ) {
                                
                                if ( !items || items.length == 0 ){
                                    this.showEmpty();
                                    return;
                                }
                                
                                var values = $.parseJSON( $("#opanda_mailchimp_groups").val() );

                                var self = this;
                                var $holder = $("#opanda-mailchimp-groups-holder").html("");

                                for( var i in items ) {
                                    var groupInfo = items[i];
                                    var $group = $("<div class='opanda-mailchimp-group-wrap'><strong>" + groupInfo.title + "</strong></div>").appendTo( $holder );
                                    
                                    for ( var k in groupInfo['items'] ) {
                                        var valueInfo = groupInfo['items'][k];
                                        var checked = ( $.inArray( valueInfo[0] + "", values) >= 0 ) ? 'checked="checked" ' : '';
                                        $("<div class='opanda-mailchimp-group-checkbox'><label><input type='checkbox' " + checked + " value='" + valueInfo[0] + "'><span>" + valueInfo[1] + "</span></label></div>").appendTo($group);
                                    }
                                }
                                
                                $holder.find("input").bind("changed click", function(){
                                    self.updateValue();
                                });
                                
                                this.updateValue();
                            },
                            
                            updateValue: function() {
                                var $holder = $("#opanda-mailchimp-groups-holder");
                                var values = [];
                                
                                $holder.find("input:checked").each(function(){
                                    values.push( $(this).attr('value') );
                                });
 
                                $("#opanda_mailchimp_groups").val(  JSON.stringify(values) );
                            },
                            
                            showEmpty: function() {
                                this.updateValue();
                                $("#opanda-mailchimp-groups-holder").html('<?php _e('<span>No groups available for the list selected. <a href="http://kb.mailchimp.com/lists/groups/getting-started-with-groups" target="_blank">You can create them</a> in your MailChimp account.<span>', 'bizpanda') ?>');
                            },                            

                            showError: function( text ) {
                                $("#opanda-mailchimp-groups-holder").html("");
                                var $holder = $("#opanda-mailchimp-groups-holder");
                                
                                var $error = $("<div class='factory-control-error'></div>")
                                    .append($("<i class='fa fa-exclamation-triangle'></i>"))
                                    .append( text );

                                $holder.append($error);
                            }
                        };

                        groupSelectWidget.init();                        
                    });
                </script>
                <div class="help-block"><?php _e('Optional. Select Groups to add subscribers.', 'bizpanda') ?></div>
            </div> 
        </div>
    <?php
}

/**
 * Returns MailChimp Groups.
 */
function onp_op_get_mailchimp_groups_json() {
    $result = array();
    
    $listId = isset( $_REQUEST["opanda_list_id"] ) ? trim( $_REQUEST["opanda_list_id"] ) : false;
    
    if ( $listId ) {
        require_once OPANDA_BIZPANDA_DIR.'/admin/includes/subscriptions.php';    
        $service = OPanda_SubscriptionServices::getService();
        
        try {
            $result = $service->getGroups( $listId );  
        } catch (Exception $ex) {

            echo json_encode(array(
                'success' => false,
                'error' => $ex->getMessage()
            )); 
        }
    }
    
    echo json_encode(array(
        'success' => true,
        'items' => $result
    ));
    
    exit;
}

add_action("wp_ajax_opanda_get_mailchimp_groups" , "onp_op_get_mailchimp_groups_json");


#comp merge
require(OPTINPANDA_DIR . '/plugin/admin/activation.php');
    require(OPTINPANDA_DIR . '/plugin/admin/notices.php');



require(OPTINPANDA_DIR . '/plugin/admin/pages/license-manager.php');
    require(OPTINPANDA_DIR . '/plugin/admin/pages/premium.php');


#endcomp