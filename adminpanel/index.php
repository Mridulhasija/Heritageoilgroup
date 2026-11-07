<?php
// error_reporting(0);
error_reporting(E_ALL);

//// Display errors on the page
ini_set('display_errors', 1);
session_start();

// Set error logging to file
ini_set('log_errors', 1);
ini_set('error_log', "error.txt");

require_once __DIR__ . '/../include/connection.php';
$conn = db(); // Get database connection
include 'utils.php';
$utils = new utils();

require_once __DIR__ . '/../include/getdata.php';
$getdata = new Getdata();

// Include rate limiter (SQL-based)
require_once __DIR__ . '/../include/rate-limiter.php';
$rateLimiter = new RateLimiter($conn, 'tbl_rate_limit', 5, 900, 3600); // 5 attempts per 15 minutes, 1 hour block

$selsql = $utils->get_webinfo();
$resweb = mysqli_fetch_object($selsql);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if IP is blocked
    if ($rateLimiter->isBlocked()) {
        $blockTime = $rateLimiter->getBlockTimeRemaining();
        $timeRemaining = $rateLimiter->formatTimeRemaining($blockTime);
        $_SESSION['alert'] = ["danger", "Too many login attempts. Your IP has been temporarily blocked. Please try again in " . $timeRemaining . "."];
        header('Location: index.php');
        exit();
    }

    if (!isset($_POST['csrfmiddlewaretoken']) || $_POST['csrfmiddlewaretoken'] !== $_SESSION['csrf_token']) {
        $_SESSION['alert'] = ["danger", "CSRF token validation failed! Please refresh the page"];
        header('Location: index.php');
        exit();
    }

    if ($getdata->login($_POST['username'], $_POST['password'])) {
        // login Success - clear rate limit attempts
        $rateLimiter->clearAttempts();
        header("location: dashboard.php");
        exit();
    } else {
        // login Failed - record attempt
        $result = $rateLimiter->recordAttempt();

        if (!$result['allowed']) {
            // IP is now blocked
            $blockTime = $rateLimiter->getBlockTimeRemaining();
            $timeRemaining = $rateLimiter->formatTimeRemaining($blockTime);
            $_SESSION['alert'] = ["danger", "Too many failed login attempts. Your IP has been blocked for " . $timeRemaining . ". Please try again later."];
        } else {
            // Still allowed, show remaining attempts
            $remaining = $result['remaining'];
            $attemptMsg = $remaining > 0
                ? "Login Failed! Wrong username or password. " . $remaining . " attempt" . ($remaining !== 1 ? 's' : '') . " remaining."
                : "Login Failed! Wrong username or password.";

            $_SESSION['msg'] = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>' . $attemptMsg . '</strong> 
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
        }

        header("location: index.php");
        exit();
    }
} else {
    //remember cookies check
    $getdata->CheckRememberMe();

    if (isset($_SESSION['is_admin'])) {
        header("location: dashboard.php");
        exit();
    } else {
        // Check if IP is blocked (for GET requests)
        if ($rateLimiter->isBlocked()) {
            $blockTime = $rateLimiter->getBlockTimeRemaining();
            $timeRemaining = $rateLimiter->formatTimeRemaining($blockTime);
            $_SESSION['alert'] = ["danger", "Your IP address has been temporarily blocked due to too many failed login attempts. Please try again in " . $timeRemaining . "."];
        }

        // Generate a new CSRF token and set it in the session
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
?>
        <!DOCTYPE html>
        <html>

        <head>
            <meta charset="utf-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <title>Admin Login | <?php echo $resweb->name; ?></title>
            <meta name="robots" content="noindex, nofollow">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <!--bootstrap-->
            <link rel="stylesheet" href="plugins/bootstrap/css/bootstrap.min.css">
            <script src="plugins/bootstrap/js/bootstrap.min.js"></script>
            <style>
                html {
                    height: 100%
                }

                body {
                    margin: 0;
                    padding: 0;
                    font-family: sans-serif;
                    background: linear-gradient(#141e30, #243b55)
                }

                .login-box {
                    position: absolute;
                    top: 50%;
                    left: 50%;
                    width: 400px;
                    padding: 40px;
                    transform: translate(-50%, -50%);
                    background: rgba(0, 0, 0, .5);
                    box-sizing: border-box;
                    box-shadow: 0 15px 25px rgba(0, 0, 0, .6);
                    border-radius: 10px
                }

                .login-box h1 {
                    margin: 0 0 30px;
                    padding: 0;
                    color: #fff;
                    text-align: center
                }

                .login-box .user-box {
                    position: relative
                }

                .login-box .user-box input {
                    width: 100%;
                    padding: 10px 0;
                    font-size: 16px;
                    color: #fff;
                    margin-bottom: 30px;
                    border: none;
                    border-bottom: 1px solid #fff;
                    outline: none;
                    background: transparent
                }

                .login-box .user-box label {
                    position: absolute;
                    top: 0;
                    left: 0;
                    padding: 10px 0;
                    font-size: 16px;
                    color: #fff;
                    pointer-events: none;
                    transition: .5s
                }

                .login-box .user-box input:focus~label,
                .login-box .user-box input:valid~label {
                    top: -20px;
                    left: 0;
                    color: #03e9f4;
                    font-size: 12px
                }

                .login-box form button {
                    position: relative;
                    display: inline-block;
                    padding: 10px 20px;
                    color: #03e9f4;
                    font-size: 16px;
                    text-decoration: none;
                    text-transform: uppercase;
                    overflow: hidden;
                    transition: .5s;
                    letter-spacing: 4px;
                    background: transparent;
                    border: 2px solid #03e9f4
                }

                .login-box button:hover {
                    background: #03e9f4;
                    color: #fff;
                    border-radius: 5px
                }

                a {
                    color: #03e9f4;
                    text-decoration: none
                }

                a:hover {
                    color: #00c4cd
                }

                h1 {
                    font-size: 1.25rem;
                    font-weight: 700;
                    line-height: 1.25;
                    letter-spacing: -0.025em;
                    color: #ffffff;
                    margin-bottom: 1.25rem;
                }

                @media (min-width: 768px) {
                    h1 {
                        font-size: 1.5rem;
                    }
                }
            </style>
        </head>

        <body>
            <span>
                <?php
                if (isset($_SESSION["alert"])) :
                ?>
                    <div class="alert alert-<?= $_SESSION["alert"][0]; ?> alert-dismissible fade show" role="alert">
                        <i class="fa fa-times-circle-o"></i> <?= $_SESSION["alert"][1]; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php
                    unset($_SESSION["alert"]);
                endif;
                ?>
            </span>
            <div class="login-box">
                <h1><?php echo $resweb->name; ?> Login</h1>
                <form method="post" action="">
                    <div class="user-box">
                        <input type="text" name="username" required>
                        <label>Username</label>
                    </div>
                    <div class="user-box">
                        <input type="password" name="password" required>
                        <label>Password</label>
                    </div>
                    <input type="hidden" name="csrfmiddlewaretoken" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="rememberMe" name="remember_me">
                        <label class="form-check-label text-white" for="rememberMe">Remember Me</label>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="mt-4 mb-2 w-100">Login</button>
                    </div>
                </form>
            </div>
        </body>

        </html>
<?php }
} ?>
