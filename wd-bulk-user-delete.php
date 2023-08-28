<?php
/*
Plugin Name: WD Bulk User Delete
Plugin URI:  https://wolfdevs.com
Description: This plugin helps delete users in bulk.
Version:     1.0
Author:      WolfDevs
Author URI:  https://wolfdevs.com
License:     GPL2
*/

//Exit if accessed directly
if( !defined( 'ABSPATH' ) ){
	return;
}

// Add a new submenu under Users
function user_deletion_menu() {
    add_users_page('WD Delete Users by Role', 'Bulk Delete', 'manage_options', 'wd-delete-users-by-role', 'delete_users_by_role_page');
}
add_action('admin_menu', 'user_deletion_menu');

// Display the page content
function delete_users_by_role_page() {
    $roles = get_editable_roles();
    unset($roles['administrator']); // Remove the administrator role from the list
    ?>
    <div class="wrap">
        <h2>WD Delete Users by Role</h2>
        <p><strong>Warning:</strong> This action is irreversible. Please proceed with caution.</p>
        <form id="delete_users_form">
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="role_to_delete">Select Role</label></th>
                    <td>
                        <select id="role_to_delete" name="role_to_delete">
                            <?php foreach ($roles as $role_key => $role) : ?>
                                <option value="<?php echo esc_attr($role_key); ?>"><?php echo esc_html($role['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="users_per_batch">Users per Batch</label></th>
                    <td>
                        <input type="number" id="users_per_batch" name="users_per_batch" value="10" min="1">
                        <p class="description">Number of users to delete at a time.</p>
                    </td>
                </tr>
            </table>
            <p class="submit">
                <button type="button" id="start_deletion" class="button button-primary">Start Deletion</button>
            </p>
        </form>
        <div id="deletion_status"></div>
    </div>
    <?php
}

// Enqueue the necessary JavaScript
function enqueue_deletion_script($hook) {
    if ('users_page_wd-delete-users-by-role' != $hook) {
        return;
    }
    wp_enqueue_script('user-deletion-script', plugin_dir_url(__FILE__) . 'js/user-deletion.js', array('jquery'), time(), true);
    wp_localize_script('user-deletion-script', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
}
add_action('admin_enqueue_scripts', 'enqueue_deletion_script');

// AJAX handler for deleting users
function delete_users_by_role_handler() {
    $role = sanitize_text_field($_POST['role']);
    $users_per_batch = intval($_POST['users_per_batch']);
    $users = get_users(array('role' => $role, 'number' => $users_per_batch)); // Get users based on the input value

    foreach ($users as $user) {
        wp_delete_user($user->ID);
    }

    echo count($users); // Return the number of deleted users
    die();
}
add_action('wp_ajax_delete_users_by_role', 'delete_users_by_role_handler');
