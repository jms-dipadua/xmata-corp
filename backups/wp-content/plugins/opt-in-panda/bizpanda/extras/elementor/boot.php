<?php

class OPanda_Elementor_Container_Extention {

    public static function init() {
        $me = 'OPanda_Elementor_Container_Extention';

        add_action( 'elementor/element/column/section_advanced/before_section_start', "$me::section_start");
        add_action( 'elementor/element/section/section_advanced/before_section_start', "$me::section_start" );

        add_action( 'elementor/frontend/column/before_render', "$me::frontend_before_render" );
        add_action( 'elementor/frontend/section/before_render', "$me::frontend_before_render" );

        add_action( 'elementor/frontend/column/after_render', "$me::frontend_after_render" );
        add_action( 'elementor/frontend/section/after_render', "$me::frontend_after_render" );
    }

    /**
     * Returns available lockers to select.
     */
    protected static function get_lockers_options() {

        $result = array();
        $result['none'] = __('- not select -', 'bizpanda');

        $allowed_shortcodes = [];

        if ( BizPanda::hasPlugin('sociallocker') ) {
            $allowed_shortcodes['sociallocker'] = true;
            $allowed_shortcodes['signinlocker'] = true;
        }

        if ( BizPanda::hasPlugin('optinpanda') ) {
            $allowed_shortcodes['emaillocker'] = true;
            $allowed_shortcodes['signinlocker'] = true;
        }

        $allowed_shortcodes = array_keys( $allowed_shortcodes );

        $lockers = get_posts(array(
            'post_type' => OPANDA_POST_TYPE,
            'meta_key' => 'opanda_item',
            'meta_value' => OPanda_Items::getAvailableNames(),
            'numberposts' => -1
        ));

        foreach($lockers as $locker)
        {
            $itemType = get_post_meta( $locker->ID, 'opanda_item', true );

            $item = OPanda_Items::getItem($itemType);
            $shortcode = $item['shortcode'];

            if ( !in_array( $shortcode, $allowed_shortcodes)) continue;

            $lockerId = $shortcode . '-' . $locker->ID;
            $result[$lockerId] = empty( $locker->post_title ) ? '(no titled, ID=' . $locker->ID . ')' : $locker->post_title;
        }

        return $result;
    }

    /**
     * Adds the locker picker for column and section elements.
     */
    public static function section_start( \Elementor\Element_Base $element )
    {

        if (BizPanda::hasPlugin('sociallocker')) {

            $element->start_controls_section(
                'section_locker',
                [
                    'label' => __('Social Locker', 'mld'),
                    'tab' => \Elementor\Controls_Manager::TAB_ADVANCED,
                ]
            );

        } elseif ( BizPanda::hasPlugin('optinpanda') ) {

            $element->start_controls_section(
                'section_spacing',
                [
                    'label' => __( 'Opt-In Panda', 'mld' ),
                    'tab' => \Elementor\Controls_Manager::TAB_ADVANCED,
                ]
            );
        }

        $element->add_control(
            'opanda_locker_id',
            [
                'label' => __( 'Locker', 'bizpanda' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => self::get_lockers_options(),
                'default' => 'none',
                'description' => 'A locker to be applied to content of the container.'
            ]
        );

        $element->end_controls_section();
    }

    /**
     * Elements that where buffered.
     * @var array
     */
    protected static $buffered_elements = [];

    /**
     * Starts buffering before rendering the element to attach the locker shortcode.
     * @param \Elementor\Element_Base $element
     */
    public static function frontend_before_render( \Elementor\Element_Base $element ) {

        $settings = $element->get_settings_for_display();

        if ( isset( $settings['opanda_locker_id'] ) && !empty( $settings['opanda_locker_id'] ) ) {
            if ( 'none' === $settings['opanda_locker_id'] ) return;

            $parts = explode('-', $settings['opanda_locker_id']);
            if ( count( $parts ) !== 2 ) return;

            $elementId = $element->get_id();
            $shortcode = $parts[0];
            $lockerId =  $parts[1];

            self::$buffered_elements[$elementId] = [
                'elementId' => $elementId,
                'shortcode' => $shortcode,
                'lockerId' => $lockerId
            ];

            ob_start();
        }
    }

    /**
     * Ends buffering and prints content with the locker.
     * @param \Elementor\Element_Base $element
     */
    public static function frontend_after_render( \Elementor\Element_Base $element ) {
        $elementId = $element->get_id();

        if  ( !isset( self::$buffered_elements[$elementId] ) ) return;

        $shortcode = self::$buffered_elements[$elementId]['shortcode'];
        $lockerId = self::$buffered_elements[$elementId]['lockerId'];

        $to_lock = ob_get_contents(); ob_end_clean();
        echo do_shortcode('[' . $shortcode . ' id="' . $lockerId . '"]'.$to_lock.'[/' . $shortcode . ']');
    }
}

OPanda_Elementor_Container_Extention::init();