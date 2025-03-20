<?php require_once dirname(__DIR__) . '/templates/head.php'; ?>

<body>

    <?php require_once dirname(__DIR__) . '/templates/headerhome.php'; ?>

    <main>

    <div class="gallery">
        <?php foreach ($images as $image): ?>
            <div class="gallery-item">
                <img src="/<?= htmlspecialchars($image['image_url']) ?>" alt="Image de la galerie">
                <p>Photo prise par : <span class="username"><?= htmlspecialchars($image['username']) ?></span></p>
            </div>
        <?php endforeach; ?>
    </div>
     
    </main>

    <?php require_once dirname(__DIR__) . '/templates/footer.php'; ?>

</body>

</html>
