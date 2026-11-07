<?php
require_once __DIR__ . '/components/config-header.php';
include("components/header.php");

$conn = db();
$page_title = "Users Management";
$table = "tbl_user";

$action = (isset($_GET['action']) && $_GET['action'] == "edit") ? 'edit' : 'add';

if ($action == "edit") {
    $page_subtitle = "Edit User";
    $id = isset($_GET['id']) ? $_GET['id'] : null;
    $data = ($id) ? mysqli_fetch_array($getdata->FetchQuery('SELECT * FROM `' . $table . '` WHERE user_id = ?', array($id))) : null;
} else {
    $page_subtitle = "Add User";
}
?>

<form method="post" action="user.php?<?= ($action == 'add') ? 'action=add' : 'action=edit&id=' . $id; ?>" id="userForm">
    <div class="content-wrapper admin-editor">
        <!-- Content Header (Page header) -->
        <section class="content-header d-flex align-items-center justify-content-between">
            <div>
                <h1 style="margin: 0; padding-bottom: 1rem"><?= $page_subtitle; ?></h1>
                <ul class="breadcrumb" style="background: transparent;padding: 0;margin-left: 0.5rem;">
                    <li><a href="dashboard.php"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                    <li><a href="user.php"><?= $page_title; ?></a></li>
                    <li class="active"><?= $page_subtitle; ?></li>
                </ul>
            </div>
            <div>
                <button type="submit" name="Submit" class="btn btn-success" id='saveButton'><i class="fa fa-floppy-o" aria-hidden="true"></i></button>
                <? if ($action === 'edit'): ?>
                    <a class='btn btn-primary' href='?action=add' role='button'><i class='fa fa-plus' aria-hidden='true'></i></a>
                <? endif ?>
            </div>
        </section>


        <section class="content">
            <div class="row">
                <!-- Main content -->
                <section class="col-lg-9">
                    <div class="box box-info">
                        <?php include_once("components/alert-msg.php"); ?>
                        <div class="box-body">
                            <div class="row">

                                <div class="form-group">
                                    <div class="form-floating mb-3">
                                        <input type="text" <?= ($action === 'edit') ? 'value="' . h($data['username']) . '"' : ''; ?> class="form-control" id="username" name="username" placeholder="Username" required>
                                        <label for="username">Username <span class="text-danger">*</span></label>
                                    </div>
                                </div>

                                <div class="form-group col-sm-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" <?= ($action === 'edit') ? 'value="' . h($data['first_name']) . '"' : ''; ?> class="form-control" id="first_name" name="first_name" placeholder="First Name" required>
                                        <label for="first_name">First Name <span class="text-danger">*</span></label>
                                    </div>
                                </div>
                                <div class="form-group col-sm-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" <?= ($action === 'edit') ? 'value="' . h($data['last_name']) . '"' : ''; ?> class="form-control" id="last_name" name="last_name" placeholder="Last Name" required>
                                        <label for="last_name">Last Name <span class="text-danger">*</span></label>
                                    </div>
                                </div>
                                <div class="form-group col-sm-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" <?= ($action === 'edit') ? 'value="' . h($data['phone_number']) . '"' : ''; ?> class="form-control" id="phone_number" name="phone_number" placeholder="Phone Number">
                                        <label for="phone_number">Phone Number</label>
                                    </div>
                                </div>
                                <div class="form-group col-sm-6">
                                    <div class="form-floating mb-3">
                                        <input type="email" <?= ($action === 'edit') ? 'value="' . h($data['email']) . '"' : ''; ?> class="form-control" id="email" name="email" placeholder="Email" required>
                                        <label for="email">Email <span class="text-danger">*</span></label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="form-floating mb-3">
                                    <input type="text" <?= ($action === 'edit') ? 'value="' . h($data['country']) . '"' : ''; ?> class="form-control" id="country" name="country" placeholder="Country">
                                    <label for="country">Country</label>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="form-floating mb-3">
                                    <input type="text" <?= ($action === 'edit') ? 'value="' . h($data['address_1']) . '"' : ''; ?> class="form-control" id="address_1" name="address_1" placeholder="Address 1">
                                    <label for="address_1">Address 1</label>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="form-floating mb-3">
                                    <input type="text" <?= ($action === 'edit') ? 'value="' . h($data['address_2']) . '"' : ''; ?> class="form-control" id="address_2" name="address_2" placeholder="Address 2">
                                    <label for="address_2">Address 2</label>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="form-floating mb-3">
                                    <input type="text" <?= ($action === 'edit') ? 'value="' . h($data['city']) . '"' : ''; ?> class="form-control" id="city" name="city" placeholder="City">
                                    <label for="city">City</label>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="form-floating mb-3">
                                    <input type="text" <?= ($action === 'edit') ? 'value="' . h($data['state']) . '"' : ''; ?> class="form-control" id="state" name="state" placeholder="State">
                                    <label for="state">State</label>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="form-floating mb-3">
                                    <input type="text" <?= ($action === 'edit') ? 'value="' . h($data['zip_code']) . '"' : ''; ?> class="form-control" id="zip_code" name="zip_code" placeholder="Zip Code">
                                    <label for="zip_code">Zip Code</label>
                                </div>
                            </div>


                        </div>
                    </div>
                </section>
                <section class="col-lg-3">
                    <div class="box box-info">
                        <div class="box-body">
                            <div class="form-group py-2 text-danger fw-bold">
                                is Admin &nbsp;
                                <input type="checkbox" name="is_admin" value="1" <?= (($action === 'edit') && ($data['is_admin'] === 1)) ? 'checked' : ''; ?>>
                            </div>

                            <div class="form-group py-2">
                                Verified Email&nbsp;
                                <input type="checkbox" name="is_email_verified" value="1" <?= (($action === 'edit') && ($data['is_email_verified'] === 1)) ? 'checked' : ''; ?>>
                            </div>


                            <div class="accordion py-2" id="passwordAccordion">
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingOne">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                            Set <?= ($action === 'edit') ? "New " : ''; ?>Password
                                        </button>
                                    </h2>
                                    <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#passwordAccordion">
                                        <div class="accordion-body">
                                            <style>
                                                .form-group .eye-icon {
                                                    margin-left: -2rem
                                                }
                                            </style>
                                            <div class="form-group position-relative">
                                                <label for="id_password">Password:</label>
                                                <div class='d-flex align-items-center'>
                                                    <input type="password" name="password" placeholder="Enter password" class="form-control" id="id_password" <?= ($action === 'add') ? "required" : ''; ?>>
                                                    <i class="fa fa-eye eye-icon" data-target="id_password"></i>
                                                </div>
                                            </div>

                                            <div class="form-group position-relative">
                                                <label for="id_confirm_password">Confirm password:</label>
                                                <div class='d-flex align-items-center'>
                                                    <input type="password" name="confirm_password" placeholder="Enter password again" class="form-control" id="id_confirm_password" disabled>
                                                    <i class="fa fa-eye eye-icon" data-target="id_confirm_password"></i>
                                                </div>
                                                <div id="password-error" class="text-danger" style="display: none">Passwords do not match</div>
                                            </div>

                                            <div class="form-check mb-3">
                                                <input type="checkbox" class="form-check-input" id="allowWeakPassword">
                                                <label class="form-check-label" for="allowWeakPassword">Allow Weak Password</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </section>
            </div>
        </section>

    </div>
</form>
<script src="assets/js/media-gallery.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const passwordField = document.getElementById('id_password');
        const confirmPasswordField = document.getElementById('id_confirm_password');
        const eyeIcons = document.querySelectorAll('.eye-icon');
        const errorMessage = document.getElementById('password-error');
        const allowWeakPasswordCheckbox = document.getElementById('allowWeakPassword');
        const userForm = document.getElementById('userForm');

        // Toggle password visibility
        function togglePasswordVisibility(inputField, eyeIcon) {
            const fieldType = inputField.type === 'password' ? 'text' : 'password';
            inputField.type = fieldType;

            // Change eye icon based on visibility
            eyeIcon.classList.toggle('fa-eye');
            eyeIcon.classList.toggle('fa-eye-slash');
        }

        // Check password strength
        function checkPasswordStrength(password) {
            if (allowWeakPasswordCheckbox.checked) {
                return true; // Skip strength check if weak passwords are allowed
            }

            // Customize the criteria based on requirements
            const minLength = 8;
            const hasUppercase = /[A-Z]/.test(password);
            const hasLowercase = /[a-z]/.test(password);
            const hasNumber = /\d/.test(password);

            return password.length >= minLength && hasUppercase && hasLowercase && hasNumber;
        }

        // Event listener for eye icons
        eyeIcons.forEach(function(eyeIcon) {
            eyeIcon.addEventListener('click', function() {
                const targetFieldId = this.dataset.target;
                const targetField = document.getElementById(targetFieldId);
                togglePasswordVisibility(targetField, this);
            });
        });

        // Event listener for password input
        passwordField.addEventListener('input', function() {
            const password = this.value;

            // Validate password strength
            if (password.length > 0) {
                passwordField.required = true;
                confirmPasswordField.required = true;
            } else {
                passwordField.required = false;
                confirmPasswordField.required = false;
            }

            if (!checkPasswordStrength(password)) {
                errorMessage.textContent = 'Password is weak. Please include at least 8 characters, an uppercase letter, a lowercase letter, and a number.';
                errorMessage.style.display = 'block';
                confirmPasswordField.disabled = true;
            } else {
                confirmPasswordField.disabled = false;
                errorMessage.style.display = 'none';
            }
        });

        // Event listener for password confirmation
        confirmPasswordField.addEventListener('input', function() {
            const password = passwordField.value;
            const confirmPassword = this.value;

            // Validate password match
            if (password !== confirmPassword) {
                errorMessage.textContent = 'Passwords do not match.';
                errorMessage.style.display = 'block';
            } else {
                errorMessage.style.display = 'none';
            }
        });

        // Event listener for allowWeakPassword checkbox
        allowWeakPasswordCheckbox.addEventListener('change', function() {
            if (allowWeakPasswordCheckbox.checked) {
                errorMessage.style.display = 'none'; // Clear any existing error message

                if (passwordField.value.length > 0) {
                    confirmPasswordField.disabled = false;
                }
            }
        });

        // Event listener for form submission
        userForm.addEventListener('submit', function(event) {
            const password = passwordField.value;
            const confirmPassword = confirmPasswordField.value;

            // Check passwords match and if password is strong
            if (password.length > 0 && password !== confirmPassword || (password.length > 0 && !allowWeakPasswordCheckbox.checked && !checkPasswordStrength(password))) {
                // Prevent form submission
                event.preventDefault();
            }
        });
    });
</script>


<?php include("components/media-gallery-modal.php"); ?>
<?php include("components/footer.php"); ?>
