<?php
namespace LRR;

if (!defined('ABSPATH')) exit;

final class Admin {
  private static $instance;

  private function __construct() {}

  public static function instance(): self {
    if (!self::$instance) self::$instance = new self();
    return self::$instance;
  }

  public function init(): void {
    add_action('admin_menu', [$this, 'menu']);
    add_action('admin_init', [$this, 'register_settings']);
  }

  public function menu(): void {
    add_options_page(
      'Location Radius Redirect',
      'Location Radius Redirect',
      'manage_options',
      'location-radius-redirect',
      [$this, 'render_page']
    );
  }

  public function register_settings(): void {
    register_setting(
      'lrr_settings_group',
      Plugin::option_key(),
      [
        'type' => 'array',
        'sanitize_callback' => [$this, 'sanitize_settings'],
        'default' => Plugin::defaults(),
      ]
    );

    add_settings_section('lrr_main', 'Configuration', '__return_false', 'lrr_settings');

    add_settings_field('lat', 'Restaurant Latitude', [$this, 'field_lat'], 'lrr_settings', 'lrr_main');
    add_settings_field('lng', 'Restaurant Longitude', [$this, 'field_lng'], 'lrr_settings', 'lrr_main');
    add_settings_field('radius_km', 'Allowed Radius (KM)', [$this, 'field_radius'], 'lrr_settings', 'lrr_main');
    add_settings_field('redirect_url', 'Redirect URL', [$this, 'field_url'], 'lrr_settings', 'lrr_main');
    add_settings_field('bind_selector', 'Bind to existing "Order Now" button (CSS selector)', [$this, 'field_selector'], 'lrr_settings', 'lrr_main');

    add_settings_section('lrr_msgs', 'Lightbox Messages', '__return_false', 'lrr_settings');

    add_settings_field('msg_denied', 'Message when location denied', [$this, 'field_msg_denied'], 'lrr_settings', 'lrr_msgs');
    add_settings_field('msg_outside', 'Message when outside radius', [$this, 'field_msg_outside'], 'lrr_settings', 'lrr_msgs');
    add_settings_field('msg_nosupport', 'Message when unsupported', [$this, 'field_msg_nosupport'], 'lrr_settings', 'lrr_msgs');
    add_settings_field('btn_text', 'Shortcode Button Text', [$this, 'field_btn_text'], 'lrr_settings', 'lrr_msgs');
  }

  public function sanitize_settings($input): array {
    $d = Plugin::defaults();
    $out = [];

    $out['lat'] = (isset($input['lat']) && is_numeric($input['lat'])) ? (string)((float)$input['lat']) : '';
    $out['lng'] = (isset($input['lng']) && is_numeric($input['lng'])) ? (string)((float)$input['lng']) : '';
    $out['radius_km'] = (isset($input['radius_km']) && is_numeric($input['radius_km'])) ? max(0.1, (float)$input['radius_km']) : (float)$d['radius_km'];

    $out['redirect_url'] = isset($input['redirect_url']) ? esc_url_raw($input['redirect_url']) : $d['redirect_url'];

    // selector is stored as plain text, used by JS querySelectorAll. Keep it trimmed.
    $out['bind_selector'] = isset($input['bind_selector']) ? sanitize_text_field($input['bind_selector']) : '';

    $out['msg_denied'] = isset($input['msg_denied']) ? sanitize_textarea_field($input['msg_denied']) : $d['msg_denied'];
    $out['msg_outside'] = isset($input['msg_outside']) ? sanitize_textarea_field($input['msg_outside']) : $d['msg_outside'];
    $out['msg_nosupport'] = isset($input['msg_nosupport']) ? sanitize_textarea_field($input['msg_nosupport']) : $d['msg_nosupport'];
    $out['btn_text'] = isset($input['btn_text']) ? sanitize_text_field($input['btn_text']) : $d['btn_text'];

    return $out;
  }

  public function render_page(): void {
    if (!current_user_can('manage_options')) wp_die('Insufficient permissions.');
    $s = Plugin::get_settings();
    ?>
    <div class="wrap">
      <h1>Location Radius Redirect</h1>
      <p>
        Use shortcode: <code>[order_now_button]</code><br>
        Or bind your existing menu link by setting a CSS selector in settings (e.g. <code>#order-now</code> or <code>.order-now a</code>).
      </p>

      <form method="post" action="options.php">
        <?php
        settings_fields('lrr_settings_group');
        do_settings_sections('lrr_settings');
        submit_button();
        ?>
      </form>

      <hr>
      <h2>Current Settings</h2>
      <ul>
        <li><strong>Restaurant:</strong> <?php echo esc_html($s['lat']); ?>, <?php echo esc_html($s['lng']); ?></li>
        <li><strong>Radius:</strong> <?php echo esc_html($s['radius_km']); ?> km</li>
        <li><strong>Redirect URL:</strong> <code><?php echo esc_html($s['redirect_url']); ?></code></li>
        <li><strong>Bind Selector:</strong> <code><?php echo esc_html($s['bind_selector']); ?></code></li>
      </ul>
    </div>
    <?php
  }

  public function field_lat(): void {
    printf('<input type="text" class="regular-text" name="%s" value="%s" placeholder="e.g. 31.5204" />',
      esc_attr($this->opt_name('lat')),
      esc_attr($this->val('lat'))
    );
  }

  private function opt_name(string $k): string {
    return Plugin::option_key() . '[' . $k . ']';
  }

  private function val(string $k) {
    $s = Plugin::get_settings();
    return $s[$k] ?? '';
  }

  public function field_lng(): void {
    printf('<input type="text" class="regular-text" name="%s" value="%s" placeholder="e.g. 74.3587" />',
      esc_attr($this->opt_name('lng')),
      esc_attr($this->val('lng'))
    );
  }

  public function field_radius(): void {
    printf('<input type="number" step="0.1" min="0.1" class="small-text" name="%s" value="%s" /> km',
      esc_attr($this->opt_name('radius_km')),
      esc_attr($this->val('radius_km'))
    );
  }

  public function field_url(): void {
    printf('<input type="url" class="regular-text" name="%s" value="%s" />',
      esc_attr($this->opt_name('redirect_url')),
      esc_attr($this->val('redirect_url'))
    );
    echo '<p class="description">User is redirected to this URL only if within radius.</p>';
  }

  public function field_selector(): void {
    printf('<input type="text" class="regular-text" name="%s" value="%s" placeholder="#order-now, .order-now a, nav a[href*=order]" />',
      esc_attr($this->opt_name('bind_selector')),
      esc_attr($this->val('bind_selector'))
    );
    echo '<p class="description">If set, plugin will attach click handler to matching elements site-wide.</p>';
  }

  public function field_msg_denied(): void {
    printf('<textarea class="large-text" rows="2" name="%s">%s</textarea>',
      esc_attr($this->opt_name('msg_denied')),
      esc_textarea($this->val('msg_denied'))
    );
  }

  public function field_msg_outside(): void {
    printf('<textarea class="large-text" rows="2" name="%s">%s</textarea>',
      esc_attr($this->opt_name('msg_outside')),
      esc_textarea($this->val('msg_outside'))
    );
  }

  public function field_msg_nosupport(): void {
    printf('<textarea class="large-text" rows="2" name="%s">%s</textarea>',
      esc_attr($this->opt_name('msg_nosupport')),
      esc_textarea($this->val('msg_nosupport'))
    );
  }

  public function field_btn_text(): void {
    printf('<input type="text" class="regular-text" name="%s" value="%s" />',
      esc_attr($this->opt_name('btn_text')),
      esc_attr($this->val('btn_text'))
    );
  }
}
