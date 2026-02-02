<?php
/*
Plugin Name: Listado de usuarios
Description: Listado de usuarios
Version: 1.0
Author: Daniel Delgado Rodríguez
*/

if (!defined('ABSPATH')) exit;

/**
 * SHORTCODE
 */
add_shortcode('listado_usuarios', 'lu_render_shortcode');
function lu_render_shortcode() {

    wp_enqueue_script(
        'lu-script',
        plugin_dir_url(__FILE__) . 'js/listado-usuarios.js',
        ['jquery'],
        null,
        true
    );

    wp_localize_script('lu-script', 'LU', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('lu_nonce')
    ]);

    wp_enqueue_style(
        'lu-style',
        plugin_dir_url(__FILE__) . 'css/listado-usuarios.css'
    );

    ob_start();
    ?>
    <div id="lu-container">

        <!-- FORMULARIO -->
        <form id="lu-search-form">

            <input type="text" name="search" placeholder="Buscar…">
            <button type="submit">Buscar</button>

            <div id="lu-filters">
                <span><strong>Buscar por:</strong></span>

                <label>
                    <input type="checkbox" name="fields[]" value="username" checked>
                    Usuario
                </label>

                <label>
                    <input type="checkbox" name="fields[]" value="name" checked>
                    Nombre
                </label>

                <label>
                    <input type="checkbox" name="fields[]" value="surname" checked>
                    Apellidos
                </label>

                <label>
                    <input type="checkbox" name="fields[]" value="email" checked>
                    Email
                </label>
            </div>
        </form>

        <!-- TABLA RENDERIZADA POR AJAX -->
        <div id="lu-results"></div>
        <div id="lu-pagination"></div>
    </div>
    <?php
    return ob_get_clean();
}


/**
 * AJAX
 */
add_action('wp_ajax_lu_get_users', 'lu_get_users');
add_action('wp_ajax_nopriv_lu_get_users', 'lu_get_users');

function lu_get_users() {

    check_ajax_referer('lu_nonce', 'nonce');

    $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
    $fields = isset($_POST['fields']) ? (array) $_POST['fields'] : [];
    $page   = isset($_POST['page']) ? max(1, (int) $_POST['page']) : 1;
    $per_page = 5;

    // Traer usuarios del API simulado
    $api_response = lu_fake_api();
    $data = json_decode($api_response, true);
    $users = $data['usuarios'] ?? [];

    // Filtrado por campos seleccionados
    if ($search && !empty($fields)) {
        $users = array_filter($users, function ($user) use ($search, $fields) {
            $haystack = '';

            if (in_array('username', $fields)) {
                $haystack .= ' ' . $user['username'];
            }
            if (in_array('name', $fields)) {
                $haystack .= ' ' . $user['name'];
            }
            if (in_array('surname', $fields)) {
                $haystack .= ' ' . $user['surname1'] . ' ' . $user['surname2'];
            }
            if (in_array('email', $fields)) {
                $haystack .= ' ' . $user['email'];
            }

            return stripos($haystack, $search) !== false;
        });
    }

    // Paginación
    $total_users = count($users);
    $total_pages = ceil($total_users / $per_page);
    $offset = ($page - 1) * $per_page;
    $users  = array_slice($users, $offset, $per_page);

    // Render vista
    ob_start();
    include plugin_dir_path(__FILE__) . 'views/users-table.php';
    $html = ob_get_clean();

    wp_send_json([
        'html' => $html,
        'total_pages' => $total_pages,
        'current' => $page
    ]);
}

/**
 * API SIMULADA (JSON)
 */
function lu_fake_api() {

    $response = ['usuarios' => []];

    for ($i = 1; $i <= 23; $i++) {
        $response['usuarios'][] = [
            'id'       => $i,
            'username' => "Usuario{$i}",
            'email'    => "Admin{$i}@yopmail.com",
            'name'     => "Nombre{$i}",
            'surname1' => "1ER_Apellido{$i}",
            'surname2' => "2DO_Apellido{$i}",
        ];
    }

    return json_encode($response);
}
