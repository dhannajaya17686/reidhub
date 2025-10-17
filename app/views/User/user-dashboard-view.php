<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>
        <?php $u = $user ?? []; echo 'ReidHub | ' . htmlspecialchars(($u['first_name'] ?? 'User') . ' Dashboard'); ?>
    </title>
</head>
<body>
    <h1>Welcome Students Union !</h1>
    <p>Email: <?php echo htmlspecialchars($u['email'] ?? ''); ?></p>
    <p>Reg No: <?php echo htmlspecialchars($u['reg_no'] ?? ''); ?></p>
</body>
</html>