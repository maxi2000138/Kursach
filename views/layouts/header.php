<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'User Management System' ?></title>
    <link rel="stylesheet" href="/public/css/style.css">
</head>
<body>
    <?php if (isAuthenticated()): ?>
        <?php require __DIR__ . '/navigation.php'; ?>
    <?php endif; ?>
    
    <main class="container">
        <?php
        $success = flash('success');
        $error = flash('error');
        $warning = flash('warning');
        $info = flash('info');
        ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <?php if ($warning): ?>
            <div class="alert alert-warning"><?= htmlspecialchars($warning) ?></div>
        <?php endif; ?>
        
        <?php if ($info): ?>
            <div class="alert alert-info"><?= htmlspecialchars($info) ?></div>
        <?php endif; ?>

