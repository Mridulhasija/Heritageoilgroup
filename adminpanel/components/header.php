<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Admin | <?php echo $website_title; ?></title>
  <meta name="robots" content="noindex, nofollow">
  <meta content="width=device-width, initial-scale=1, initial-scale=1" name="viewport">
  <!-- bootstrap -->
  <link rel="stylesheet" href="plugins/bootstrap/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/admin-shell.css">
  <!--jquery-->
  <script src="plugins/jquery/jquery-3.7.1.min.js"></script>
  <script src="plugins/jquery/jquery-ui.js"></script>
  <!--bootstrap-->
  <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
</head>

<body class="admin-body">
  <div class="wrapper admin-shell">
    <header class="admin-mobile-bar">
      <a class="admin-mobile-brand" href="dashboard.php" aria-label="Go to dashboard">
        <strong><?= h($website_title) ?></strong>
        <small>Administration</small>
      </a>
      <button class="admin-menu-button" type="button" aria-label="Open navigation" aria-controls="admin-navigation" aria-expanded="false">
        <i class="fa fa-bars" aria-hidden="true"></i>
        <span class="admin-menu-button-label">Menu</span>
      </button>
    </header>

    <?php
    $menuItems = [
      [
        'title' => 'Dashboard',
        'icon' => 'fa-dashboard',
        'link' => 'dashboard.php',
      ],
      [
        'title' => 'Media',
        'icon' => 'fa-picture-o',
        'link' => 'media-gallery.php',
      ],
      [
        'title' => 'Blogs',
        'icon' => 'fa-edit',
        'submenu' => [
          ['title' => 'Blog Category', 'link' => 'blog-category.php'],
          ['title' => 'Manage Blogs', 'link' => 'blog.php'],
        ]
      ],
      [
        'title' => 'Pages',
        'icon' => 'fa-file-text-o',
        'submenu' => [
          ['title' => 'Add Page', 'link' => 'page-editor.php'],
          ['title' => 'All Pages', 'link' => 'page.php'],
        ]
      ],
      [
        'title' => 'Banner/Gallery',
        'icon' => 'fa-picture-o',
        'submenu' => [
          ['title' => 'Manage Banners', 'link' => 'banners.php'],
          ['title' => 'Manage Gallery', 'link' => 'gallery.php'],
        ]
      ],
      [
        'title' => 'Products',
        'icon' => 'fa-archive',
        'submenu' => [
          ['title' => 'Add New', 'link' => 'product-editor.php'],
          ['title' => 'All Product', 'link' => 'product.php'],
          ['title' => 'Category', 'link' => 'product-category.php'],
        ]
      ],
      [
        'title' => 'Users',
        'icon' => 'fa-users',
        'submenu' => [
          ['title' => 'All User', 'link' => 'user.php'],
          ['title' => 'Add New User', 'link' => 'user-editor.php'],
        ]
      ],
      [
        'title' => 'Enquiries',
        'icon' => 'fa-question-circle',
        'link' => 'enquiry.php',
      ],
      [
        'title' => 'Newsletter',
        'icon' => 'fa-newspaper-o',
        'link' => 'newsletter.php',
      ],
      [
        'title' => 'Testimonials',
        'icon' => 'fa-comment',
        'submenu' => [
          ['title' => 'Add Testimonial', 'link' => 'testimonial-editor.php'],
          ['title' => 'All Testimonial', 'link' => 'testimonial.php'],
        ]
      ],
      [
        'title' => 'Team Members',
        'icon' => 'fa-users',
        'submenu' => [
          ['title' => 'All Members', 'link' => 'member.php'],
          ['title' => 'Add Member', 'link' => 'member-editor.php'],
        ]
      ],
      [
        'title' => 'Service Areas',
        'icon' => 'fa-map-marker',
        'link' => 'sevice-location.php',
      ],
      [
        'title' => 'Settings',
        'icon' => 'fa-cog',
        'link' => 'settings.php',
      ],
    ];

    ?>
    <?php $currentPage = basename(parse_url($_SERVER['PHP_SELF'], PHP_URL_PATH)); ?>
    <aside id="admin-navigation" class="admin-sidebar" aria-label="Main navigation">
      <a class="admin-brand" href="dashboard.php"><strong>Admin Panel</strong><small><?= h($website_title) ?></small></a>
      <div class="admin-sidebar-label">Manage</div>
      <nav class="admin-nav" aria-label="Admin pages">
        <?php foreach ($menuItems as $item): ?>
          <?php if (isset($item['submenu'])):
            $active = in_array($currentPage, array_column($item['submenu'], 'link'), true); ?>
            <details class="admin-nav-group" <?= $active ? 'open' : '' ?>>
              <summary class="admin-nav-link <?= $active ? 'is-active' : '' ?>"><i class="fa <?= h($item['icon']) ?>" aria-hidden="true"></i><span><?= h($item['title']) ?></span><i class="fa fa-angle-down admin-nav-chevron" aria-hidden="true"></i></summary>
              <div class="admin-subnav">
                <?php foreach ($item['submenu'] as $sub): ?>
                  <a href="<?= h($sub['link']) ?>" <?= $currentPage === $sub['link'] ? 'aria-current="page"' : '' ?>><?= h($sub['title']) ?></a>
                <?php endforeach; ?>
              </div>
            </details>
          <?php else: ?>
            <a class="admin-nav-link <?= $currentPage === $item['link'] ? 'is-active' : '' ?>" href="<?= h($item['link']) ?>" <?= $currentPage === $item['link'] ? 'aria-current="page"' : '' ?>><i class="fa <?= h($item['icon']) ?>" aria-hidden="true"></i><span><?= h($item['title']) ?></span></a>
          <?php endif; ?>
        <?php endforeach; ?>
      </nav>
      <div class="admin-sidebar-footer">
        <div class="admin-account dropup">
          <button class="admin-account-button" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            <span class="admin-avatar" aria-hidden="true"><?= h(strtoupper(substr((string) $user_name, 0, 1))) ?></span>
            <span class="admin-account-name"><?= h($user_name) ?></span>
            <i class="fa fa-angle-up" aria-hidden="true"></i>
          </button>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="user-editor.php?action=edit&amp;id=<?= urlencode((string) $_SESSION['user_id']) ?>">Profile</a></li>
            <li><a class="dropdown-item" href="logout.php">Sign out</a></li>
          </ul>
        </div>
      </div>
    </aside>
    <button class="admin-sidebar-backdrop" type="button" aria-label="Close navigation" tabindex="-1"></button>