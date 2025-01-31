<?php require_once dirname(__DIR__) . '/templates/head.php'; ?>

<body>

    <?php require_once dirname(__DIR__) . '/templates/headerhome.php'; ?>

    <main>

    <?php 
    if (isset($_SESSION['username'])) {
        echo '<h1 class="test">Bienvenue sur Camagru ' . $_SESSION['username'] . ' !</h1>';
    }
    ?>

    <div class="camera-container">
        <video id="video" autoplay></video>
        <button id="capture" class="capture-btn">Prendre une photo</button>
    </div>

    <div class=photo-container>
        <canvas id="canvas"></canvas>
    </div>

    <script src="/js/webcam.js" defer></script>

    </main>

    <?php require_once dirname(__DIR__) . '/templates/footer.php'; ?>

</body>

</html>