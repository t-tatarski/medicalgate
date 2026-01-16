<?php
/**
 * Plugin Name: MedicalGate
 * Description: Popup z informacją dla potwierdzenia profesjonalisty (branża medyczna) z panelem admina.
 * Author: tatarski / Codent
 * Version: 1.2.0
 * Author URI: https://codent.pl/
 * Plugin URI: https://github.com/t-tatarski/medicalgate
 * License: MIT
 * Text Domain: medicalgate
 */

defined('ABSPATH') || exit;

define('MPG_VERSION', '1.2.0');
define('MPG_URL', plugin_dir_url(__FILE__));

// front
add_action('wp_enqueue_scripts', function () {
    if (isset($_COOKIE['mpg_professional'])) return;

    wp_enqueue_style(
        'mpg-style',
        MPG_URL . 'gate.css',
        [],
        MPG_VERSION
    );

    wp_enqueue_script(
        'mpg-script',
        MPG_URL . 'gate.js',
        [],
        MPG_VERSION,
        true
    );

    $options = get_option('mpg_options', []);
    wp_localize_script('mpg-script', 'mpgData', [
        'cookieDays' => intval($options['cookie_days'] ?? 365),
    ]);
});

//dynamic css

add_action('wp_head', function () {
    if (isset($_COOKIE['mpg_professional'])) return;

    $options    = get_option('mpg_options', []);
    $overlay_bg = $options['overlay_bg'] ?? '#00000099';
    $btn_bg     = $options['btn_bg'] ?? '#28a745';
    ?>
    <style id="mpg-dynamic-styles">
        #mpg-overlay { background: <?php echo esc_attr($overlay_bg); ?> !important; }
        #mpg-accept {
            background: <?php echo esc_attr($btn_bg); ?> !important;
            border: 2px solid <?php echo esc_attr($btn_bg); ?> !important;
        }
        #mpg-accept:hover {
            background: transparent !important;
            color: <?php echo esc_attr($btn_bg); ?> !important;
        }
    </style>
    <?php
}, 11);

// html 

add_action('wp_footer', function () {
    if (isset($_COOKIE['mpg_professional'])) return;

    $options = get_option('mpg_options', []);

    $title       = $options['title'] ?? 'Prośba o potwierdzenie';
    $content     = $options['content'] ?? 'Treści na stronie mają charakter medyczny i są przeznaczone wyłącznie dla profesjonalistów.';
    $confirm     = $options['confirm'] ?? 'Jeśli jesteś profesjonalistą z branży medycznej, potwierdź proszę.';
    $confirm_btn = $options['confirm_btn'] ?? 'Jestem profesjonalistą';
    $exit_text   = $options['exit_text'] ?? 'Opuść stronę';
    ?>
    <div id="mpg-overlay">
        <div class="mpg-modal">
            <h2><?php echo esc_html($title); ?></h2>
            <div class="mpg-divider"></div>
            <p><?php echo esc_html($content); ?></p>
            <p><?php echo esc_html($confirm); ?></p>

            <div class="mpg-actions">
                <button id="mpg-accept"><?php echo esc_html($confirm_btn); ?></button>
                <a href="https://www.google.com" class="mpg-exit"><?php echo esc_html($exit_text); ?></a>
            </div>
        </div>
    </div>
    <?php
});

// menu

add_action('admin_menu', function () {
    add_options_page(
        'Medical Gate',
        'Medical Gate',
        'manage_options',
        'medical-gate-settings',
        'mpg_settings_page'
    );
});

// settings

add_action('admin_init', function () {

    register_setting('mpg_settings', 'mpg_options', 'mpg_sanitize_options');

    add_settings_section(
        'mpg_main_section',
        'Ustawienia popupu',
        null,
        'medical-gate-settings'
    );

    add_settings_field('mpg_title', 'Tytuł popupu', 'mpg_title_callback', 'medical-gate-settings', 'mpg_main_section');
    add_settings_field('mpg_content', 'Treść główna', 'mpg_content_callback', 'medical-gate-settings', 'mpg_main_section');
    add_settings_field('mpg_confirm', 'Tekst potwierdzenia', 'mpg_confirm_callback', 'medical-gate-settings', 'mpg_main_section');
    add_settings_field('mpg_confirm_btn', 'Tekst przycisku', 'mpg_confirm_btn_callback', 'medical-gate-settings', 'mpg_main_section');
    add_settings_field('mpg_exit_text', 'Tekst "Opuść stronę"', 'mpg_exit_text_callback', 'medical-gate-settings', 'mpg_main_section');
    add_settings_field('mpg_overlay_bg', 'Kolor tła overlay', 'mpg_overlay_bg_callback', 'medical-gate-settings', 'mpg_main_section');
    add_settings_field('mpg_btn_bg', 'Kolor przycisku', 'mpg_btn_bg_callback', 'medical-gate-settings', 'mpg_main_section');
    add_settings_field('mpg_cookie_days', 'Czas trwania cookie (dni)', 'mpg_cookie_days_callback', 'medical-gate-settings', 'mpg_main_section');
});

// 

function mpg_sanitize_options($input) {
    return [
        'title'       => sanitize_text_field($input['title'] ?? ''),
        'content'     => sanitize_textarea_field($input['content'] ?? ''),
        'confirm'     => sanitize_text_field($input['confirm'] ?? ''),
        'confirm_btn' => sanitize_text_field($input['confirm_btn'] ?? ''),
        'exit_text'   => sanitize_text_field($input['exit_text'] ?? ''),
        'overlay_bg'  => sanitize_text_field($input['overlay_bg'] ?? ''),
        'btn_bg'      => sanitize_text_field($input['btn_bg'] ?? ''),
        'cookie_days' => max(1, intval($input['cookie_days'] ?? 365)),
    ];
}

// callbacks

function mpg_title_callback() {
    $o = get_option('mpg_options');
    echo '<input type="text" name="mpg_options[title]" value="' . esc_attr($o['title'] ?? '') . '" class="regular-text">';
}

function mpg_content_callback() {
    $o = get_option('mpg_options');
    echo '<textarea name="mpg_options[content]" rows="4" class="large-text">' . esc_textarea($o['content'] ?? '') . '</textarea>';
}

function mpg_confirm_callback() {
    $o = get_option('mpg_options');
    echo '<input type="text" name="mpg_options[confirm]" value="' . esc_attr($o['confirm'] ?? '') . '" class="regular-text">';
}

function mpg_confirm_btn_callback() {
    $o = get_option('mpg_options');
    echo '<input type="text" name="mpg_options[confirm_btn]" value="' . esc_attr($o['confirm_btn'] ?? '') . '" class="regular-text">';
}

function mpg_exit_text_callback() {
    $o = get_option('mpg_options');
    echo '<input type="text" name="mpg_options[exit_text]" value="' . esc_attr($o['exit_text'] ?? '') . '" class="regular-text">';
}

function mpg_overlay_bg_callback() {
    $o = get_option('mpg_options');
    echo '<input type="text" name="mpg_options[overlay_bg]" value="' . esc_attr($o['overlay_bg'] ?? '#00000099') . '" class="regular-text">';
}

function mpg_btn_bg_callback() {
    $o = get_option('mpg_options');
    echo '<input type="text" name="mpg_options[btn_bg]" value="' . esc_attr($o['btn_bg'] ?? '#28a745') . '" class="regular-text">';
}

function mpg_cookie_days_callback() {
    $o = get_option('mpg_options');
    echo '<input type="number" min="1" max="3650" name="mpg_options[cookie_days]" value="' . esc_attr($o['cookie_days'] ?? 365) . '">';
    echo '<p class="description">Ile dni zapamiętywać potwierdzenie (np. 365 = 1 rok)</p>';
}

// admin panel settings

function mpg_settings_page() { ?>
    <div class="wrap">
        <h1>Medical Gate <small>v<?php echo MPG_VERSION; ?></small></h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('mpg_settings');
            do_settings_sections('medical-gate-settings');
            submit_button('Zapisz ustawienia');
            ?>
        </form>
    </div>
<?php }
