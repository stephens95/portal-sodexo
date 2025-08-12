<script>
    $('#usersTable').DataTable({
        paging: true,
        searching: true,
        ordering: true
    });

    $(document).on('click', '.btn-edit', function() {
        let userId = $(this).data('id');

        $.get("<?= base_url('/users/getUserById/') ?>" + userId, function(response) {
            $('#edit_user_id').val(response.user.user_id);
            $('#edit_name').val(response.user.name);
            $('#edit_email').val(response.user.email);

            // isi dropdown buyers
            let buyerSelect = $('#buyer_id');
            buyerSelect.empty();
            buyerSelect.append('<option value="">-- Select Buyer --</option>');
            response.buyers.forEach(function(b) {
                buyerSelect.append(
                    `<option value="${b.buyer_id}" ${b.buyer_id == response.user.buyer_id ? 'selected' : ''}>
                    ${b.buyer_name}
                </option>`
                );
            });

            $('#editUserModal').modal('show');
        });
    });

    $('#editUserForm').submit(function(e) {
        e.preventDefault();

        $.post("<?= base_url('/users/updateUser') ?>", $(this).serialize(), function(response) {
            if (response.status === 'success') {
                location.reload();
            }
        }, 'json');
    });

    $('#btnCreateUser').click(function() {
        $.get('/buyers/listAll', function(buyers) {
            let buyerSelect = $('#create_buyer_id');
            buyerSelect.empty();
            buyerSelect.append('<option value="">-- Select Buyer --</option>');
            buyers.forEach(function(b) {
                buyerSelect.append(`<option value="${b.buyer_id}">${b.buyer_name}</option>`);
            });

            // Ambil roles
            $.get('/roles/listAll', function(roles) {
                let roleSelect = $('#create_role_id');
                roleSelect.empty();
                roleSelect.append('<option value="">-- Select Role --</option>');
                roles.forEach(function(r) {
                    roleSelect.append(`<option value="${r.role_id}">${r.role_name}</option>`);
                });

                $('#createUserModal').modal('show');
            });
        });
    });
</script>