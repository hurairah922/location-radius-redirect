<?php
namespace LRR;

if (!defined('ABSPATH')) exit;

final class Plugin {
  private static $instance;

  private function __construct() {}

  public function init(): void {
    require_once LRR_PATH . 'includes/class-lrr-admin.php';
    require_once LRR_PATH . 'includes/class-lrr-geo.php';

    Admin::instance()->init();

    add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
    add_shortcode('order_now_button', [$this, 'shortcode_button']);
    add_filter('wp_nav_menu_objects', [$this, 'do_shortcodes_in_menu_items'], 10, 2);


    // For safety: only render modal once in footer.
    add_action('wp_footer', [$this, 'render_lightbox_markup']);
  }

  public static function instance(): self {
    if (!self::$instance) self::$instance = new self();
    return self::$instance;
  }

  public function enqueue_assets(): void {
    if (is_admin()) return;

    wp_enqueue_style(
      'lrr-frontend',
      LRR_URL . 'assets/css/lrr-frontend.css',
      [],
      LRR_VERSION
    );

    wp_enqueue_script(
      'lrr-frontend',
      LRR_URL . 'assets/js/lrr-frontend.js',
      [],
      LRR_VERSION,
      true
    );

    $s = self::get_settings();

    wp_localize_script('lrr-frontend', 'LRR_CFG', [
      'storeLat' => is_numeric($s['lat']) ? (float)$s['lat'] : null,
      'storeLng' => is_numeric($s['lng']) ? (float)$s['lng'] : null,
      'radiusKm' => is_numeric($s['radius_km']) ? (float)$s['radius_km'] : 0,
      'redirectUrl' => esc_url_raw($s['redirect_url']),
      'bindSelector' => (string)$s['bind_selector'],
      'msgDenied' => (string)$s['msg_denied'],
      'msgOutside' => (string)$s['msg_outside'],
      'msgNoSupport' => (string)$s['msg_nosupport'],
    ]);
  }

  public static function get_settings(): array {
    $settings = get_option(self::option_key(), []);
    if (!is_array($settings)) $settings = [];
    return array_merge(self::defaults(), $settings);
  }

  public static function option_key(): string {
    return 'lrr_settings';
  }

  public static function defaults(): array {
    return [
      'lat' => '',
      'lng' => '',
      'radius_km' => 10,
      'redirect_url' => 'https://app.ipos247.com/contactless/#/0c078165d578c0ed60fca00d6c4de792b9f72f399b64bd11e6b8ef089c7d6a18',
      'bind_selector' => '', // e.g. ".menu a.order-now" or "#order-now"
      'msg_denied' => 'Sorry — we need your location permission to continue.',
      'msg_outside' => 'Sorry — ordering is only available within our delivery range.',
      'msg_nosupport' => 'Sorry — your browser does not support location services.',
      'btn_text' => 'Order Now',
    ];
  }

  public function shortcode_button($atts): string {
    $s = self::get_settings();

    $atts = shortcode_atts([
      'text' => $s['btn_text'] ?: 'Order Now',
      'class' => '',
    ], $atts);

    $text = esc_html($atts['text']);
    $class = trim('lrr-order-link ' . sanitize_html_class($atts['class']));


    // data attribute used by JS to bind click
    return sprintf(
      '<a href="#" class="%s" data-lrr-order="1" role="menuitem">%s</a>',
      esc_attr($class),
      $text
    );
  }

  public function render_lightbox_markup(): void {
    // Render once. This is a simple accessible modal.
    ?>
    <div id="lrr-modal" class="lrr-modal" aria-hidden="true">
      <div class="lrr-modal__backdrop" data-lrr-close="1"></div>
      <div class="lrr-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="lrr-modal-title">
        <h3 id="lrr-modal-title" class="lrr-modal__title">Sorry</h3>
        <p id="lrr-modal-message" class="lrr-modal__message"></p>
        <button type="button" class="lrr-modal__btn" data-lrr-close="1">Close</button>
      </div>
    </div>
    <?php
  }

  public function do_shortcodes_in_menu_items($items, $args) {
    if (!is_array($items)) return $items;

    foreach ($items as $item) {
      if (!isset($item->title)) continue;

      // Run shortcodes safely in menu title
      if (
        strpos($item->title, '[') !== false &&
        strpos($item->title, ']') !== false
      ) {
        $item->title = do_shortcode($item->title);
      }
    }

    return $items;
  }

}
