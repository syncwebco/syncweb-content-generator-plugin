<?php
/**
 * Plugin Name: SyncWeb Content Generator Plugin
 * Plugin URI:  https://syncweb.co
 * Description: Adds an "AI" button next to text fields in the WP/Elementor editor to generate content via ChatGPT.
 * Version:     2.0
 * Author:      Syncweb
 * Author URI:  https://syncweb.co
 * License:     GPL2
 * Text Domain: syncweb-content-generator-plugin
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * 1. Register plugin settings (store API key).
 */
function swcg_register_settings() {
    register_setting( 'swcg_settings_group', 'swcg_openai_api_key', [
        'sanitize_callback' => 'sanitize_text_field'
    ] );
}
add_action( 'admin_init', 'swcg_register_settings' );

/**
 * 2. Create settings page for the API key.
 */
function swcg_add_settings_page() {
    add_options_page(
        'Syncweb Content Generator',
        'Syncweb Content Generator',
        'manage_options',
        'swcg-settings',
        'swcg_render_settings_page'
    );
}
add_action( 'admin_menu', 'swcg_add_settings_page' );

/**
 * 3. Render settings page HTML.
 */
function swcg_render_settings_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    ?>
    <div class="wrap">
        <h1>Syncweb Content Generator Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields( 'swcg_settings_group' );
            do_settings_sections( 'swcg_settings_group' );
            ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">OpenAI API Key</th>
                    <td>
                        <input
                            type="text"
                            name="swcg_openai_api_key"
                            value="<?php echo esc_attr( get_option('swcg_openai_api_key') ); ?>"
                            style="width: 400px;"
                        />
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

/**
 * 4. Enqueue a global admin script on all WP admin pages.
 *    - You could limit to Elementor-only pages if you prefer,
 *      for example by checking specific query params or Elementor conditions.
 */
function swcg_enqueue_admin_scripts( $hook ) {
    // For example, if you only want to run in Elementor editor, you can check:
    // if ( ! isset( $_GET['action'] ) || $_GET['action'] !== 'elementor' ) {
    //     return;
    // }

    wp_enqueue_script(
        'swcg_admin_js',
        plugin_dir_url( __FILE__ ) . 'admin.js',
        [ 'jquery' ],
        '2.0',
        true
    );

    wp_localize_script( 'swcg_admin_js', 'SWCG_Data', [
        'ajaxUrl' => admin_url( 'admin-ajax.php' ),
        'nonce'   => wp_create_nonce( 'swcg-nonce' )
    ]);
}
add_action( 'admin_enqueue_scripts', 'swcg_enqueue_admin_scripts' );

/**
 * 5. AJAX handler that calls OpenAI/ChatGPT.
 */
function swcg_ajax_generate_content() {
    check_ajax_referer( 'swcg-nonce', 'security' );

    $prompt = isset($_POST['prompt']) ? sanitize_text_field($_POST['prompt']) : '';

    $api_key = get_option( 'swcg_openai_api_key' );
    if ( empty( $api_key ) ) {
        wp_send_json_error( [ 'message' => 'OpenAI API key not set. Please go to Settings -> Syncweb Content Generator.' ] );
    }

    $response = swcg_call_openai_api( $prompt, $api_key );

    if ( is_wp_error( $response ) ) {
        wp_send_json_error( [ 'message' => $response->get_error_message() ] );
    } else {
        wp_send_json_success( [ 'content' => $response ] );
    }
}
add_action( 'wp_ajax_swcg_generate_content', 'swcg_ajax_generate_content' );

/**
 * 6. Actual call to the OpenAI API.
 */
function swcg_call_openai_api( $prompt, $api_key ) {
    $url = 'https://api.openai.com/v1/chat/completions';
    $body = [
        'model'       => 'gpt-3.5-turbo',
        'messages'    => [
            [ 'role' => 'system', 'content' => 'You are a helpful assistant.' ],
            [ 'role' => 'user',   'content' => $prompt ]
        ],
        'max_tokens'  => 300,
        'temperature' => 0.7
    ];
    $headers = [
        'Authorization' => 'Bearer ' . $api_key,
        'Content-Type'  => 'application/json'
    ];

    $response = wp_remote_post( $url, [
        'headers' => $headers,
        'body'    => wp_json_encode( $body )
    ] );

    if ( is_wp_error( $response ) ) {
        return $response;
    }

    $status_code = wp_remote_retrieve_response_code( $response );
    if ( $status_code !== 200 ) {
        return new WP_Error( 'openai_error', 'OpenAI API request failed with status ' . $status_code );
    }

    $response_body = wp_remote_retrieve_body( $response );
    $data          = json_decode( $response_body, true );

    if ( isset( $data['choices'][0]['message']['content'] ) ) {
        return trim( $data['choices'][0]['message']['content'] );
    }

    return new WP_Error( 'openai_error', 'Invalid API response structure.' );
}
