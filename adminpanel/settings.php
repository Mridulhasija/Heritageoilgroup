<?php
require_once __DIR__ . '/components/config-header.php';

$conn = db();

$conn = db();
$page_title = "Website Settings";
$table = "tbl_config";

$page_subtitle = "Edit " . $page_title;

$id_col = 'id';
$id_type = "i";

$currentUrl = $_SERVER['PHP_SELF'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $id = 1;

        $name = isset($_POST['name']) ? trim($_POST['name']) : null;
        $email_1 = isset($_POST['email_1']) ? trim($_POST['email_1']) : null;
        $email_2 = isset($_POST['email_2']) ? trim($_POST['email_2']) : null;
        $phone_1 = isset($_POST['phone_1']) ? trim($_POST['phone_1']) : null;
        $phone_2 = isset($_POST['phone_2']) ? trim($_POST['phone_2']) : null;
        $address = isset($_POST['address']) ? trim($_POST['address']) : null;
        $google_map = isset($_POST['google_map']) ? trim($_POST['google_map']) : null;
        $logo = isset($_POST['logo']) ? trim($_POST['logo']) : null;
        $facebook = isset($_POST['facebook']) ? trim($_POST['facebook']) : null;
        $twitter = isset($_POST['twitter']) ? trim($_POST['twitter']) : null;
        $instagram = isset($_POST['instagram']) ? trim($_POST['instagram']) : null;
        $linkedin = isset($_POST['linkedin']) ? trim($_POST['linkedin']) : null;
        $youtube = isset($_POST['youtube']) ? trim($_POST['youtube']) : null;
        $tiktok = isset($_POST['tiktok']) ? trim($_POST['tiktok']) : null;
        $currency = isset($_POST['currency']) ? trim($_POST['currency']) : null;
        $decimal_separator = isset($_POST['decimal_separator']) ? trim($_POST['decimal_separator']) : null;
        $thousand_separator = isset($_POST['thousand_separator']) ? trim($_POST['thousand_separator']) : null;
        $tax = isset($_POST['tax']) ? trim($_POST['tax']) : null;
        $stmt = null;

        $stmt = $conn->prepare(
            "UPDATE $table SET name = ?, email_1 = ?, email_2 = ?, phone_1 = ?, phone_2 = ?, address = ?, google_map = ?, logo = ?, facebook = ?, twitter = ?, instagram = ?, linkedin = ?, youtube = ?, tiktok = ?, currency = ?, decimal_separator = ?, thousand_separator = ?, tax = ? WHERE id = ?"
        );

        $stmt->bind_param("sssssssssssssssssii", $name, $email_1, $email_2, $phone_1, $phone_2, $address, $google_map, $logo, $facebook, $twitter, $instagram, $linkedin, $youtube, $tiktok, $currency, $decimal_separator, $thousand_separator, $tax, $id);

        if ($stmt) {
            $stmt->execute();
            $stmt->close();
            $_SESSION["success-msg"] = "Settings successfully edited";
        }

        header('Location: ' . strtok($currentUrl, '?'));
    } catch (Exception $e) {
        $_SESSION["error-msg"] = "Error: " . $e->getMessage();
    }
    exit();
} else {
    //show page
    include("components/header.php");
    $data = mysqli_fetch_array($getdata->FetchQuery('SELECT * FROM `' . $table . '` WHERE id = 1', []));
?>

    <form method="post" action="<?= strtok($currentUrl, '?') ?>">
        <div class="content-wrapper admin-editor">
            <!-- Content Header (Page header) -->
            <section class="content-header d-flex align-items-center justify-content-between">
                <div>
                    <h1 style="margin: 0; padding-bottom: 1rem"><?= $page_subtitle; ?></h1>
                    <ul class="breadcrumb" style="background: transparent;padding: 0;margin-left: 0.5rem;">
                        <li><a href="dashboard.php"><i class="fa fa-dashboard fs-4"></i> Dashboard</a></li>
                        <li class="active"><?= $page_title; ?></li>
                    </ul>
                </div>
                <div>
                    <button type="submit" name="Submit" class="btn btn-success" id='saveButton'><i class="fa fa-floppy-o"
                            aria-hidden="true fs-4"></i></button>
                </div>
            </section>

            <section class="content">
                <div class="row">
                    <!-- Main content -->
                    <section class="col-lg-9">
                        <div class="box box-info">
                            <div class="box-body">
                                <?php include_once("components/alert-msg.php"); ?>

                                <h3>Contact & Identity</h3>

                                <div class="row">
                                    <div class="form-group col-lg-12">
                                        <div class="form-floating">
                                            <input type="text" value="<?= h($data['name']) ?>" class="form-control" id="name"
                                                name="name" placeholder="Name" required>
                                            <label for="name">Name <span class="text-danger">*</span></label>
                                        </div>
                                    </div>
                                    <div class="form-group col-lg-6">
                                        <div class="form-floating">
                                            <input type="email" value="<?= h($data['email_1']) ?>" name="email_1"
                                                class="form-control" id="email1" placeholder="Email Address 1">
                                            <label for="email1">Email Address 1</label>
                                        </div>
                                    </div>

                                    <div class="form-group col-lg-6">
                                        <div class="form-floating">
                                            <input type="email" value="<?= h($data['email_2']) ?>" name="email_2"
                                                class="form-control" id="email2" placeholder="Email Address 2">
                                            <label for="email2">Email Address 2</label>
                                        </div>
                                    </div>

                                    <div class="form-group col-lg-6">
                                        <div class="form-floating">
                                            <input type="tel" value="<?= h($data['phone_1']) ?>" name="phone_1"
                                                class="form-control" id="phone1" placeholder="Phone Number 1">
                                            <label for="phone1">Phone Number 1</label>
                                        </div>
                                    </div>

                                    <div class="form-group col-lg-6">
                                        <div class="form-floating">
                                            <input type="tel" value="<?= h($data['phone_2']) ?>" name="phone_2"
                                                class="form-control" id="phone2" placeholder="Phone Number 2">
                                            <label for="phone2">Phone Number 2</label>
                                        </div>
                                    </div>

                                </div>

                                <div class="form-group">
                                    <label for="settings-address">Address</label>
                                    <textarea class="form-control" id="settings-address" name="address" cols="45"
                                        rows="3"><?= h($data['address']) ?></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="settings-google-map">Google Map Iframe</label>
                                    <textarea class="form-control" id="settings-google-map" name="google_map" cols="45"
                                        rows="5"><?= h($data['google_map']) ?></textarea>
                                </div>

                                <h3 class="pt-3">Currency options</h3>
                                <div class="row">
                                    <div class="form-group col-lg-12">
                                        <div class="form-floating">
                                            <select class="form-select" id="select_currency" name="currency"></select>
                                            <label for="select_currency">Currency</label>
                                        </div>
                                    </div>

                                    <div class="form-group col-lg-6">
                                        <div class="form-floating">
                                            <input type="text" value="<?= h($data['thousand_separator']) ?>"
                                                name="thousand_separator" class="form-control" id="thousand_separator"
                                                placeholder="Thousand separator">
                                            <label for="thousand_separator">Thousand separator</label>
                                        </div>
                                    </div>

                                    <div class="form-group col-lg-6">
                                        <div class="form-floating">
                                            <input type="text" value="<?= h($data['decimal_separator']) ?>"
                                                name="decimal_separator" class="form-control" id="decimal_separator"
                                                placeholder="Decimal separator">
                                            <label for="decimal_separator">Decimal separator</label>
                                        </div>
                                    </div>
                                </div>

                                <h4 class="pt-3">Tax Percentage</h4>
                                <div class="form-group">
                                    <div class="form-floating">
                                        <input type="number" value="<?= h($data['tax']) ?>" name="tax" class="form-control"
                                            id="tax" placeholder="Tax Amount">
                                        <label for="tax">Tax Amount</label>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </section>
                    <section class="col-lg-3">
                        <div class="box box-info">
                            <div class="box-body">
                                <div class="form-group py-2">
                                    <label>Logo<span style="color:red;">*</span></label><br>
                                    <?php
                                    if (empty($data['logo']) !== true) {
                                        echo "<img src='$media_folder{$data['logo']}' class='full-width' id='feature-image-img'>";
                                    } ?>
                                    <input type="hidden" name="logo" value="<?= h($data['logo']) ?>" id='feature-image-input'>
                                    <button type="button" class="btn btn-secondary btn-sm mt-2 openMediaModal"
                                        data-bs-toggle="modal" data-bs-target="#mediaSelectionModal" data-type="image"
                                        data-selection="single" group-id='feature-image'>
                                        Select Image
                                    </button>
                                </div>

                                <div class="accordion" id="accordionExample">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingOne">
                                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#collapseOne" aria-expanded="true"
                                                aria-controls="collapseOne">
                                                Social Media URLs
                                            </button>
                                        </h2>
                                        <div id="collapseOne" class="accordion-collapse collapse show"
                                            aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                            <div class="accordion-body">
                                                <div class="form-group mb-3">
                                                    <div class="form-floating w-100">
                                                        <input type="url" value="<?= h($data['facebook']) ?>" name="facebook"
                                                            class="form-control" id="facebookURL" placeholder="Facebook">
                                                        <label for="facebookURL" class="d-flex align-items-center gap-2"><i
                                                                class="fa fa-facebook-square fs-4"></i> Facebook</label>
                                                    </div>
                                                </div>

                                                <div class="form-group mb-3">
                                                    <div class="form-floating w-100">
                                                        <input type="url" value="<?= h($data['twitter']) ?>" name="twitter"
                                                            class="form-control" id="twitterURL" placeholder="Twitter">
                                                        <label for="twitterURL" class="d-flex align-items-center gap-2"><i
                                                                class="fa fa-twitter-square fs-4"></i> Twitter</label>
                                                    </div>
                                                </div>

                                                <div class="form-group mb-3">
                                                    <div class="form-floating w-100">
                                                        <input type="url" value="<?= h($data['instagram']) ?>"
                                                            name="instagram" class="form-control" id="instagramURL"
                                                            placeholder="Instagram">
                                                        <label for="instagramURL" class="d-flex align-items-center gap-2"><i
                                                                class="fa fa-instagram fs-4"></i> Instagram</label>
                                                    </div>
                                                </div>

                                                <div class="form-group mb-3">
                                                    <div class="form-floating w-100">
                                                        <input type="url" value="<?= h($data['linkedin']) ?>" name="linkedin"
                                                            class="form-control" id="linkedinURL" placeholder="LinkedIn">
                                                        <label for="linkedinURL" class="d-flex align-items-center gap-2"><i
                                                                class="fa fa-linkedin-square fs-4"></i> LinkedIn</label>
                                                    </div>
                                                </div>

                                                <div class="form-group mb-3">
                                                    <div class="form-floating w-100">
                                                        <input type="url" value="<?= h($data['youtube']) ?>" name="youtube"
                                                            class="form-control" id="youtubeURL" placeholder="YouTube">
                                                        <label for="youtubeURL" class="d-flex align-items-center gap-2"><i
                                                                class="fa fa-youtube-square fs-4"></i> YouTube</label>
                                                    </div>
                                                </div>

                                                <div class="form-group mb-3">
                                                    <div class="form-floating w-100">
                                                        <input type="url" value="<?= h($data['tiktok'] ?? '') ?>" name="tiktok"
                                                            class="form-control" id="tiktokURL" placeholder="TikTok">
                                                        <label for="tiktokURL" class="d-flex align-items-center gap-2">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-tiktok" viewBox="0 0 16 16">
                                                                <path d="M9 0h1.98c.144.715.54 1.617 1.235 2.512C12.895 3.389 13.797 4 15 4v2c-1.753 0-3.07-.814-4-1.829V11a5 5 0 1 1-5-5v2a3 3 0 1 0 3 3z" />
                                                            </svg>
                                                            TikTok
                                                        </label>
                                                    </div>
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
    <script src="assets/js/select-currency.js"></script>
    <?php if (!empty($data['currency'])): ?>
        <script>
            selectOption("<?= h($data['currency']) ?>");
        </script>
    <?php endif; ?>

    <?php include("components/media-gallery-modal.php"); ?>
<?php include("components/footer.php");
} ?>
