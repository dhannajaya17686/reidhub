<!DOCTYPE html>
<html>
<head>
    <title><?= $title ?? 'ReidHub' ?></title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/app/globals.css">
    <link rel="stylesheet" href="/css/app/user/layout.css">
    <link rel="stylesheet" href="/css/app/components/header.css">
    <link rel="stylesheet" href="/css/app/components/sidebar.css">
     <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- ...other head elements... -->
</head>
<body>
    <div class="app-shell">
        <?php include __DIR__ . '/../components/sidebar.php'; ?>
        <?php include __DIR__ . '/../components/header.php'; ?>
        <main class="forum-main">
            <?php
            if (isset($content)) {
                echo $content;
            }
            ?>
        </main>
    </div>
    <!-- Sidebar overlay for mobile -->
    <div class="sidebar-overlay" data-sidebar-overlay aria-hidden="true"></div>
    <!-- Scripts -->
    <script type="module" src="/js/app/edu-forum/all-questions.js"></script>
</body>
</html>