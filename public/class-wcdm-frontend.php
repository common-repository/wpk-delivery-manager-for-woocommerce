<?php
/**
 * Manage Delivery option frontend settings
 */


if( ! class_exists( 'WCDM_Frontend' ) ) :

    class WCDM_Frontend {

        function __construct() {
            $this->event_handler();
        }

        public function event_handler() {
            add_action( 'wp_enqueue_scripts', [ $this, 'wcdm_enquqeue_scripts' ], 999 );

            add_action( 'woocommerce_before_order_notes', [ $this, 'add_checkout_field' ], 50 );
            add_action( 'woocommerce_cart_calculate_fees', [ $this, 'add_delivery_date_fee' ] );     
            add_action( 'woocommerce_checkout_update_order_review', [ $this, 'set_delivery_date_session' ] );

            add_action( 'woocommerce_checkout_process', [ $this, 'validate_delivery_date_field' ] );
            add_action( 'woocommerce_checkout_update_order_meta', [ $this, 'save_delivery_date_checkout_field' ] );
            add_action( 'woocommerce_thankyou', [ $this, 'show_delivery_date_field_thankyou' ] );    
        }

        public function wcdm_enquqeue_scripts() {
            if( is_checkout() ) :
                wp_enqueue_style( 'jquery.flatpickr.min', WCDM_URL . 'assets/css/flatpickr.css', array(), WCDM_VERSION );
                
                wp_enqueue_script( 'jquery.flatpickr.min', WCDM_URL . 'assets/js/flatpickr.js', array(), time(), true );
                wp_enqueue_script( 'wcdm-js', WCDM_URL . 'assets/js/wcdm-main.js', array(), WCDM_VERSION, true );
                $wcdm_weekdays      = get_option( 'wcdm_weekdays' );
                $wcdm_delivery_date = get_option( 'wcdm_min_delviery_days' );

                wp_localize_script( 'wcdm-js', 'wcdm_param',
                    array( 
                        'wcdm_weekdays' => implode(',', array_map(function($value) {
                            return intval(str_replace('weekdays_', '', $value));
                        }, (array)$wcdm_weekdays)),
                        'wcdm_max_days' => !empty( $wcdm_delivery_date) ? $wcdm_delivery_date : false
                    )
                );

            endif;
        }

        public function add_checkout_field( $checkout ) {
            $specific_delivery_date = get_option( 'wcdm_enable_disable' );
            $required_field         = get_option( 'wcdm_required_date_field' );

            if( $specific_delivery_date == 'yes' ) :
                woocommerce_form_field( 'wcdm_delivery_date', array(
                    'type'          => 'text',
                    'id'            => 'datepicker',
                    'required'      => $required_field == 'yes' ? true : false,  // Ensure 'required' is true only if $required_field is true
                    'class'         => array( 'wcdm-delivery-field', 'form-row', 'form-row-wide', 'update_totals_on_change' ),
                    'label'         => __( 'Delivery Date', 'order-delivery-date-and-time-for-woocommerce' ),
                    'placeholder'   => 'DD-MM-YYYY HH:MM',
                ), $checkout->get_value( 'wcdm_delivery_date' ) );                
            endif;
        }

        public function set_delivery_date_session( $posted_data  ) {
            parse_str( $posted_data, $output );
            $specific_date = isset( $output['wcdm_delivery_date'] ) && !empty( $output['wcdm_delivery_date'] ) ? true : false;
            WC()->session->set( 'specific_date', $specific_date );
        }

        public function add_delivery_date_fee( $cart ){
            if ( is_admin() && ! defined('DOING_AJAX') )
                return;
            
            if ( WC()->session->get('specific_date') == true ) :
                $fee_label   = __( "Delivery Charge", 'order-delivery-date-and-time-for-woocommerce' );
                $fee_amount  = get_option( 'wcdm_price' );
                $cart->add_fee( $fee_label, $fee_amount );
            endif;
        }

        public function validate_delivery_date_field() {    
            $required_field = get_option( 'wcdm_required_date_field' );
            if ( isset( $required_field ) && empty( $_POST['wcdm_delivery_date'] ) ) :
                wc_add_notice( __( 'Please choose delviery date', 'order-delivery-date-and-time-for-woocommerce' ), 'error' );
            endif;
        }

        public function save_delivery_date_checkout_field( $order_id ) { 
            if ( !empty( $_POST['wcdm_delivery_date'] ) ) :
                update_post_meta( $order_id, 'wcdm_delivery_date', sanitize_text_field( $_POST['wcdm_delivery_date'] ) );
            endif;
        }
        
        public function show_delivery_date_field_thankyou( $order_id ) { 
            $deliveryDate = get_post_meta( $order_id, 'wcdm_delivery_date', true );
            if ( !empty( $deliveryDate ) ) :
                echo sprintf( "<p><strong>Delivery date:</strong>%s</p>", esc_html( $deliveryDate ) );
            endif;
        }
    }
    new WCDM_Frontend();
endif;

?>