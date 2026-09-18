<?php
defined('ABSPATH') OR exit;

class Trustedsite {
    public static function activate() {
        update_option('trustedsite_active', 1);
        
        if(Trustedsite::get_sitemap_active() == true) {
            update_option('trustedsite_robots_enable', 1);
        } else {
            update_option('trustedsite_robots_enable', 0);
        }
        
        Trustedsite::ping_event('activate');
    }

    public static function scripts($hook) {
        if (strpos($hook, "trustedsite-settings") !== false) {
            wp_enqueue_style('trustedsite-settings-fa', plugins_url('../css/font-awesome.min.css',__FILE__));
            wp_enqueue_style('trustedsite-settings-css', plugins_url('../css/settings.css',__FILE__));
            wp_enqueue_script('trustedsite-trustedsite-js', plugins_url('../js/trustedsite.js',__FILE__));
        }
    }
    
    public static function get_api_response() {
        $endpoint_host = "https://cdn.trustedsite.com";
        $arrHost = parse_url(home_url('', $scheme = 'http'));
        $host = $arrHost['host'];
     
        $api_url = $endpoint_host . "/api/v2/site-lookup.json?host=" . urlencode($host) . "&cache=" . get_the_date('i');
        $response = wp_remote_get($api_url);
        
        return $response;
    }

    public static function get_site_id() {
        $existing_site_id = get_option('trustedsite_site_id');
        if (!empty($existing_site_id)) {
            return $existing_site_id;
        }

        $response = Trustedsite::get_api_response();
        
        if (is_array($response) && !is_wp_error($response)) {
            $rjson = json_decode($response['body'], true);
            $site_id = sanitize_text_field( $rjson['site_id'] );
            update_option('trustedsite_site_id', $site_id);
            return $site_id;
        }
        
        return false;
    }
    
    public static function get_sitemap_active() {
        $response = Trustedsite::get_api_response();

        if ( is_array( $response ) && ! is_wp_error( $response ) ) {
            $rjson = json_decode( $response['body'], true );
            
            if ( ! is_array( $rjson ) || isset( $rjson['success'] ) && $rjson['success'] == 0 ) {
                return false;
            }
            
            if ( isset( $rjson['sitemap'] ) && is_array( $rjson['sitemap'] ) ) {
                $sitemap = $rjson['sitemap'];
                $status  = isset( $sitemap['status'] ) ? $sitemap['status'] : '';
                
                if ( 'active' === $status ) {
                    return true;
                }
            }
        }
     
        return false;
        
        if (is_array($response) && !is_wp_error($response)) {
            $rjson = json_decode($response['body'], true);
            if ($rjson['success'] == 0) return false;
            $sitemap = $rjson['sitemap'];
            $status = $sitemap['status'];
            if ($status == 'active') return true;
            return false;
        }
     
        return false;
    }

    public static function ping_event($name) {
        $id = get_option('trustedsite_id');
        if ( ! $id ) {
           $id = wp_generate_uuid4();
           update_option('trustedsite_id', $id);
         }
         $response = wp_remote_post('https://www.trustedsite.com/rpc/wordpress', [
             'timeout' => 5,
             'body'    => [
                 'do'       => 'event',
                 'id'       => $id,
                 'name'     => $name,
                 'site_url' => esc_url_raw( site_url() ),
                 'home_url' => esc_url_raw( home_url() ),
                 'email'    => sanitize_email( get_option('admin_email') ),
                 'version'  => TRUSTEDSITE_VERSION
             ],
         ]);
    }
    
    public static function ping_siteurl_change() {
        Trustedsite::ping_event('siteurl_change');
    }
    
    public static function ping_admin_email_change() {
        Trustedsite::ping_event('admin_email_change');
    }

    public static function install() {
        add_shortcode('mcafeesecure', 'Trustedsite::mfes_engagement_trustmark_shortcode');
        add_shortcode('trustedsite', 'Trustedsite::ts_engagement_trustmark_shortcode');
        add_shortcode('trustedsite_form', 'Trustedsite::ts_form_engagement_trustmark_shortcode');
        add_shortcode('trustedsite_checkout', 'Trustedsite::ts_checkout_engagement_trustmark_shortcode');
        add_shortcode('trustedsite_login', 'Trustedsite::ts_login_engagement_trustmark_shortcode');
        add_shortcode('mcafeesecure_sip', 'Trustedsite::mfes_sip_trustmark_shortcode');
        add_shortcode('trustedsite_sip', 'Trustedsite::ts_sip_shortcode');
        add_shortcode('trustedsite_sip_legacy', 'Trustedsite::ts_sip_legacy_shortcode');
        add_shortcode('trustedsite_banner', 'Trustedsite::ts_banner_shortcode');
        add_shortcode('trustedsite_testimonial', 'Trustedsite::ts_testimonial_shortcode');
        add_shortcode('mcafeesecure_hide', 'Trustedsite::hide_floating_trustmark_shortcode');
        add_shortcode('trustedsite_hide', 'Trustedsite::hide_floating_trustmark_shortcode');

        add_action('admin_menu', 'Trustedsite::admin_menus');
        add_action('admin_enqueue_scripts', 'Trustedsite::scripts');
        add_action('update_option_siteurl', 'Trustedsite::ping_siteurl_change');
        add_action('update_option_admin_email', 'Trustedsite::ping_admin_email_change');
        
        add_filter('plugin_action_links_trustedsite/trustedsite.php', 'Trustedsite::add_plugin_settings_link');

        if ( ! get_option( 'mcafeesecure_active' ) ) {
            add_action('do_robots', 'Trustedsite::robots');
            add_action('wp_footer', 'Trustedsite::inject_code');
            add_action( 'woocommerce_thankyou', 'Trustedsite::inject_sip_modal' );
        }
        
        if (!get_option('trustedsite_install_ping_done')) {
            Trustedsite::ping_event('install');
            update_option('trustedsite_install_ping_done', 1);
        }
    }

    public static function robots() {
        if ( intval( get_option( 'trustedsite_robots_enable' ) ) === 1 ) {
            $site_id = Trustedsite::get_site_id();

            if ( ! empty( $site_id ) ) {
                echo "\nSitemap: https://cdn.ywxi.net/sitemap/"
                    . sanitize_key( $site_id )
                    . "/1.xml\n";
            }
        }
    }
    
    public static function inject_sip_modal($order_id) {
        $order = wc_get_order($order_id);
        $email = sanitize_email($order->get_billing_email());
        $first_name = sanitize_text_field($order->get_billing_first_name());
        $last_name = sanitize_text_field($order->get_billing_last_name());
        $country_code = sanitize_text_field($order->get_billing_country());
        $state_code = sanitize_text_field($order->get_billing_state());

        ?>
        <script type="text/javascript">
            (function() {
                var sipScript = document.createElement('script');
                sipScript.setAttribute("class", "trustedsite-track-conversion");
                sipScript.setAttribute("type", "text/javascript");
                sipScript.setAttribute("data-type", "purchase");
                sipScript.setAttribute("data-orderid", <?php echo wp_json_encode( (string) $order_id ); ?>);
                sipScript.setAttribute("data-email", <?php echo wp_json_encode( $email ); ?>);
                sipScript.setAttribute("data-firstname", <?php echo wp_json_encode( $first_name ); ?>);
                sipScript.setAttribute("data-lastname", <?php echo wp_json_encode( $last_name ); ?>);
                sipScript.setAttribute("data-country", <?php echo wp_json_encode( $country_code ); ?>);
                sipScript.setAttribute("data-state", <?php echo wp_json_encode( $state_code ); ?>);
                sipScript.setAttribute("src", "https://cdn.ywxi.net/js/conversion.js");
                document.getElementsByTagName("head")[0].appendChild(sipScript);
            })();
        </script>
        <?php
    }

    public static function deactivate() {
        Trustedsite::ping_event( 'deactivate' );
        
        delete_option( 'trustedsite_active' );
    }

    public static function uninstall() {
        Trustedsite::ping_event('uninstall');
        
        delete_option("trustedsite_active");
        delete_option("trustedsite_data");
        delete_option("trustedsite_site_id");
        delete_option("trustedsite_robots_enable");
        delete_option("trustedsite_install_ping_done");
    }

    public static function mfes_engagement_trustmark_shortcode($atts = array()) {
        $a = shortcode_atts(array(
            'width' => 90,
        ), $atts);

        $width = intval($a['width']);
        return "<div class='mfes-trustmark' data-type='102' data-width=" . $width . " data-ext='svg'></div>";
    }

    public static function ts_engagement_trustmark_shortcode($atts = array()) {
        $a = shortcode_atts(array(
            'width' => 90,
        ), $atts);

        $width = intval($a['width']);
        return "<div class='trustedsite-trustmark' data-type='202' data-width=" . $width . " data-ext='svg'></div>";
    }

    public static function ts_form_engagement_trustmark_shortcode($atts = array()) {
        $a = shortcode_atts(array(
            'width' => 90,
        ), $atts);

        $width = intval($a['width']);
        return "<div class='trustedsite-trustmark' data-type='211' data-width=" . $width . " data-ext='svg'></div>";
    }

    public static function ts_checkout_engagement_trustmark_shortcode($atts = array()) {
        $a = shortcode_atts(array(
            'width' => 90,
        ), $atts);

        $width = intval($a['width']);
        return "<div class='trustedsite-trustmark' data-type='212' data-width=" . $width . " data-ext='svg'></div>";
    }

    public static function ts_login_engagement_trustmark_shortcode($atts = array()) {
        $a = shortcode_atts(array(
            'width' => 90,
        ), $atts);

        $width = intval($a['width']);
        return "<div class='trustedsite-trustmark' data-type='213' data-width=" . $width . " data-ext='svg'></div>";
    }

    public static function mfes_sip_trustmark_shortcode($atts = array()) {
        $a = shortcode_atts(array(
            'width' => 90,
        ), $atts);

        $width = intval($a['width']);
        return "<div class='mfes-trustmark' data-type='103' data-width=" . $width . " data-ext='svg'></div>";
    }

    public static function ts_sip_legacy_shortcode($atts = array()) {
        $a = shortcode_atts(array(
            'width' => 90,
        ), $atts);

        $width = intval($a['width']);
        return"<div class='trustedsite-trustmark' data-type='203' data-width=" . $width . " data-ext='svg'></div>";
    }

    public static function ts_sip_shortcode($atts = array()) {
        $a = shortcode_atts(array(
            'width' => 90,
        ), $atts);

        $width = intval($a['width']);
        return"<div class='trustedsite-trustmark' data-type='204' data-width=" . $width . " data-ext='svg'></div>";
    }
    
    public static function ts_banner_shortcode($atts = array()) {
        $a = shortcode_atts(array(
            'width' => '0',
        ), $atts);

        $width = intval($a['width']);
        return"<div class='trustedsite-trustmark' data-type='1001' data-width=" . $width . "></div>";
    }
    
    public static function ts_testimonial_shortcode($atts = array()) {
        $a = shortcode_atts(array(
            'width' => '0',
            'height' => '0',
        ), $atts);


        $width = intval($a['width']);
        $height = intval($a['height']);
        return"<div class='trustedsite-trustmark' data-type='1002' data-width=" . $width . " data-height=" . $height . "></div>";
    }

    public static function hide_floating_trustmark_shortcode($atts = array()) {
        return "<div class='trustedsite-tm-float-disable'></div>";
    }

    public static function admin_menus() {

        add_options_page(
            'TrustedSite',
            'TrustedSite',
            'activate_plugins',
            'trustedsite-settings',
            'Trustedsite::settings_page');

    }

    public static function add_plugin_settings_link($links) {
        array_unshift( $links, '<a href="' . esc_url( admin_url( 'options-general.php?page=trustedsite-settings' ) ) . '">Settings</a>' );
        return $links;
    }

    public static function settings_page() {
        if ( ! current_user_can( 'activate_plugins' ) ) {
            wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'trustedsite' ) );
        }

        $settings_path = plugin_dir_path( __FILE__ ) . 'settings_page.php';
        
        if ( file_exists( $settings_path ) ) {
            require_once $settings_path;
        } else {
            wp_die( esc_html__( 'Configuration template view file is missing.', 'trustedsite' ) );
        }
    }

    public static function inject_code() {
        ?>
        <script type="text/javascript">
          (function() {
            var sa = document.createElement('script'); sa.type = 'text/javascript'; sa.async = true;
            sa.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'cdn.ywxi.net/js/1.js';
            var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(sa, s);
          })();
        </script>
        <?php
    }
}

?>
