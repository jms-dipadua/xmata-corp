<?php

if ( is_admin() ) require( OPTINPANDA_DIR . '/plugin/admin/boot.php' );

// ---
// Subscription Services
//

/**
 * Registers available subscription services.
 * 
 * @see the 'opanda_subscription_services' action
 * 
 * @since 1.0.8
 * @return mixed[]
 */
function optinpanda_subscription_services( $services ) {
    
    $items = array(

        'database' => array(
            'title' => __('None', 'optinpanda'),
            'description' => __('Emails of subscribers will be saved in the WP database.', 'optinpanda'),
            
            'class' => 'OPanda_DatabaseSubscriptionService',
            'path' => OPTINPANDA_DIR . '/plugin/includes/subscription/database/class.database.php',
            'modes' => array('quick')
        ),
        'mailchimp' => array(
            'title' => __('MailChimp', 'optinpanda'),
            'description' => __('Adds subscribers to your MailChimp account.', 'optinpanda'),
            
            'image' => 'https://cconp.s3.amazonaws.com/optinpanda/mailing-services/gray/mailchimp.png',
            'hover' => 'https://cconp.s3.amazonaws.com/optinpanda/mailing-services/gray/mailchimp.png', 
            
            'class' => 'OPanda_MailChimpSubscriptionService',
            'path' => OPTINPANDA_DIR . '/plugin/includes/subscription/mailchimp/class.mailchimp.php',
            'modes' => array('double-optin', 'quick-double-optin', 'quick'),
            
            'notes' => array(
                'customFields' => sprintf( __('Select one of merge tags in your MailChimp account to save the value from this field. <a href="%s" target="_blank">Learn more about Merge Tags</a>.'), 'http://kb.mailchimp.com/merge-tags/using/getting-started-with-merge-tags' )
            )
        ),
        'aweber' => array(
            'title' => __('Aweber', 'optinpanda'),
            'description' => __('Adds subscribers to your Aweber account.', 'optinpanda'),
            
            'image' => 'https://cconp.s3.amazonaws.com/optinpanda/mailing-services/gray/aweber.png',
            'hover' => 'https://cconp.s3.amazonaws.com/optinpanda/mailing-services/colored/aweber.png',       
            
            'class' => 'OPanda_AweberSubscriptionService',
            'path' => OPTINPANDA_DIR . '/plugin/includes/subscription/aweber/class.aweber.php',
            'modes' => array('double-optin', 'quick-double-optin')
        ),
        'getresponse' => array(
            'title' => __('GetResponse', 'optinpanda'),
            'description' => __('Adds subscribers to your GetResponse account.', 'optinpanda'),
            
            'image' => 'https://cconp.s3.amazonaws.com/optinpanda/mailing-services/gray/getresponse.png',
            'hover' => 'https://cconp.s3.amazonaws.com/optinpanda/mailing-services/colored/getresponse.png',  
            
            'class' => 'OPanda_GetResponseSubscriptionService',
            'path' => OPTINPANDA_DIR . '/plugin/includes/subscription/getresponse/class.getresponse.php',
            'modes' => array('double-optin', 'quick-double-optin')
        ),
        'mailpoet' => array(
            'title' => __('MailPoet', 'optinpanda'),
            'description' => __('Adds subscribers to the plugin MailPoet.', 'optinpanda'),
            
            'image' => 'https://cconp.s3.amazonaws.com/optinpanda/mailing-services/gray/mailpoet.png',
            'hover' => 'https://cconp.s3.amazonaws.com/optinpanda/mailing-services/colored/mailpoet.png',
            
            'class' => 'OPanda_MailPoetSubscriptionService',
            'path' => OPTINPANDA_DIR . '/plugin/includes/subscription/mailpoet/class.mailpoet.php',
            'modes' => array('double-optin', 'quick-double-optin', 'quick')
        ),
        'mymail' => array(
            'title' => __('MyMail', 'optinpanda'),
            'description' => __('Adds subscribers to the plugin MyMail.', 'optinpanda'),
            
            'image' => 'https://cconp.s3.amazonaws.com/optinpanda/mailing-services/gray/mymail.png',
            'hover' => 'https://cconp.s3.amazonaws.com/optinpanda/mailing-services/colored/mymail.png',
            
            'class' => 'OPanda_MyMailSubscriptionService',
            'path' => OPTINPANDA_DIR . '/plugin/includes/subscription/mymail/class.mymail.php',
            'modes' => array('double-optin', 'quick-double-optin', 'quick')
        ),
        'mailster' => array(
            'title' => __('Mailster', 'optinpanda'),
            'description' => __('Adds subscribers to the plugin Mailster.', 'optinpanda'),
            
            'image' => 'https://cconp.s3.amazonaws.com/optinpanda/mailing-services/gray/mailster.png',
            'hover' => 'https://cconp.s3.amazonaws.com/optinpanda/mailing-services/colored/mailster.png',
            
            'class' => 'OPanda_MailsterSubscriptionService',
            'path' => OPTINPANDA_DIR . '/plugin/includes/subscription/mailster/class.mailster.php',
            'modes' => array('double-optin', 'quick-double-optin', 'quick')
        ),
        'acumbamail' => array(
            'title' => __('Acumbamail', 'optinpanda'),
            'description' => __('Adds subscribers to your Acumbamail account.', 'optinpanda'),
            
            'image' => 'https://cconp.s3.amazonaws.com/optinpanda/mailing-services/gray/acumbamail.png',
            'hover' => 'https://cconp.s3.amazonaws.com/optinpanda/mailing-services/colored/acumbamail.png',
            
            'class' => 'OPanda_AcumbamailSubscriptionService',
            'path' => OPTINPANDA_DIR . '/plugin/includes/subscription/acumbamail/class.acumbamail.php',
            'modes' => array('quick')
        ),
        'knews' => array(
            'title' => __('K-news', 'optinpanda'),
            'description' => __('Adds subscribers to the plugin K-news.', 'optinpanda'),
            
            'image' => 'https://cconp.s3.amazonaws.com/optinpanda/mailing-services/gray/knews.png',
            'hover' => 'https://cconp.s3.amazonaws.com/optinpanda/mailing-services/gray/knews.png',
            
            'class' => 'OPanda_KNewsSubscriptionService',
            'path' => OPTINPANDA_DIR . '/plugin/includes/subscription/knews/class.knews.php',
            'modes' => array('double-optin', 'quick-double-optin', 'quick')
        ),
        'freshmail' => array(
            'title' => __('FreshMail', 'optinpanda'),
            'description' => __('Adds subscribers to your FreshMail account.', 'optinpanda'),
            
            'image' => 'https://cconp.s3.amazonaws.com/optinpanda/mailing-services/gray/freshmail.png',
            'hover' => 'https://cconp.s3.amazonaws.com/optinpanda/mailing-services/colored/freshmail.png',
            
            'class' => 'OPanda_FreshmailSubscriptionService',
            'path' => OPTINPANDA_DIR . '/plugin/includes/subscription/freshmail/class.freshmail.php',
            'modes' => array('double-optin', 'quick-double-optin', 'quick')
        ),
        'sendy' => array(
            'title' => __('Sendy', 'optinpanda'),
            'description' => __('Adds subscribers to your Sendy application.', 'optinpanda'),
            
            'image' => 'https://cconp.s3.amazonaws.com/optinpanda/mailing-services/gray/sendy.png',
            'hover' => 'https://cconp.s3.amazonaws.com/optinpanda/mailing-services/colored/sendy.png',
            
            'class' => 'OPanda_SendySubscriptionService',
            'path' => OPTINPANDA_DIR . '/plugin/includes/subscription/sendy/class.sendy.php',
            'modes' => array('double-optin', 'quick'),
            
            'manualList' => true
        ),
        'smartemailing' => array(
            'title' => __('SmartEmailing', 'optinpanda'),
            'description' => __('Adds subscribers to your SmartEmailing account.', 'optinpanda'),
            
            'image' => 'https://cconp.s3.amazonaws.com/optinpanda/mailing-services/gray/smartemailing.png',
            'hover' => 'https://cconp.s3.amazonaws.com/optinpanda/mailing-services/colored/smartemailing.png',
            
            'class' => 'OPanda_SmartemailingSubscriptionService',
            'path' => OPTINPANDA_DIR . '/plugin/includes/subscription/smartemailing/class.smartemailing.php',
            'modes' => array('quick')
        ),
        'sendinblue' => array(
            'title' => __('SendInBlue', 'optinpanda'),
            'description' => __('Adds subscribers to your SendInBlue account.', 'optinpanda'),
            
            'image' => 'https://cconp.s3.amazonaws.com/optinpanda/mailing-services/gray/sendinblue.png',
            'hover' => 'https://cconp.s3.amazonaws.com/optinpanda/mailing-services/colored/sendinblue.png',
            
            'class' => 'OPanda_SendinblueSubscriptionService',
            'path' => OPTINPANDA_DIR . '/plugin/includes/subscription/sendinblue/class.sendinblue.php',
            'modes' => array('quick')
        ),
        'activecampaign' => array(
            'title' => __('ActiveCampaign', 'optinpanda'),
            'description' => __('Adds subscribers to your ActiveCampaign account.', 'optinpanda'),
            
            'image' => 'https://cconp.s3.amazonaws.com/optinpanda/mailing-services/gray/activecampaign.png',
            'hover' => 'https://cconp.s3.amazonaws.com/optinpanda/mailing-services/colored/activecampaign.png',
            
            'class' => 'OPanda_ActivecampaignSubscriptionService',
            'path' => OPTINPANDA_DIR . '/plugin/includes/subscription/activecampaign/class.activecampaign.php',
            'modes' => array('quick'),
        ),
        'sendgrid' => array(
            'title' => __('SendGrid', 'optinpanda'),
            'description' => __('Adds subscribers to your SendGrid account.', 'optinpanda'),
            
            'image' => 'https://cconp.s3.amazonaws.com/optinpanda/mailing-services/gray/sendgrid.png',
            'hover' => 'https://cconp.s3.amazonaws.com/optinpanda/mailing-services/colored/sendgrid.png',
            
            'class' => 'OPanda_SendGridSubscriptionService',
            'path' => OPTINPANDA_DIR . '/plugin/includes/subscription/sendgrid/class.sendgrid.php',
            'modes' => array('quick'),
            
            'transactional' => true
        ),
        'sgautorepondeur' => array(
            'title' => __('SG Autorepondeur', 'optinpanda'),
            'description' => __('Adds subscribers to your SG Autorepondeur account.', 'optinpanda'),
            
            'image' => 'https://cconp.s3.amazonaws.com/optinpanda/mailing-services/gray/sgautorepondeur.png',
            'hover' => 'https://cconp.s3.amazonaws.com/optinpanda/mailing-services/colored/sgautorepondeur.png',
            
            'class' => 'OPanda_SGAutorepondeurSubscriptionService',
            'path' => OPTINPANDA_DIR . '/plugin/includes/subscription/sgautorepondeur/class.sgautorepondeur.php',
            'modes' => array('quick'),
            
            'manualList' => true
        )   
    );
    
    return array_merge( $services, $items );
}

add_action('opanda_subscription_services', 'optinpanda_subscription_services');
