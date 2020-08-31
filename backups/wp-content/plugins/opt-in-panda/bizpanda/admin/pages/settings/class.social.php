<?php
/**
 * A class for the page providing the social settings.
 * 
 * @author Paul Kashtanoff <paul@byonepress.com>
 * @copyright (c) 2014, OnePress Ltd
 * 
 * @package core 
 * @since 1.0.0
 */

/**
 * The Social Settings
 * 
 * @since 1.0.0
 */
class OPanda_SocialSettings extends OPanda_Settings  {
    
    public $id = 'social';
    
    /**
     * Shows the header html of the settings screen.
     * 
     * @since 1.0.0
     * @return void
     */
    public function header() {
        ?>
        <p><?php _e('Set up here your social API keys and app IDs for social buttons.', 'optionpanda') ?></p>
        <?php
    }
    
    /**
     * Returns subscription options.
     * 
     * @since 1.0.0
     * @return mixed[]
     */
    public function getOptions() {
        
        $languages = array(
            array('ca_ES', __('Catalan', 'bizpanda')),
            array('cs_CZ', __('Czech', 'bizpanda')),
            array('cy_GB', __('Welsh', 'bizpanda')),
            array('da_DK', __('Danish', 'bizpanda')),
            array('de_DE', __('German', 'bizpanda')),
            array('eu_ES', __('Basque', 'bizpanda')),
            array('en_US', __('English', 'bizpanda')),
            array('es_ES', __('Spanish', 'bizpanda')),
            array('fi_FI', __('Finnish', 'bizpanda')), 
            array('fr_FR', __('French', 'bizpanda')), 
            array('gl_ES', __('Galician', 'bizpanda')), 
            array('hu_HU', __('Hungarian', 'bizpanda')),
            array('it_IT', __('Italian', 'bizpanda')),
            array('ja_JP', __('Japanese', 'bizpanda')),
            array('ko_KR', __('Korean', 'bizpanda')),
            array('nb_NO', __('Norwegian', 'bizpanda')),
            array('nl_NL', __('Dutch', 'bizpanda')),
            array('pl_PL', __('Polish', 'bizpanda')),
            array('pt_BR', __('Portuguese (Brazil)', 'bizpanda')),
            array('pt_PT', __('Portuguese (Portugal)', 'bizpanda')),
            array('ro_RO', __('Romanian', 'bizpanda')),
            array('ru_RU', __('Russian', 'bizpanda')),
            array('sk_SK', __('Slovak', 'bizpanda')),  
            array('sl_SI', __('Slovenian', 'bizpanda')), 
            array('sv_SE', __('Swedish', 'bizpanda')),
            array('th_TH', __('Thai', 'bizpanda')),
            array('tr_TR', __('Turkish', 'bizpanda')), 
            array('ku_TR', __('Kurdish', 'bizpanda')), 
            array('zh_CN', __('Simplified Chinese (China)', 'bizpanda')), 
            array('zh_HK', __('Traditional Chinese (Hong Kong)', 'bizpanda')),
            array('zh_TW', __('Traditional Chinese (Taiwan)', 'bizpanda')), 
            array('af_ZA', __('Afrikaans', 'bizpanda')),
            array('sq_AL', __('Albanian', 'bizpanda')),
            array('hy_AM', __('Armenian', 'bizpanda')),
            array('az_AZ', __('Azeri', 'bizpanda')),
            array('be_BY', __('Belarusian', 'bizpanda')),
            array('bn_IN', __('Bengali', 'bizpanda')),
            array('bs_BA', __('Bosnian', 'bizpanda')),
            array('bg_BG', __('Bulgarian', 'bizpanda')),
            array('hr_HR', __('Croatian', 'bizpanda')),
            array('nl_BE', __('Dutch (Belgie)', 'bizpanda')),
            array('eo_EO', __('Esperanto', 'bizpanda')),
            array('et_EE', __('Estonian', 'bizpanda')),
            array('fo_FO', __('Faroese', 'bizpanda')),
            array('ka_GE', __('Georgian', 'bizpanda')),
            array('el_GR', __('Greek', 'bizpanda')),
            array('gu_IN', __('Gujarati', 'bizpanda')),
            array('hi_IN', __('Hindi', 'bizpanda')),
            array('is_IS', __('Icelandic', 'bizpanda')),
            array('id_ID', __('Indonesian', 'bizpanda')),
            array('ga_IE', __('Irish', 'bizpanda')),
            array('jv_ID', __('Javanese', 'bizpanda')),
            array('kn_IN', __('Kannada', 'bizpanda')),
            array('kk_KZ', __('Kazakh', 'bizpanda')),
            array('la_VA', __('Latin', 'bizpanda')),
            array('lv_LV', __('Latvian', 'bizpanda')),
            array('li_NL', __('Limburgish', 'bizpanda')),
            array('lt_LT', __('Lithuanian', 'bizpanda')), 
            array('mk_MK', __('Macedonian', 'bizpanda')), 
            array('mg_MG', __('Malagasy', 'bizpanda')),
            array('ms_MY', __('Malay', 'bizpanda')),
            array('mt_MT', __('Maltese', 'bizpanda')),
            array('mr_IN', __('Marathi', 'bizpanda')),
            array('mn_MN', __('Mongolian', 'bizpanda')),
            array('ne_NP', __('Nepali', 'bizpanda')),
            array('pa_IN', __('Punjabi', 'bizpanda')),
            array('rm_CH', __('Romansh', 'bizpanda')),
            array('sa_IN', __('Sanskrit', 'bizpanda')),
            array('sr_RS', __('Serbian', 'bizpanda')),
            array('so_SO', __('Somali', 'bizpanda')),
            array('sw_KE', __('Swahili', 'bizpanda')),
            array('tl_PH', __('Filipino', 'bizpanda')),
            array('ta_IN', __('Tamil', 'bizpanda')),
            array('tt_RU', __('Tatar', 'bizpanda')), 
            array('te_IN', __('Telugu', 'bizpanda')),
            array('ml_IN', __('Malayalam', 'bizpanda')),
            array('uk_UA', __('Ukrainian', 'bizpanda')),
            array('uz_UZ', __('Uzbek', 'bizpanda')),
            array('vi_VN', __('Vietnamese', 'bizpanda')),
            array('xh_ZA', __('Xhosa', 'bizpanda')),
            array('zu_ZA', __('Zulu', 'bizpanda')),
            array('km_KH', __('Khmer', 'bizpanda')),
            array('tg_TJ', __('Tajik', 'bizpanda')),
            array('ar_AR', __('Arabic', 'bizpanda')), 
            array('he_IL', __('Hebrew', 'bizpanda')),
            array('ur_PK', __('Urdu', 'bizpanda')),
            array('fa_IR', __('Persian', 'bizpanda')),
            array('sy_SY', __('Syriac', 'bizpanda')),  
            array('yi_DE', __('Yiddish', 'bizpanda')),
            array('gn_PY', __('Guarani', 'bizpanda')),
            array('qu_PE', __('Quechua', 'bizpanda')),
            array('ay_BO', __('Aymara', 'bizpanda')),
            array('se_NO', __('Northern Sami', 'bizpanda')),
            array('ps_AF', __('Pashto', 'bizpanda'))
        );
        
        
        
        $options = array();
        
        $options[] = array(
            'type' => 'separator'
        );

        $options[] = array(
            'type' => 'html',
            'html' =>   '<div class="col-md-offset-2" style="padding: 10px 0 10px 0;">' .
                '<strong style="font-size: 15px;">' . __('Social Buttons', 'bizpanda') . '</strong>' .
                '<p>' . __('Options to configure native social buttons (Like, Share, Tweet, Subscribe).', 'bizpanda') . '</p>' .
                '</div>'
        );

        $options[] = array(
            'type'      => 'checkbox',
            'way'       => 'buttons',
            'name'      => 'lazy',
            'title'     => __( 'Lazy Loading', 'bizpanda' ),
            'hint'      => __( 'If on, start loading resources needed for the buttons only when the locker gets visible on the screen on scrolling. Speeds up loading the website.', 'bizpanda' )
        );

        $options[] = array(
            'type' => 'separator'
        );

        $options[] = array(
            'type'      => 'dropdown',
            'name'      => 'lang',
            'title'     => __( 'Language of Buttons', 'bizpanda' ),
            'data'      => $languages,
            'hint'      => sprintf( __( 'Optional. Select the language that will be used for the social buttons. Used only with the native buttons.', 'bizpanda' ), opanda_get_settings_url('text') )
        );

        $options[] = array(
            'type'      => 'dropdown',
            'way'       => 'buttons',
            'name'      => 'facebook_version',
            'title'     => __( 'Facebook API Version', 'bizpanda' ),
            'default'   => 'v5.0',
            'data'      => array(
                array('v5.0', 'v5.0'),
                array('v6.0', 'v6.0'),
                array('v7.0', 'v7.0'),
            ),
            'hint'      => __( 'Optional. Use the most recent version of the API by default.', 'bizpanda' )
        );

        $options[] = array(
            'type' => 'separator'
        );

        $options[] = array(
            'type'      => 'checkbox',
            'way'       => 'buttons',
            'name'      => 'own_apps_for_permissions',
            'title'     => __( 'Use Own Apps<br />To Request Permissions', 'bizpanda' ),
            'hint'      => __( 'Optional. Some social buttons require a user to grant a set of permissions to perform social actions. It works fine out-of-box by using embedded social apps. At the case if you wish to display a logo of your website when a user grants the permissions you need to register your own social apps.', 'bizpanda' )
        );

        $options[] = array(
            'type'      => 'div',
            'id'        => 'own_social_apps_wrap',
            'items'     => $this->getSocialAppsOptions()
        );

        $options[] = array(
            'type' => 'separator'
        );

        $options[] = array(
            'type' => 'html',
            'html' =>   '<div class="col-md-offset-2" style="padding: 10px 0 10px 0;">' .
                '<strong style="font-size: 15px;">' . __('Sign-In Buttons', 'bizpanda') . '</strong>' .
                '<p>' . __('Options to configure sign-in buttons used with sign-in lockers.', 'bizpanda') . '</p>' .
                '</div>'
        );

        $options[] = array(
            'type'      => 'checkbox',
            'way'       => 'buttons',
            'name'      => 'own_apps_to_signin',
            'title'     => __( 'Use Own Apps<br />To Sign-In Users', 'bizpanda' ),
            'hint'      => __( 'Optional. Sign-In buttons work fine out-of-box by using embedded social apps. Set your own apps only if you wish to display a logo of your website when a user signs-in.', 'bizpanda' )
        );

        $options[] = array(
            'type'      => 'div',
            'id'        => 'own_signin_apps_wrap',
            'items'     => $this->getSignInAppsOptions()
        );

        $options[] = array(
            'type' => 'separator'
        );

        return $options;
    }

    /**
     * Returns options for social buttons.
     */
    protected function getSocialAppsOptions() {

        $options[] = array(
            'type' => 'separator'
        );

        $options[] = array(
            'type'      => 'textbox',
            'name'      => 'facebook_app_id',
            'title'     => __( 'Facebook App ID', 'bizpanda' ),
            'hint'      =>  sprintf( __( 'The App ID of your Facebook App.', 'bizpanda' ), admin_url('admin.php?page=how-to-use-' . $this->plugin->pluginName . '&onp_sl_page=facebook-app') ),
            'after'     => sprintf( __( '<a href="%s" class="btn btn-default">How To Get</a>', 'bizpanda' ), admin_url('admin.php?page=how-to-use-' . $this->plugin->pluginName . '&onp_sl_page=facebook-app') )
        );

        $options[] = array(
            'type' => 'separator'
        );

        $options[] = array(
            'type'      => 'textbox',
            'name'      => 'google_client_id',
            'title'     => __( 'Google Client ID', 'bizpanda' ),
            'after'     => sprintf( __( '<a href="%s" class="btn btn-default">How To Get</a>', 'bizpanda' ), admin_url('admin.php?page=how-to-use-' . $this->plugin->pluginName . '&onp_sl_page=google-client-id') ),
            'hint'      => sprintf( __( 'The Google Client ID of your Google App.', 'bizpanda' ) )
        );

        $options[] = array(
            'type'      => 'textbox',
            'name'      => 'google_client_secret',
            'title'     => __( 'Google Client Secret', 'bizpanda' ),
            'hint'      => __( 'The Google Client Secret of your Google App.', 'bizpanda' )
        );

        $options[] = array(
            'type' => 'separator'
        );

        $options[] = array(
            'type'      => 'textbox',
            'name'      => 'twitter_social_app_consumer_key',
            'title'     => __( 'Twitter App Key', 'bizpanda' ),
            'after'     => sprintf( __( '<a href="%s" class="btn btn-default">How To Get</a>', 'bizpanda' ), admin_url('admin.php?page=how-to-use-' . $this->plugin->pluginName . '&onp_sl_page=twitter-app') ),
            'hint'      => sprintf( __( 'The Twitter Consumer Key of your Twitter App (set only "Read" permission).', 'bizpanda' ) )
        );

        $options[] = array(
            'type'      => 'textbox',
            'name'      => 'twitter_social_app_consumer_secret',
            'title'     => __( 'Twitter App Key Secret', 'bizpanda' ),
            'hint'      => __( 'The Twitter Consumer Secret of your Twitter App.', 'bizpanda' ),
            'for'       => array(__('Connect Locker', 'bizpanda'))
        );

        return $options;
    }

    /**
     * Returns options for sign-in buttons.
     */
    protected function getSignInAppsOptions() {

        $options[] = array(
            'type' => 'separator'
        );

        $options[] = array(
            'type'      => 'textbox',
            'name'      => 'facebook_app_id',
            'title'     => __( 'Facebook App ID', 'bizpanda' ),
            'after'     => sprintf( __( '<a href="%s" class="btn btn-default">How To Get</a>', 'bizpanda' ), admin_url('admin.php?page=how-to-use-' . $this->plugin->pluginName . '&onp_sl_page=facebook-app') ),
            'hint'      => sprintf( __( 'The Facebook App ID of your Facebook App.', 'bizpanda' ) )
        );

        $options[] = array(
            'type'      => 'textbox',
            'name'      => 'facebook_app_secret',
            'title'     => __( 'Facebook App Secret', 'bizpanda' ),
            'hint'      => __( 'The Facebook App Secret of your Facebook App.', 'bizpanda' )
        );

        $options[] = array(
            'type' => 'separator'
        );

        $options[] = array(
            'type'      => 'textbox',
            'name'      => 'google_client_id',
            'title'     => __( 'Google Client ID', 'bizpanda' ),
            'after'     => sprintf( __( '<a href="%s" class="btn btn-default">How To Get</a>', 'bizpanda' ), admin_url('admin.php?page=how-to-use-' . $this->plugin->pluginName . '&onp_sl_page=google-client-id') ),
            'hint'      => sprintf( __( 'The Google Client ID of your Google App.', 'bizpanda' ) )
        );

        $options[] = array(
            'type'      => 'textbox',
            'name'      => 'google_client_secret',
            'title'     => __( 'Google Client Secret', 'bizpanda' ),
            'hint'      => __( 'The Google Client Secret of your Google App.', 'bizpanda' )
        );

        $options[] = array(
            'type' => 'separator'
        );

        $options[] = array(
            'type'      => 'textbox',
            'name'      => 'twitter_signin_app_consumer_key',
            'title'     => __( 'Twitter App Key', 'bizpanda' ),
            'after'     => sprintf( __( '<a href="%s" class="btn btn-default">How To Get</a>', 'bizpanda' ), admin_url('admin.php?page=how-to-use-' . $this->plugin->pluginName . '&onp_sl_page=twitter-app') ),
            'hint'      => sprintf( __( 'The Twitter Consumer Key of your Twitter App (set "Read" and "Write" permissions).', 'bizpanda' ) )
        );

        $options[] = array(
            'type'      => 'textbox',
            'name'      => 'twitter_signin_app_consumer_secret',
            'title'     => __( 'Twitter App Key Secret', 'bizpanda' ),
            'hint'      => __( 'The Twitter Consumer Secret of your Twitter App.', 'bizpanda' ),
            'for'       => array(__('Connect Locker', 'bizpanda'))
        );

        $options[] = array(
            'type' => 'separator'
        );

        $options[] = array(
            'type'      => 'textbox',
            'name'      => 'linkedin_client_id',
            'title'     => __( 'LinkedIn Client ID', 'bizpanda' ),
            'after'     => sprintf( __( '<a href="%s" class="btn btn-default">How To Get</a>', 'bizpanda' ), admin_url('admin.php?page=how-to-use-' . $this->plugin->pluginName . '&onp_sl_page=linkedin-api-key') ),
            'hint'      => sprintf( __( 'The LinkedIn Client ID of your LinkedIn App.', 'bizpanda' ) )
        );

        $options[] = array(
            'type'      => 'textbox',
            'name'      => 'linkedin_client_secret',
            'title'     => __( 'LinkedIn Client Secret', 'bizpanda' ),
            'hint'      => __( 'The LinkedIn Client Secret of your LinkedIn App.', 'bizpanda' )
        );

        return $options;
    }
}

