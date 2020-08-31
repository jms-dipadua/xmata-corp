<?php

/**
 * The base class of the BizPanda Framework.
 * 
 * @since 1.0.0
 */
class BizPanda {
    
    /**
     * Stores the number of the plugins using the BizPanda Framework.
     * 
     * @since 1.0.0
     * @var int 
     */
    protected static $pluginCount = 1;

    /**
     * Returns the number of plugins using the BizPanda Framework.
     * 
     * @since 1.0.0
     * @var int 
     */
    public static function getPluginCount() {
        return count( self::$_installedPlugins );
    }
    
    /**
     * [obsoleted]
     * 
     * @since 1.0.0
     * @var void 
     */
    public static function countCallerPlugin() {
        // nothing
    }
    
    /**
     * Returns true if only one plugin is usigin the  Framework.
     * 
     * @since 1.0.0
     * @var bool 
     */
    public static function isSinglePlugin() {
        return count( self::$_installedPlugins ) == 1;
    }
    
    protected static $_features = array();
    
    public static function hasFeature( $featureName ) {
        return isset( self::$_features[$featureName] ) && self::$_features[$featureName];
    }
    
    public static function enableFeature( $featureName ) {
        self::$_features[$featureName] = true;
    }
    
    public static function disableFeature( $featureName ) {
        self::$_features[$featureName] = true;
    } 
    
    
    protected static $_plugins = array();
    protected static $_installedPlugins = array();
    
    protected static $_hasPremiumPlugins = false;
    
    public static function hasPlugin( $name ) {
        return isset( self::$_plugins[$name] );
    }
    
    public static function registerPlugin( $plugin, $name = null, $type = null ) {
        $pluginName = empty( $name ) ? $plugin->pluginName : $name;
        $pluginType = empty( $type ) ? ( $plugin->options['build'] !== 'free' ? 'premium' : 'free' ) : $type;
        
        if ( !isset( self::$_plugins[$pluginName] ) ) self::$_plugins[$pluginName] = array();
        self::$_plugins[$pluginName][$pluginType] = $plugin;
        
        self::$_installedPlugins[] = array(
            'name' => $pluginName,
            'type' => $type,
            'plugin' => $plugin
        );

        self::$_hasPremiumPlugins = self::$_hasPremiumPlugins || 'premium' === $pluginType;
    }  

    public static function hasDefaultMenuIcon() {
        $default = OPANDA_BIZPANDA_URL . '/assets/admin/img/menu-icon.png';
        $current = self::getMenuIcon();
        return $default == $current;        
    }
    
    public static function getMenuIcon() {
        $default = 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyMCAyNiIgZmlsbD0iIzllYTNhOCIgc2hhcGUtcmVuZGVyaW5nPSJnZW9tZXRyaWNQcmVjaXNpb24iPjxwYXRoIGQ9Ik0zLjQzNDQ2LDE3Ljc1MTA2TDEuNzUsMTkuODE1OTRhMTQuODQyNDcsMTQuODQyNDcsMCwwLDAsMS42MjU2NywxLjMwOTY4QTcuMjY4MzcsNy4yNjgzNywwLDAsMSwzLjM2MTQ1LDE4LjA2OFEzLjM5NDc1LDE3LjkwOTE0LDMuNDM0NDYsMTcuNzUxMDZaIi8+PHBhdGggZD0iTTMuNTY2ODksMjEuNDEwMTZsLTAuMjYyNy0uMTgxNjRBMTUuMDQ3NjEsMTUuMDQ3NjEsMCwwLDEsMS42NjUsMTkuOTA4MkwxLjU3OTEsMTkuODI4MTNsMi4xMTI3OS0yLjU4OTg0LTAuMTM2MjMuNTQzYy0wLjAyNTg4LjEwNDQ5LS4wNDk4LDAuMjA4LTAuMDcyMjcsMC4zMTI1YTcuMTA2MzksNy4xMDYzOSwwLDAsMCwuMDE0MTYsMy4wMDQ4OFpNMS45MjEzOSwxOS44MDM3MWExNC42NjQyNCwxNC42NjQyNCwwLDAsMCwxLjI3LDEuMDM4MDksNy4zNTc0Myw3LjM1NzQzLDAsMCwxLC4wMTAyNS0yLjYwNzQyWiIvPjxwYXRoIGQ9Ik01Ljk0MTE0LDE3LjUyMDc0cS0wLjE0MjIyLS4xMDQ4NC0wLjI4NTgxLTAuMjE2MTFjLTAuNjI0MTgsMS41MjE5LS41MTgyNyw0LjEyNjY2Ljk3NDIzLDUuMjg0ODgtMC4wMzIzMi0uMDQyODMtMC4wNjM4Ni0wLjA4NjI1LTAuMDk1LTAuMTMwNDRBNS4zMzU1OCw1LjMzNTU4LDAsMCwxLDUuOTQxMTQsMTcuNTIwNzRaIi8+PHBhdGggZD0iTTcuOTQ1MzEsMjMuOTI3NzNMNi40NzYwNywyMi43ODcxMUM0LjgyMzI0LDIxLjUwMzkxLDQuODAzMjIsMTguNzIzNjMsNS40MjM4MywxNy4yMWwwLjEyNS0uMzA0NjksMC4yNTk3NywwLjIwMjE1YzAuMDkzNzUsMC4wNzIyNy4xODcsMC4xNDM1NSwwLjI3OTc5LDAuMjEwOTRsMC4xNTIzNCwwLjExMTMzLTAuMDY1NDMuMTc3NzNhNS4xMDM0LDUuMTAzNCwwLDAsMCwuNTY0LDQuNzA4WiIvPjxwYXRoIGQ9Ik0xMS4zNTYwNywxNS40MDk3YTMuMTAxODEsMy4xMDE4MSwwLDAsMC0uMzUxODEtMC4wMjkyLDIuODQ4LDIuODQ4LDAsMCwxLDIuNDcyNTcsMi43MDIzMWMwLjA0OTA2LDMuMDQwMDktMy45NTE0NCwzLjgyMzkyLTQuMDAyNjUsMS4xNzQ1N1E5LjI4NDM3LDE5LjIxNTYyLDkuMDkyNCwxOS4xNjEyYy0wLjI3MDYyLDMuMTg2MTEsNC45MDI1MSwyLjUzNiw0LjY4NDg0LTEuMTc1QTIuNjk5NDQsMi42OTk0NCwwLDAsMCwxMS4zNTYwNywxNS40MDk3WiIvPjxwYXRoIGQ9Ik0xMC45MzMxMSwyMS40NTQxQTIuMjg5NTQsMi4yODk1NCwwLDAsMSw5LjcxMjg5LDIxLjEyN2EyLjAwOSwyLjAwOSwwLDAsMS0uODY5NjMtMS45ODczbDAuMDI1MzktLjMwMDc4LDAuMjkxNSwwLjA4MmMwLjEyNCwwLjAzNDE4LjI0NjU4LDAuMDY1NDMsMC4zNjg2NSwwLjA5Mjc3bDAuMTkxNDEsMC4wNDMsMC4wMDM5MSwwLjE5NjI5YTEuMjMxODcsMS4yMzE4NywwLDAsMCwxLjI4MjcxLDEuMzYzMjhIMTEuMDRhMi4zNzMsMi4zNzMsMCwwLDAsMi4xODY1Mi0yLjUyOTMsMi42MTEsMi42MTEsMCwwLDAtMi4yNjcwOS0yLjQ2bDAuMDUwMjktLjQ5NjA5YTMuMDk3NjIsMy4wOTc2MiwwLDAsMSwuMzgxODQuMDMyMjMsMi45NDAzNCwyLjk0MDM0LDAsMCwxLDIuNjM1NzQsMi44MDg1OSwzLjE4ODM1LDMuMTg4MzUsMCwwLDEtMS41MzQxOCwzLjA0NTlBMy4xMDEyMiwzLjEwMTIyLDAsMCwxLDEwLjkzMzExLDIxLjQ1NDFaIi8+PHBhdGggZD0iTTE2LjUzMDU2LDE0LjI4OTA3YTYuNDEwODQsNi40MTA4NCwwLDAsMC00LjM5NDk1LTIuNjM5NjIsNy4zOTM0Nyw3LjM5MzQ3LDAsMCwwLTUuMjc3MjksMS4zMTMxOSwwLjc3NzQ2LDAuNzc3NDYsMCwxLDEtLjg5NS0xLjI3MTUyLDkuMTI3NjEsOS4xMjc2MSwwLDAsMSwyLjkxNzI1LTEuMzY0MzksMy41MzY0MSwzLjUzNjQxLDAsMCwxLS41Mzc1NC0wLjI3NTFBMS41NTg1MSwxLjU1ODUxLDAsMCwxLDcuNTY5MTQsOC42ODdhMS42OTY1NywxLjY5NjU3LDAsMCwxLC42MzMxMy0xLjM3OSwyLjY0NTE2LDIuNjQ1MTYsMCwwLDEsMS43MzA2MS0uNTIwNDFBOC42NjEyLDguNjYxMiwwLDAsMSwxNS4yMjMsOC43MjkwOWwyLjIyMy0zLjIwNzcyYTEwLjU0OTc1LDEwLjU0OTc1LDAsMCwwLTMuNDMzLTEuOTEzNjEsMTIuMDgzLDEyLjA4MywwLDAsMC0zLjkzOTM3LS42NzUxOUE4LjA5NTIxLDguMDk1MjEsMCwwLDAsNS4wNTA4Miw0LjQ1MjEyLDUuMDkyMTEsNS4wOTIxMSwwLDAsMCwzLjAzODg5LDguNzcxMzQsNS4wMTYzMyw1LjAxNjMzLDAsMCwwLDQuNjI4NzMsMTIuODc5NWE3LjY2NTI4LDcuNjY1MjgsMCwwLDAsMS41OTMzNS45ODA2Niw4LjI0NDg1LDguMjQ0ODUsMCwwLDEsLjgxMDExLTAuNjUwNDYsNi45MDc3OSw2LjkwNzc5LDAsMCwxLDQuOTMzMDctMS4yMTU4NCw2LjE1NTE0LDYuMTU1MTQsMCwwLDEsNC4yMTY4MSwyLjU0MDUyLDYuMjgyMzUsNi4yODIzNSwwLDAsMSwuNTI2MjUsNi4xOTUwNkE1LjQzNTQ4LDUuNDM1NDgsMCwwLDAsMTYuNTMwNTYsMTQuMjg5MDdaIi8+PHBhdGggZD0iTTE1LjY0MzU1LDIyLjUwMzkxbDAuODM1OTQtMS44NzZhNi4wMDIyNyw2LjAwMjI3LDAsMCwwLS41MDItNS45NTAyLDUuODg0OTIsNS44ODQ5MiwwLDAsMC00LjA0Nzg1LTIuNDM2NTIsNi42Mjk2Niw2LjYyOTY2LDAsMCwwLTQuNzUzNDIsMS4xNzI4NSw4LjA2MTgsOC4wNjE4LDAsMCwwLS43ODU2NC42MzA4NmwtMC4xMjQuMTEzMjgtMC4xNTEzNy0uMDcyMjdhNy45MTgsNy45MTgsMCwwLDEtMS42NDU1MS0xLjAxMzY3QTUuMjcwNzcsNS4yNzA3NywwLDAsMSwyLjc4OTA2LDguNzcxNDhhNS4zNTIsNS4zNTIsMCwwLDEsMi4xMTA4NC00LjUxOSw4LjM3NzkyLDguMzc3OTIsMCwwLDEsNS4xNzM4My0xLjU2OTgyLDEyLjM3NjI5LDEyLjM3NjI5LDAsMCwxLDQuMDIuNjg5LDEwLjgzMjc5LDEwLjgzMjc5LDAsMCwxLDMuNTEzNjcsMS45NTlsMC4xNzM4MywwLjE0Ny0yLjUwMiwzLjYwODQtMC4yMDgtLjE1NzcxQTguNDUzNjgsOC40NTM2OCwwLDAsMCw5LjkzMzExLDcuMDM3NmEyLjQxNzc1LDIuNDE3NzUsMCwwLDAtMS41NzIyNy40NjM4N0ExLjQ0NiwxLjQ0NiwwLDAsMCw3LjgxOTM0LDguNjg3LDEuMzE0MDYsMS4zMTQwNiwwLDAsMCw4LjQ4LDkuODQyNzdhMy4zMjg1NSwzLjMyODU1LDAsMCwwLC40OTg1NC4yNTM5MUw5LjY0NywxMC4zODE4NGwtMC43MDI2NC4xODY1MmE4Ljg1OTczLDguODU5NzMsMCwwLDAtMi44MzY5MSwxLjMyNzE1LDAuNTI3MjQsMC41MjcyNCwwLDEsMCwuNjA2OTMuODYyMyw3LjYyLDcuNjIsMCwwLDEsNS40NTY1NC0xLjM1Niw2LjYzMDA4LDYuNjMwMDgsMCwwLDEsNC41NjM0OCwyLjc0MzY1aDBhNS42ODgsNS42ODgsMCwwLDEsLjE3MDksNi43MzczWk0xMC4wNzM3MywzLjE4MjYyQTcuODg1NjQsNy44ODU2NCwwLDAsMCw1LjIwMTY2LDQuNjUxODZhNC44MTMyLDQuODEzMiwwLDAsMC0xLjkxMjYsNC4xMTk2Myw0LjgwNDA5LDQuODA0MDksMCwwLDAsMS40OTg1NCwzLjkxNSw3LjI0NDUzLDcuMjQ0NTMsMCwwLDAsMS4zOTI1OC44NzZjMC4wODEwNS0uMDcxMjkuMTYzNTctMC4xNDA2MiwwLjI0NzU2LTAuMjA5YTEuMDU4NjIsMS4wNTg2MiwwLDAsMS0uODU3NDItMC40MzUwNiwxLjAyOTE2LDEuMDI5MTYsMCwwLDEsLjI0OS0xLjQzMjEzQTkuMzk5OTEsOS4zOTk5MSwwLDAsMSw4LjIxNDg0LDEwLjI2NjZsLTAuMDA4NzktLjAwNTg2QTEuODAxNTQsMS44MDE1NCwwLDAsMSw3LjMxOTM0LDguNjg3YTEuOTQyNTEsMS45NDI1MSwwLDAsMSwuNzI0MTItMS41NzE3OEEyLjg5NzE5LDIuODk3MTksMCwwLDEsOS45MzMxMSw2LjUzNzZhOC44MzU2NSw4LjgzNTY1LDAsMCwxLDUuMjMxLDEuODM2OTFsMS45NDYyOS0yLjgwNjY0YTEwLjQzNDMxLDEwLjQzNDMxLDAsMCwwLTMuMTc4NzEtMS43MjM2M0ExMS44ODUxOCwxMS44ODUxOCwwLDAsMCwxMC4wNzM3MywzLjE4MjYyWiIvPjwvc3ZnPg==';
        return apply_filters('opanda_menu_icon', $default );
    }
    
    public static function getShortCodeIcon() {
        $default = OPANDA_BIZPANDA_URL . '/assets/admin/img/opanda-shortcode-icon.png';
        return apply_filters('opanda_shortcode_icon', $default );
    }
    
    public static function getMenuTitle() {
        $menuTitle = __('Biz<span class="onp-sl-panda">Panda</span>', 'bizpanda');
        return apply_filters('opanda_menu_title', $menuTitle );      
    }
    
    public static function getSubscriptionServiceName() {
        return get_option('opanda_subscription_service', 'database'); 
    }
    
    public static function hasPremiumPlugins() {
        return self::$_hasPremiumPlugins;
    }
    
    public static function getPlugin() {

        if ( isset( self::$_installedPlugins[0] )) return self::$_installedPlugins[0]['plugin'];
        return false;
    }

    public static function getPluginNames( $full = false ) {
        if ( !$full ) return array_keys( self::$_plugins );
        
        $names = array();
        foreach( self::$_installedPlugins as $pluginInfo ) {
            $plugin = self::$_installedPlugins[0]['plugin'];
            $name = $plugin->options['name'] . '-' .$plugin->options['assembly']; 
            $names[] = $name;
        }
        
        return $names;
    }
    
    public static function getInstalledPlugins() {
        return self::$_installedPlugins;
    }
    
    public static function hasInstalled( $pluginName, $pluginType = null ) {
        
        if ( empty( $pluginType ) ) {
            return isset( self::$_plugins[$pluginName] );
        } else {
            return isset( self::$_plugins[$pluginName][$pluginType] );
        }
    }
}

/**
 * Returns an URL of the admin page of Business Panda.
 * 
 * @since 1.0.0
 * 
 * @param string $page A page id (for example, how-to-use).
 * @param array $args Extra query args.
 * @return string
 */
function opanda_get_admin_url( $page = 'how-to-use', $args = array() ) {
    $baseUrl = admin_url('edit.php?post_type=' . OPANDA_POST_TYPE);
    
    $args['page'] = $page . '-bizpanda';
    return add_query_arg( $args, $baseUrl );
}

function opanda_get_help_url( $page = null ) {
    return opanda_get_admin_url( 'how-to-use', array('onp_sl_page' => $page) );
}

function opanda_get_subscribers_url() {
    return opanda_get_admin_url('leads');
}

function opanda_get_settings_url( $screen ) {
    return opanda_get_admin_url( 'settings', array('opanda_screen' => $screen) ); 
}

/**
 * Returns an URL of local proxy to perform social actions, to subscribe, to save data.
 * @return string
 */
function opanda_local_proxy_url($args = []) {

    $url = admin_url('admin-ajax.php');
    return add_query_arg( array_merge( array(
        'action' => 'opanda_connect'
    ), $args), $url);
}

/**
 * Returns an URL to make calls to remote social proxy.
 * @return string
 */
function opanda_remote_social_proxy_url() {

    if ( defined('ONP_DEVELOPING_MODE') && ONP_DEVELOPING_MODE ) {
        return 'http://app.sociallocker.developing';
    }

    return 'https://gate.sociallocker.app';
}

function opanda_terms_url( $force = false ) {
    $enabled = get_option('opanda_terms_enabled', false);
    if ( empty( $enabled ) && !$force ) return false;
    
    $usePages = get_option('opanda_terms_use_pages', false);
    if ( $usePages ) {
        
        $pageId = get_option('opanda_terms_of_use_page', false);   
        if ( !empty( $pageId ) ) return get_permalink( $pageId );
    }
    
    return add_query_arg(array(
        'bizpanda' => 'terms-of-use'
    ), site_url() );
}

function opanda_privacy_policy_url( $force = false ) {
    $enabled = get_option('opanda_privacy_enabled', false);
    if ( empty( $enabled ) && !$force  ) return false;
    
    $usePages = get_option('opanda_terms_use_pages', false);
    if ( $usePages ) {
        
        $pageId = get_option('opanda_privacy_policy_page', false);   
        if ( !empty( $pageId ) ) return get_permalink( $pageId );
        
    }
    
    return add_query_arg(array(
        'bizpanda' => 'privacy-policy'
    ), site_url() );
}

/**
 * Returns the global option for the panda item.
 * 
 * @since 1.0.0
 */
function opanda_get_option( $id, $default = null ) {
    return get_option( 'opanda_' . $id, $default ) ;
}

/**
 * Returns the option for a given panda item.
 * 
 * @since 1.0.0
 */
function opanda_get_item_option( $id, $name, $isArray = false, $default = null ) {
    $options = opanda_get_item_options( $id );
    $value = isset( $options['opanda_' . $name] ) ? $options['opanda_' . $name] : null;

    return ($value === null || $value === '')
        ? $default 
        : ( $isArray ? maybe_unserialize($value) : stripslashes( $value ) ); 
}

/**
 * Replaces the variables in URLs {var} and return the result URL.
 * @param $id integer A locker ID.
 * @param $name string An option name.
 * @param $default string A default value.
 * @return string|string[]|null
 */
function opanda_get_dynamic_url( $id, $name, $default = null ) {
    $url = opanda_get_item_option( $id, $name, false );
    
    if( empty( $url ) ) return $default;
    return preg_replace_callback("/\{([^}]+)\}/", 'opanda_get_dynamic_url_callback', $url);
}

/**
 * A callback for 'preg_replace_callback' in the function opanda_get_dunamic_url.
 * 
 * @since 1.1.3
 */
function opanda_get_dynamic_url_callback( $match ) {
    if( array_key_exists( $match[1], $_REQUEST ) ) return $_REQUEST[$match[1]];
    return $match[0];
}

/**
 * Cache for the locker options.
 */
global $opanda_item_options;
$opanda_item_options = array();

/**
 * Returns all the options for a given panda item.
 * 
 * @since 1.0.0
 */
function opanda_get_item_options( $id ) {
    global $opanda_item_options;
    if ( isset( $opanda_item_options[$id] ) ) return $opanda_item_options[$id];
    
    $options = get_post_meta($id, '');

    $real = array();
    foreach($options as $key => $values) {
        if ( !strpos($key, '__arr') ) $real[$key] = $values[0];
        else $real[$key] = $values;
    }

    $opanda_item_options[$id] = $real;
    return $real;
}

/**
 * Normilize the values after receving them via ajax.
 * 
 * @since 1.0.0
 */
function opanda_normilize_values( $values = array() ) {
    if ( empty( $values) ) return $values;
    if ( !is_array( $values ) ) $values = array( $values );

    foreach ( $values as $index => $value ) {

        $values[$index] = is_array( $value )
                    ? opanda_normilize_values( $value ) 
                    : opanda_normilize_value( $value );
    }

    return $values;
}

/**
 * Normilize the value after receving them via ajax.
 * 
 * @since 1.0.0
 */
function opanda_normilize_value( $value = null ) {
    if ( 'false' === $value ) $value = false;
    elseif ( 'true' === $value ) $value = true;
    elseif ( 'null' === $value ) $value = null;
    return $value;
}

/**
 * Returns a website robust key to load failed assets.
 * 
 * @since 1.1.3
 */
function opanda_get_robust_key() {
    $key = get_option('opanda_robust_key', false);
    if ( empty( $key ) ) {
        $key = substr( md5( NONCE_SALT ), 0, rand(5,15) );
        update_option( 'opanda_robust_key', $key );
    }
    return $key;
}

/**
 * Returns a website robust script key to load the locker script.
 * 
 * @since 1.1.3
 */
function opanda_get_robust_script_key() {
    $key = get_option('opanda_robust_script_key', false);
    if ( empty( $key ) ) {
        $key = substr( md5( NONCE_SALT ), 15, rand(5,15) );
        update_option( 'opanda_robust_script_key', $key );
    }
    return $key;
}

/**
 * Returns available lockers.
 * 
 * @since 1.1.3
 */
function opanda_get_lockers( $lockerType = null, $output = null ) {
    
    $lockers = get_posts(array(
        'post_type' => OPANDA_POST_TYPE,
        'meta_key' => 'opanda_item',
        'meta_value' => empty( $lockerType ) ? OPanda_Items::getAvailableNames() : $lockerType,
        'numberposts' => -1
    ));
    
    foreach( $lockers as $locker ) {
        $locker->post_title = empty( $locker->post_title ) 
            ? sprintf( __( '(no titled, ID=%s)' ), $locker->ID )
            : $locker->post_title;
    } 
    
    if ( 'vc' === $output ) {
        
        $result = array();
        foreach ( $lockers as $locker ) $result[$locker->post_title] = $locker->ID;
        return $result;
    }
    
    return $lockers;
}

// ---------------------------------
// Gutenberg Blocks
// ---------------------------------

function bizpanda_register_blocks() {
    if (!function_exists('register_block_type')) return;

    wp_register_script(
        'bizpanda-locker-block-js',
        OPANDA_BIZPANDA_URL . '/assets/admin/js/blocks.010001.dist.js',
        array(
            'wp-blocks',
            'wp-block-editor',
            'wp-i18n',
            'wp-element',
            'wp-components'
        )
    );

    if ( BizPanda::hasPlugin('sociallocker') ) $block_types[] = 'sociallocker';
    if ( BizPanda::hasPlugin('sociallocker') || BizPanda::hasPlugin('optinpanda') ) $block_types[] = 'signinlocker';
    if ( BizPanda::hasPlugin('optinpanda') ) $block_types[] = 'emaillocker';

    wp_localize_script( 'bizpanda-locker-block-js', '__bizpanda_locker_blocks', [
        'blockTypes' => $block_types,
        'urlCreateNew' => opanda_get_admin_url('new-item'),
        'urlEditUrl' => admin_url('post.php?post={0}&action=edit')
    ]);

    wp_register_style(
        'bizpanda-lockers-css',
        OPANDA_BIZPANDA_URL . '/assets/admin/css/blocks.010001.css'
    );

    foreach( $block_types as $block_type ) {

        register_block_type('bizpanda/' . $block_type, array(
            'editor_script' => 'bizpanda-locker-block-js',
            'editor_style'  => 'bizpanda-lockers-css',
            'render_callback' => 'bizpanda_' . $block_type . '_block_render_callback',
            'attributes' => [
                'id' => [
                    'type' => 'number'
                ]
            ]
        ));
    }
}

function bizpanda_sociallocker_block_render_callback( $attributes, $content ) {
    $attributes['shortcode'] = 'sociallocker';
    return bizpanda_block_render_callback( $attributes, $content );
}

function bizpanda_signinlocker_block_render_callback( $attributes, $content ) {
    $attributes['shortcode'] = 'signinlocker';
    return bizpanda_block_render_callback( $attributes, $content );
}

function bizpanda_emaillocker_block_render_callback( $attributes, $content ) {
    $attributes['shortcode'] = 'emaillocker';
    return bizpanda_block_render_callback( $attributes, $content );
}

function bizpanda_block_render_callback( $attributes, $content ) {

    $lockerId = !empty( $attributes['id'] ) ? (int)$attributes['id'] : false;
    $shortcode = !empty( $attributes['shortcode'] ) ? $attributes['shortcode'] : false;

    if ( $lockerId ) {
        return "[$shortcode id='$lockerId']" . $content . "[/$shortcode]";
    } else {
        return "[$shortcode]" . $content . "[/$shortcode]";
    }
}

add_action('init', 'bizpanda_register_blocks');


// ---------------------------------
// Move to hooks.php
// ---------------------------------

/**
 * Handles a frontend action linked with bizpanda.
 * 
 * @since 1.1.0
 * @return void
 */
function bizpanda_frontend_action() {
    $robustKey = opanda_get_robust_key();
    
    if ( isset( $_REQUEST['bizpanda'] ) ) {
        
        $action = $_REQUEST['bizpanda'];

        if ( 'terms-of-use' === $action ) {
            return bizpanda_show_terms_of_use();
        }

        if ( 'privacy-policy' === $action ) {
            return bizpanda_show_privacy_policy();
        }

    } else if ( isset( $_REQUEST[$robustKey] ) ) {
        
        $action = $_REQUEST[$robustKey];
        
        if ( opanda_get_robust_script_key() === $action ) {
            echo file_get_contents(OPANDA_BIZPANDA_DIR . '/assets/js/lockers.min.js');
            exit;
        }
    }
}
add_action('template_redirect', 'bizpanda_frontend_action');

/**
 * Displays the text of the Terms of Use.
 * 
 * @since 1.1.0
 * @return void
 */
function bizpanda_show_terms_of_use() {
    
    $enabled = get_option('opanda_terms_enabled', false);
    if ( empty( $enabled ) ) return;
    
    $usePages = get_option('opanda_terms_use_pages', false);
    if ( $usePages ) return;
    
    ?>

    <html>
        <title><?php echo get_bloginfo('name'); ?></title>
        <link rel='stylesheet' href='<?php echo OPANDA_BIZPANDA_URL . '/assets/css/terms.css?' . BIZPANDA_VERSION ?>' type='text/css' media='all' />
        <body>
            <?php echo get_option('opanda_terms_of_use_text', false); ?>
        </body>
    <html>
        
    <?php
    exit;
}

/**
 * Displays the text of the Privacy Policy.
 * 
 * @since 1.1.0
 * @return void
 */
function bizpanda_show_privacy_policy() {
    
    $enabled = get_option('opanda_terms_enabled', false);
    if ( empty( $enabled ) ) return;
    
    $usePages = get_option('opanda_terms_use_pages', false);
    if ( $usePages ) return;
    
    ?>
        
    <html>
        <title><?php echo get_bloginfo('name'); ?></title>
        <link rel='stylesheet' href='<?php echo OPANDA_BIZPANDA_URL . '/assets/css/terms.css?' . BIZPANDA_VERSION ?>' type='text/css' media='all' />
        <body>
            <?php echo get_option('opanda_privacy_policy_text', false); ?>
        </body>
    <html>
        
    <?php
    exit;
}

/**
 * Confrims subscription made through Wordpress.
 */
function bizpanda_confrim_wp_subscription() {

    if ( !isset( $_GET['opanda_confirm'] ) ) return;
    if ( !isset( $_GET['opanda_lead'] ) ) return;
    if ( !isset( $_GET['opanda_code'] ) ) return;
    
    require_once OPANDA_BIZPANDA_DIR . '/admin/includes/leads.php';
    require_once OPANDA_BIZPANDA_DIR . '/admin/includes/stats.php';
        
    $leadId = (int)$_GET['opanda_lead'];
    $code = $_GET['opanda_code'];

    OPanda_Leads::confirm($leadId, $code, true);
}

add_action( 'init', 'bizpanda_confrim_wp_subscription' );

// ----------------------------------------------
// Visibility Parameters
// ----------------------------------------------

/**
 * Writes a current user role into the visibility vars.
 */
function bizpanda_visibility_param_user_role( $value ) {
    
    if ( !is_user_logged_in() ) return 'guest';
    else {
        $current_user = wp_get_current_user(); 
        if ( !($current_user instanceof WP_User) ) return $value;
        return $current_user->roles[0];
    }
}
add_filter('bp_visibility_param_user-role', 'bizpanda_visibility_param_user_role');

/**
 * Writes a timestamp when the user was registered the visibility vars.
 */
function bizpanda_visibility_param_user_registered( $value ) {
    
    if ( !is_user_logged_in() ) return 0;
    else {
        $user = wp_get_current_user();
        $timestamp = strtotime( $user->data->user_registered ) * 1000;
        return $timestamp;
    }
}
add_filter('bp_visibility_param_user-registered', 'bizpanda_visibility_param_user_registered');

/**
 * Writes a number of user pageviews.
 */
function bizpanda_visibility_param_user_pageviews( $value ) {
    
    if ( !is_user_logged_in() ) return 0;
    else {
        $user = wp_get_current_user();
        $timestamp = strtotime( $user->data->user_registered ) * 1000;
        return $timestamp;
    }
}
add_filter('bp_visibility_param_user-pageviews', 'bizpanda_visibility_param_user_pageviews');

/**
 * Writes a number of user pageviews.
 */
function bizpanda_visibility_param_post_published( $value ) {
    global $post;
    if ( empty( $post ) ) return $value;
    
    if ( empty( $post->post_date_gmt ) ) return time() * 1000;
    return strtotime( $post->post_date_gmt ) * 1000;
}
add_filter('bp_visibility_param_post-published', 'bizpanda_visibility_param_post_published');

/**
 * Sets visibility cookies on logging in.
 */
function bizpanda_wp_login( $user_login, $user  ) {
    if ( empty( $user ) ) return 'guest';
    if ( !isset( $user->roles[0] )) return;

    $userRole = $user->roles[0];
    $userRegistered = strtotime( $user->data->user_registered ) * 1000;

    setcookie( 'bp_user-role', $userRole, time() + 3600 * 24 * 5000, COOKIEPATH, COOKIE_DOMAIN );
    setcookie( 'bp_user-registered', $userRegistered, time() + 3600 * 24 * 5000, COOKIEPATH, COOKIE_DOMAIN );
}

/**
 * Sets visibility cookies on logging out.
 */
function bizpanda_wp_logout() {
    setcookie( 'bp_user-role', 'guest', time() + 3600 * 24 * 5000, COOKIEPATH, COOKIE_DOMAIN );
    setcookie( 'bp_user-registered', 0, time() + 3600 * 24 * 5000, COOKIEPATH, COOKIE_DOMAIN );
}

add_action('wp_login', 'bizpanda_wp_login', 10, 2);
add_action('wp_logout', 'bizpanda_wp_logout');


if( !is_admin() ) {

    add_action('init', 'bizpanda_set_cookies');

    function bizpanda_set_cookies()
    {
        // do not set the cookies if the headers have been sent for some reasons
        // otherwise a user can see warnings
        if ( headers_sent() ) return;

        if ( !isset( $_COOKIE['bp_user-role'] )) {
            $value = bizpanda_visibility_param_user_role( 'guest' );
            setcookie( 'bp_user-role', $value, time() + 3600 * 24 * 5000, COOKIEPATH, COOKIE_DOMAIN );
        }
        
        if ( !isset( $_COOKIE['bp_user-registered'] )) {
            $value = bizpanda_visibility_param_user_registered( 0 );
            setcookie( 'bp_user-registered', $value, time() + 3600 * 24 * 5000, COOKIEPATH, COOKIE_DOMAIN );
        }
    }
}

