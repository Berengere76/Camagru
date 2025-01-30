<?php require_once dirname(__DIR__) . '/templates/head.php'; ?>

<body>

    <?php require_once dirname(__DIR__) . '/templates/header.php'; ?>

    <main>
        <div class="home">
            <h1>Inscription</h1>
            <form action="/controllers/register.php" method="post">
                <input type="text" name="username" placeholder="Nom d'utilisateur" required>
                <input type="email" name="email" placeholder="Adresse e-mail" required>
                <input type="password" name="password" placeholder="Mot de passe" required>
                <button type="submit" name="register">S'inscrire</button>
            </form>
            <p>Déjà un compte ? <a href="/controllers/login.php">Connectez-vous</a></p>
        </div>
    </main>

    <div class="clouds">
        <img src="../images/cloud1.png" alt="Nuage 1" class="cloud cloud1">
        <img src="../images/cloud1.png" alt="Nuage 2" class="cloud cloud2">
        <img src="../images/cloud1.png" alt="Nuage 3" class="cloud cloud3">
    </div>

    <div class="star star1"></div>
    <div class="star star2"></div>
    <div class="star star3"></div>
    <div class="star star4"></div>
    <div class="star star5"></div>
    <div class="star star6"></div>
    <div class="star star7"></div>
    <div class="star star8"></div>
    <div class="star star9"></div>
    <div class="star star10"></div>

    <div class="sky">
        <div class="shooting-star"></div>
        <div class="shooting-star"></div>
        <div class="shooting-star"></div>
    </div>

    <?php require_once dirname(__DIR__) . '/templates/footer.php'; ?>

</body>

</html>