<?php require_once dirname(__DIR__) . '/templates/head.php'; ?>

<body>

    <?php require_once dirname(__DIR__) . '/templates/headerhome.php'; ?>

    <main>

    <?php 
    if (isset($_SESSION['username'])) {
        echo '<h1 class="test">Profil de ' . $_SESSION['username'] . '</h1>';
    }
    ?>
     
    </main>

    <?php require_once dirname(__DIR__) . '/templates/footer.php'; ?>

</body>

</html>