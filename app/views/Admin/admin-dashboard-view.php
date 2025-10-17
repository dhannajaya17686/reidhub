<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>
        <?php $a = $admin ?? []; echo 'ReidHub | ' . htmlspecialchars(($a['first_name'] ?? 'Admin') . ' Dashboard'); ?>
    </title>
</head>
<body>
    <h1>Welcome, <?php echo htmlspecialchars(($a['first_name'] ?? '') . ' ' . ($a['last_name'] ?? '')); ?> (Admin)</h1>
    <p>Email: <?php echo htmlspecialchars($a['email'] ?? ''); ?></p>
    <p>Admin ID: <?php echo htmlspecialchars($a['id'] ?? ''); ?></p>
</body>
</html>
