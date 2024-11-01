<?php
/**
 * Manage Delivery option admin settings
 */


if( ! class_exists( 'WCDM_Admin' ) ) :

    class WCDM_Admin {

        function __construct() {
            $this->event_handler();
        }

        public function event_handler() {
            add_filter( 'plugin_action_links_' . WCDM_PLUGIN_BASENAME, array( __CLASS__, 'plugin_action_links' ) );
            add_filter( 'woocommerce_settings_tabs_array', [ $this, 'add_settings_tab' ], 50 );

            add_action( 'woocommerce_settings_tabs_wcdm', [ $this, 'settings_tab' ] );
            add_action( 'woocommerce_update_options_wcdm', [ $this, 'update_settings' ] );

            add_action( 'woocommerce_admin_order_data_after_billing_address', [ $this, 'show_delivery_date_field_edi_order' ], 20 );
            add_action( 'woocommerce_email_after_order_table', [ $this, 'show_delivery_date_field_emails' ], 20, 4 );
        }

        public function add_settings_tab($tabs) {
            $tabs['wcdm'] = __( 'Order Delivery Date', 'order-delivery-date-and-time-for-woocommerce' );
            return $tabs;
        }

        public static function settings_tab() {
            woocommerce_admin_fields( self::get_settings() );
        }

        public static function get_settings() {
            global $wcdm_weekdays;
            $settings = array(
                'section_title' => array(
                    'name'      => __( 'Order Delivery Date ', 'order-delivery-date-and-time-for-woocommerce' ),
                    'type'      => 'title',
                    'id'        => 'wcdm_section_title'
                ),
                'enable_disable' => array(
                    'name'      => __( 'Enable/Disable', 'order-delivery-date-and-time-for-woocommerce' ),
                    'type'      => 'checkbox',
                    'desc'      => __( 'Enable delivery date', 'order-delivery-date-and-time-for-woocommerce' ),
                    'id'        => 'wcdm_enable_disable'
                ),
                'delivery_weekdays' => array(
                    'name'      => __( 'Disable Delivery Days:', 'order-delivery-date-and-time-for-woocommerce' ),
                    'type'      => 'multiselect',
                    'options'   => $wcdm_weekdays,
                    'desc'      => __( 'Select the weekdays on which you don\'t want to deliver.', 'order-delivery-date-and-time-for-woocommerce' ),
                    'id'        => 'wcdm_weekdays',
                    'class'     => 'wc-enhanced-select'
                ),
                'required_date_field' => array(
                    'name'      => __( 'Required Field', 'order-delivery-date-and-time-for-woocommerce' ),
                    'type'      => 'checkbox',
                    'desc'      => __( 'Check this box if you want to make selecting a delivery date on the checkout page required.', 'order-delivery-date-and-time-for-woocommerce' ),
                    'id'        => 'wcdm_required_date_field'
                ),
                'min_delivery_days' => array(
                    'name'      => __( 'Select number of days user can select:', 'order-delivery-date-and-time-for-woocommerce' ),
                    'type'      => 'number',
                    'default'   => 5,
                    'desc'      => __( 'Indicates the total number of dates available for delivery from the current date.', 'order-delivery-date-and-time-for-woocommerce' ),
                    'id'        => 'wcdm_min_delviery_days'
                ),
                'delivery_cost' => array(
                    'name'      => __( 'Delivery date cost', 'order-delivery-date-and-time-for-woocommerce' ),
                    'type'      => 'number',
                    'default'   => 10,
                    'desc'      => __( 'Enter the amount for addional fees if delivery date choosen.. set 0 for <strong>FREE</strong>', 'order-delivery-date-and-time-for-woocommerce' ),
                    'id'        => 'wcdm_price'
                ),
                'section_end' => array(
                    'type' => 'sectionend',
                    'id' => 'wcdm_section_end'
                )
            );
            return apply_filters( 'wcdm_settings', $settings );
        }

        public static function update_settings() {
            woocommerce_update_options( self::get_settings() );
        }

        public function show_delivery_date_field_edi_order( $order ) {    
            $order_id = $order->get_id();
            $deliveryDate = get_post_meta( $order_id, 'wcdm_delivery_date', true );
            if ( !empty( $deliveryDate ) ) :
                echo sprintf( "<p><strong>%s</strong>%s</p>", esc_html__( 'Delivery date:', '' ), esc_html( $deliveryDate ) );
            endif;
        }
        
        public function show_delivery_date_field_emails( $order, $sent_to_admin, $plain_text, $email ) {
            $order_id = $order->get_id();
            $deliveryDate = get_post_meta( $order_id, 'wcdm_delivery_date', true );
            if ( !empty( $deliveryDate ) ) :
                echo sprintf( "<p><strong>%s</strong>%s</p>", esc_html__( 'Delivery date:', 'order-delivery-date-and-time-for-woocommerce' ), esc_html( $deliveryDate ) );
            endif;
        }

        public static function plugin_action_links( $links ) {
            $action_links = array(
                'settings' => '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=wcdm' ) . '" aria-label="' . esc_attr__( 'View WooCommerce settings', 'order-delivery-date-and-time-for-woocommerce' ) . '">' . esc_html__( 'Settings', 'order-delivery-date-and-time-for-woocommerce' ) . '</a>',
            );
    
            return array_merge( $action_links, $links );
        }
    }
    new WCDM_Admin();
endif;