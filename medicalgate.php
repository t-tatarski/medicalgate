<?php
/**
 * Plugin Name: MedicalGate
 * Description: popup z informacją dla potwierdzenia profesjonalisty
 * Author: tatarski
 */

defined('ABSPATH') || exit;

define('MPG_VERSION', '1.0.0');
define('MPG_URL', plugin_dir_url(__FILE__));

/**
 *  CSS i JS
 */
add_action('wp_enqueue_scripts', function () {

    if (isset($_COOKIE['mpg_professional'])) {
        return;
    }

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
});

/**
 * HTML popupu
 */
add_action('wp_footer', function () {

    if (isset($_COOKIE['mpg_professional'])) {
        return;
    }
    ?>

    <div id="mpg-overlay">
        <div class="mpg-modal">
            <h2>Informacja</h2>
            <p>
                Treści na stronie mają charakter medyczny i są przeznaczone
                dla lekarzy dentystów oraz techników dentystycznych. 
            </p>
            <p>
                Jeśli jesteś profesjonalistą z branży, potwierdź proszę.
            </p>

            <div class="mpg-actions">
                <button id="mpg-accept">jestem profesjonalisą</button>
                <a href="https://www.google.com" class="mpg-exit">opuść stronę</a>
            </div>
        </div>
    </div>

    <?php
});
