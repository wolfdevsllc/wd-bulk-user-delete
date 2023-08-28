jQuery(document).ready(function($) {
    var totalDeleted = 0;

    $('#start_deletion').click(function() {
        var role = $('#role_to_delete').val();
        var usersPerBatch = $('#users_per_batch').val();
        $('#deletion_status').html('<p><span id="deleted_count">0</span> users deleted so far...</p>'); // Initial message
        deleteUsers(role, usersPerBatch);
    });

    function deleteUsers(role, usersPerBatch) {
        $.post(ajax_object.ajax_url, {
            action: 'delete_users_by_role',
            role: role,
            users_per_batch: usersPerBatch
        }, function(response) {
            totalDeleted += parseInt(response);
            $('#deleted_count').text(totalDeleted); // Update the count

            if (parseInt(response) > 0) {
                deleteUsers(role, usersPerBatch); // Continue deleting
            } else {
                $('#deletion_status').html('<p>All users with the role ' + role + ' have been deleted. Total: ' + totalDeleted + ' users.</p>');
            }
        });
    }
});
