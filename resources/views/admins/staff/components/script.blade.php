<script>
    function resetPassword(userId) {
        Swal.fire({
            title: "Reset Password",
            text: "Enter a new password for this user:",
            input: "password",
            inputPlaceholder: "Enter new password",
            inputAttributes: {
                minlength: 8
            },
            showCancelButton: true,
            confirmButtonText: "Reset",
            cancelButtonText: "Cancel",
            preConfirm: (password) => {
                if (!password || password.length < 8) {
                    Swal.showValidationMessage("Password must be at least 8 characters long");
                    return false;
                }
                return password;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                let newPassword = result.value;

                fetch(`/admin/staff/${userId}/reset-password`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': getCsrfToken(),
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            password: newPassword
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire("Success!", data.message, "success");
                        } else {
                            Swal.fire("Error", data.message || "Something went wrong.", "error");
                        }
                    })
                    .catch(error => {
                        console.log(error);
                        Swal.fire("Error", "An error occurred while resetting the password.", "error");
                    });
            }
        });
    }

    // Delete user function
    function deleteUser(userId) {
        showConfirm("Are you sure?", "This action cannot be undone.")
            .then((result) => {
                if (result.isConfirmed) {
                    fetch(`/admin/staff/${userId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': getCsrfToken(),
                                'Content-Type': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire("Deleted!", data.message, "success")
                                    .then(() => {
                                        window.location.href = "{{ route('staff.index') }}";
                                    });
                            } else {
                                Swal.fire("Error", data.message || "Something went wrong.", "error");
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire("Error", "An error occurred while deleting the user.", "error");
                        });
                }
            });
    }


    function toggleStatus(userId) {
        showConfirm("Are you sure?", "You want to change this user's status?")
            .then((result) => {
                if (result.isConfirmed) {
                    fetch(`/admin/staff/${userId}/toggle-status`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': getCsrfToken(),
                                'Content-Type': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire("Success!", data.message, "success")
                                    .then(() => location.reload());
                            } else {
                                Swal.fire("Error", data.message || "Something went wrong.", "error");
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire("Error", "An error occurred while updating the user status.", "error");
                        });
                }
            });
    }
</script>
