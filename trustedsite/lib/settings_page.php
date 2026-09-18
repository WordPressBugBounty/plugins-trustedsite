<?php
    defined('ABSPATH') OR exit;
    
    if ( ! current_user_can( 'activate_plugins' ) ) {
        wp_die( esc_html__( 'You do not have sufficient permissions to modify these settings.', 'trustedsite' ) );
    }
    
    if ( isset( $_POST['do'] ) ) {
        if ( ! isset( $_POST['trustedsite_settings_nonce'] ) || ! wp_verify_nonce( $_POST['trustedsite_settings_nonce'], 'trustedsite_save_action' ) ) {
            wp_die( esc_html__( 'Security check failed. Please refresh and try again.', 'trustedsite' ) );
        }

        if ( 'sitemap_enable' === $_POST['do'] ) {
            update_option( 'trustedsite_robots_enable', 1 );
        } elseif ( 'sitemap_disable' === $_POST['do'] ) {
            update_option( 'trustedsite_robots_enable', 0 );
        }
    }
    
    $email    = sanitize_email( get_option( 'admin_email' ) );
    $site_url = esc_url_raw( get_option( 'siteurl' ) );
    $arrHost  = parse_url( home_url( '', 'http' ) );
    $host     = isset( $arrHost['host'] ) ? sanitize_text_field( $arrHost['host'] ) : '';
    
    $endpoint = "https://www.trustedsite.com";
?>

<div class="wrap" id="trustedsite-container">

<div id="trustedsite-data" data-host="<?php echo esc_attr( $host ); ?>" data-email="<?php echo esc_attr( $email ); ?>"></div>

<div id="trustedsite-load" class="lds-ring">
<div class="lds-ring"></div>
</div>

<div id="trustedsite-error">
<h1><?php esc_html_e( 'TrustedSite', 'trustedsite' ); ?></h1>
<p><?php esc_html_e( 'Sorry, we have encountered an error loading your TrustedSite dashboard. If you have just activated your account, please allow up to a few minutes and try again. Otherwise, feel free to contact', 'trustedsite' ); ?>
<a href="<?php echo esc_url( 'https://support.trustedsite.com' ); ?>"><?php esc_html_e( 'TrustedSite Support', 'trustedsite' ); ?></a>.</p>
</div>

<div id="trustedsite-activation">
<h1><?php esc_html_e( 'TrustedSite', 'trustedsite' ); ?></h1>
<br/>
<div id="signup-header"><?php esc_html_e( 'Your Account', 'trustedsite' ); ?></div>
<div id="signup-text"><?php esc_html_e( 'To activate TrustedSite, please create your TrustedSite account.', 'trustedsite' ); ?></div>

<form method="POST" action="">
<!-- Generates implicit tracking hidden fields for CSRF defenses -->
<?php wp_nonce_field( 'trustedsite_save_action', 'trustedsite_settings_nonce' ); ?>

<span id="email">
<?php esc_html_e( 'Email', 'trustedsite' ); ?>
<input id="email-input" class="ts-input" type="text" name="email" value="<?php echo esc_attr( $email ); ?>">
</span><br>

<span id="domain">
<?php esc_html_e( 'Domain', 'trustedsite' ); ?>
<input id="domain-input" class="ts-input" type="text" name="domain" value="<?php echo esc_attr( $site_url ); ?>">
</span><br><br>

<input type="button" value="<?php esc_attr_e( 'Create Account', 'trustedsite' ); ?>" id="activate-now">
</form>
<br>
<div class="signup-text"><?php esc_html_e( 'Already have an account?', 'trustedsite' ); ?> <a href="#" id="login"><?php esc_html_e( 'Log in', 'trustedsite' ); ?></a> <?php esc_html_e( 'and add your site.', 'trustedsite' ); ?></div>
</div>
</div>

<div id="trustedsite-dashboard">
<h1>TrustedSite</h1>

<div class="row row-last row-txt highlight" id="trustedsite-exceed">
<div class="ts-arrow">
<i class="fa fa-angle-right"></i>
</div>
<div class="ts-long"><i class="fa fa-warning"></i>  You've exceeded your monthly visit limit. Upgrade now to continue displaying TrustedSite trustmarks.
</div>
</div>

<div class="wrapper">

<div class="left">
<div class="content" id="certifications">


<div class="row row-txt ts-title">
<span class="status-icon"></span>
Certifications
</div>

<div class="row row-txt highlight" id="certified-secure">
<div class="ts-arrow">
<i class="fa fa-angle-right"></i>
</div>
<div class="link">View Details</div>
<div class="ts-row">
<span class="status-icon"></span>
Certified Secure
</div>
</div>

<div class="row row-txt highlight" id="verified-business">
<div class="ts-arrow">
<i class="fa fa-angle-right"></i>
</div>
<div class="link">View Details</div>
<div class="ts-row">
<span class="status-icon"></span>
Verified Business
</div>
</div>

<div class="row row-txt highlight" id="issue-free-orders">
<div class="ts-arrow">
<i class="fa fa-angle-right"></i>
</div>
<div class="link">View Details</div>
<div class="ts-row">
<span class="status-icon"></span>
Issue Free Orders
</div>
</div>

<div class="row row-txt highlight" id="shopper-identity-protection">
<div class="ts-arrow">
<i class="fa fa-angle-right"></i>
</div>
<div class="link">View Details</div>
<div class="ts-row">
<span class="status-icon"></span>
Shopper Identity Protection
</div>
</div>

<div class="row row-txt highlight" id="data-protection">
<div class="ts-arrow">
<i class="fa fa-angle-right"></i>
</div>
<div class="link">View Details</div>
<div class="ts-row">
<span class="status-icon"></span>
Data Protection
</div>
</div>

<div class="row row-txt highlight" id="spam-free">
<div class="ts-arrow">
<i class="fa fa-angle-right"></i>
</div>
<div class="link">View Details</div>
<div class="ts-row">
<span class="status-icon"></span>
Spam-Free
</div>
</div>

<div class="row row-txt highlight" id="trusted-reviews">
<div class="ts-arrow">
<i class="fa fa-angle-right"></i>
</div>
<div class="link">View Details</div>
<div class="ts-row">
<span class="status-icon"></span>
Trusted Reviews
</div>
</div>

</div>

<div class="content" id="trustmarks">

<div class="row row-txt ts-title">
<span class="status-icon"></span>
Trustmarks
</div>

<div class="row row-img ts-img highlight" id="floating-tm">
<div>
<span class="status-icon"></span>
Floating
</div>
<div class="ts-img">
<img class="img-preview" src="<?php echo esc_url( plugins_url('../images/preview-64-floating.png',__FILE__) )?>" >
<div class="ts-arrow">
<i class="fa fa-angle-right"></i>
</div>
</div>
</div>

<div class="row row-img ts-img highlight" id="engagement-tm">
<div>
<span class="status-icon"></span>
Engagement
</div>
<div class="ts-img">
<img class="img-preview" src="<?php echo esc_url( plugins_url('../images/preview-64-engagement.png',__FILE__) )?>" >
<div class="ts-arrow">
<i class="fa fa-angle-right"></i>
</div>
</div>
</div>

<div class="row row-img ts-img highlight" id="shopper-identity-protection-tm">
<div>
<span class="status-icon"></span>
Shopper Identity Protection
</div>
<div class="ts-img">
<img class="img-preview" src="<?php echo esc_url( plugins_url('../images/preview-64-sip.png',__FILE__) )?>" >
<div class="ts-arrow">
<i class="fa fa-angle-right"></i>
</div>
</div>
</div>

<div class="row row-img ts-img highlight" id="testimonials-tm">
<div>
<span class="status-icon"></span>
Testimonials
</div>
<div class="ts-img">
<img class="img-preview" src="<?php echo esc_url( plugins_url('../images/preview-64-testimonials.png',__FILE__) )?>" >
<div class="ts-arrow">
<i class="fa fa-angle-right"></i>
</div>
</div>
</div>

<div class="row row-last row-img ts-img highlight" id="banner-tm">
<div>
<span class="status-icon"></span>
Banner
</div>
<div class="ts-img">
<img class="img-preview" src="<?php echo esc_url( plugins_url('../images/preview-64-banner.png',__FILE__) )?>" >
<div class="ts-arrow">
<i class="fa fa-angle-right"></i>
</div>
</div>
</div>

</div>
</div>

<div class="right">

<div class="content" id="setup">

<div class="row row-txt ts-title">
<span class="status-icon"></span>
Set Up
</div>

<div class="row row-txt highlight" id="setup-main-code">
<div class="ts-arrow">
<i class="fa fa-angle-right"></i>
</div>
<div class="link"></div>
<div class="ts-row">
<span class="status-icon"></span>
Main Code Installed
</div>
</div>

<div class="row row-txt highlight" id="setup-conversion-tracking">
<div class="ts-arrow">
<i class="fa fa-angle-right"></i>
</div>
<div class="link"></div>
<div class="ts-row">
<span class="status-icon"></span>
Set Up Conversion Tracking
</div>
</div>

<div class="row row-txt highlight" id="setup-directory-listing">
<div class="ts-arrow">
<i class="fa fa-angle-right"></i>
</div>
<div class="link"></div>
<div class="ts-row">
<span class="status-icon"></span>
Complete Directory Listing
</div>
</div>

<div class="row row-last row-txt no-arrow">
<form action="<?php echo esc_url( $endpoint . '/user/site/' . $host . '/' ); ?>" method="get" target="_blank">
<button class="ts-button" type="submit"><?php esc_html_e( 'Manage Account', 'trustedsite' ); ?></button>
</form>
</div>

</div>

<div class="content" id="trustedsite-upgrade">

<div class="row row-txt ts-title">
<span class="status-icon"></span>
Upgrade to Pro
</div>

<div class="row no-arrow">
<script src="https://fast.wistia.com/embed/medias/h04o4ou8tz.jsonp" async></script>
<script src="https://fast.wistia.com/assets/external/E-v1.js" async></script>
<div class="wistia_embed wistia_async_h04o4ou8tz" id="ts-video">&nbsp;</div>

</div>

<div class="row row-txt no-arrow" id="upgrade-link">
<div class="centered-text">
Get our full suite of trust-building tools and start boosting sales today.
</div>
<br>
<div>
<form action="<?php echo esc_url( $endpoint . '/user/site/' . $host . '/upgrade' ); ?>" method="get" target="_blank">
<button class="ts-button" type="submit">Upgrade Now</button>
</form>
</div>
</div>

<div class="row row-txt no-arrow" id="usage">
<div class="ts-title">
<span class="status-icon"></span>
Visit Usage
</div>
<progress id="usage-meter"></progress>
<div id="usage-text">
<span class="status-icon"></span>
</div>
</div>

</div>


<div class="content" id="addons">

<div class="row row-txt ts-title">
<span class="status-icon"></span>
Add-Ons
</div>

<div class="row row-txt highlight" id="addons-search-submission">
<div class="ts-arrow">
<i class="fa fa-angle-right"></i>
</div>
<div class="link">Learn More</div>
<div class="ts-row">
<span class="status-icon"></span>
Search Submission
</div>
</div>

<div class="row row-txt highlight" id="addons-diagnostics">
<div class="ts-arrow">
<i class="fa fa-angle-right"></i>
</div>
<div class="link">Learn More</div>
<div class="ts-row">
<span class="status-icon"></span>
Diagnostics
</div>
</div>

</div>

<div class="content" id="enable-sitemap">

<div class="row row-txt ts-title">
<span class="status-icon"></span>
<?php if ( intval( get_option( 'trustedsite_robots_enable' ) ) === 1 ): ?>
<?php esc_html_e( 'Sitemap', 'trustedsite' ); ?>
<?php else: ?>
<?php esc_html_e( 'Enable Sitemap', 'trustedsite' ); ?>
<?php endif; ?>
</div>

<div class="row row-last row-txt no-arrow">
<div>
<?php if ( intval( get_option( 'trustedsite_robots_enable' ) ) === 1 ): ?>
<?php esc_html_e( 'Your sitemap for Search Submission is enabled.', 'trustedsite' ); ?>
<?php else: ?>
<?php esc_html_e( 'Automatically enable your sitemap in robots.txt to help search engines find more of your content with Search Submission.', 'trustedsite' ); ?>
<?php endif; ?>
</div>
<br>

<form action="<?php echo esc_url( admin_url( 'options-general.php?page=trustedsite-settings' ) ); ?>" method="post">
<?php wp_nonce_field( 'trustedsite_save_action', 'trustedsite_settings_nonce' ); ?>
<?php if ( intval( get_option( 'trustedsite_robots_enable' ) ) === 1 ): ?>
<button class="ts-button" name="do" value="sitemap_disable" type="submit"><?php esc_html_e( 'Disable Sitemap', 'trustedsite' ); ?></button>
<?php else: ?>
<button class="ts-button" name="do" value="sitemap_enable" type="submit"><?php esc_html_e( 'Enable Sitemap', 'trustedsite' ); ?></button>
<?php endif; ?>
</form>
</div>

</div>
</div>
</div>
</div>
