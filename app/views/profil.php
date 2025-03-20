<?php require_once dirname(__DIR__) . '/templates/head.php'; ?>

<body>

    <?php require_once dirname(__DIR__) . '/templates/headerhome.php'; ?>

    <main>

    <p>Username :<span class="username"><?= htmlspecialchars($user['username']) ?></span></p>
    <p>Email :<span class="username"><?= htmlspecialchars($user['email']) ?></span></p>
    <p>Compte créé le :<span class="username"><?= ($user['created_at']) ?></span></p>

    </main>

    <?php require_once dirname(__DIR__) . '/templates/footer.php'; ?>

</body>

</html>