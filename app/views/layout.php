<!DOCTYPE html>
<html>
<head>
    <title><?= $title ?? 'ReidHub' ?></title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/app/globals.css">
    <link rel="stylesheet" href="/css/app/layout.css">
    <link rel="stylesheet" href="/css/app/components/header.css">
    <link rel="stylesheet" href="/css/app/components/sidebar.css">
     <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
        <!-- Material Symbols (Google icons) -->
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,400,0,0" rel="stylesheet">
        <style>
        .report-icon {
            font-family: 'Material Symbols Outlined';
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 20;
            font-size: 18px;
            width: 34px;
            height: 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            border: none;
            background: transparent;
            cursor: pointer;
            color: inherit;
            padding: 0;
        }
        .report-icon:hover { background: rgba(0,0,0,0.05); }
        </style>
        <!-- ...other head elements... -->
</head>
<body>
    <div class="app-shell">
        <?php include __DIR__ . '/components/sidebar.php'; ?>
        <?php include __DIR__ . '/components/header.php'; ?>
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
    <script type="module" src="/js/app/components/sidebar.js"></script>
</body>
</html>