<?php

trait GetdataAuthTrait
{
    public function isLoggedIn()
    {
        return isset($_SESSION['user_id']);
    }

    public function login($username, $password)
    {
        $conn = db();
        // Sanitize input data
        $username = $this->sanitizeInput($username);

        // Fetch user from the database
        $sql = "SELECT user_id, username, password_hash, is_email_verified, is_admin FROM tbl_user WHERE username = ?";
        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 's', $username);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $user_id, $db_username, $db_password_hash, $is_email_verified, $is_admin);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);

            // Verify password (ensure db password hash is a string to avoid null being passed)
            if ($db_username === $username && is_string($db_password_hash) && password_verify($password, $db_password_hash)) {
                if ($is_email_verified === 1) {
                    $_SESSION['user_id'] = $user_id;

                    //admin or not
                    if ($is_admin === 1) {
                        $_SESSION['is_admin'] = 1;
                    }

                    session_regenerate_id(true);

                    $updateLastLoginSql = "UPDATE tbl_user SET last_login = CURRENT_TIMESTAMP WHERE user_id = ?";
                    $updateLastLoginStmt = mysqli_prepare($conn, $updateLastLoginSql);

                    if ($updateLastLoginStmt) {
                        mysqli_stmt_bind_param($updateLastLoginStmt, 's', $user_id);
                        mysqli_stmt_execute($updateLastLoginStmt);
                        mysqli_stmt_close($updateLastLoginStmt);
                    }

                    // Check if "Remember Me" checkbox is checked
                    $rememberMe = isset($_POST['remember_me']) && $_POST['remember_me'] === 'on';

                    // If the user wants to be remembered, set a hashed cookie
                    if ($rememberMe) {
                        $uniqueSalt = 'REMEMBER_ME';
                        $token = hash('sha256', (string) $db_password_hash . ':' . $uniqueSalt);

                        // Base64 encode the username
                        $encodedUsername = base64_encode($username);

                        setcookie('username', $encodedUsername, time() + (86400 * 7), '/');
                        setcookie('remember_token', $token, time() + (86400 * 7), '/'); // Cookie set to expire in 7 days
                    }

                    return true;
                } else {
                    $_SESSION['alert'] = ["danger", "Please verify your email by clicking the link sent to your email."];
                    return false;
                }
            } else {
                $_SESSION['alert'] = ["danger", "Invalid username or password"];
                return false;
            }
        } else {
            //  echo "Error: " . mysqli_error($conn);
            $_SESSION['alert'] = ["danger", "Error: Something Went Wrong"];
            return false;
        }
    }

    public function CheckRememberMe()
    {
        $conn = db();
        if (!isset($_SESSION['user_id']) && isset($_COOKIE['username']) && isset($_COOKIE['remember_token'])) {
            $storedUsername = $this->sanitizeInput(base64_decode($_COOKIE['username']));

            $storedToken = $this->sanitizeInput($_COOKIE['remember_token']);


            // Run the SQL query to verify the username and token
            $sql = "SELECT user_id, username, password_hash, is_admin FROM tbl_user WHERE username = ?";
            $stmt = mysqli_prepare($conn, $sql);

            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 's', $storedUsername);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_bind_result($stmt, $user_id, $db_username, $db_password_hash, $is_admin);
                mysqli_stmt_fetch($stmt);
                mysqli_stmt_close($stmt);

                $uniqueSalt = 'REMEMBER_ME';
                $generatedToken = hash('sha256', $db_password_hash . ':' . $uniqueSalt);

                // Check if the verification was successful
                if ($generatedToken === $storedToken) {
                    $_SESSION['user_id'] = $user_id;

                    //admin or not
                    if ($is_admin === 1) {
                        $_SESSION['is_admin'] = 1;
                    }

                    session_regenerate_id(true);

                    $updateLastLoginSql = "UPDATE tbl_user SET last_login = CURRENT_TIMESTAMP WHERE user_id = ?";
                    $updateLastLoginStmt = mysqli_prepare($conn, $updateLastLoginSql);

                    if ($updateLastLoginStmt) {
                        mysqli_stmt_bind_param($updateLastLoginStmt, 's', $user_id);
                        mysqli_stmt_execute($updateLastLoginStmt);
                        mysqli_stmt_close($updateLastLoginStmt);
                    }
                } else {
                    // delete invalid cookies
                    setcookie('username', '', time() - 3600, '/');
                    setcookie('remember_token', '', time() - 3600, '/');
                }
            } else {
                // SQL query preparation failed, delete cookies
                setcookie('username', '', time() - 3600, '/');
                setcookie('remember_token', '', time() - 3600, '/');
            }
        }
    }

    public function logout()
    {
        if (isset($_SESSION['user_id'])) {
            // Destroy all session data
            session_destroy();

            //delete remember me cookies
            setcookie('username', '', time() - 3600, '/');
            setcookie('remember_token', '', time() - 3600, '/');
        }
    }
}

