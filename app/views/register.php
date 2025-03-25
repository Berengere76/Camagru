<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page d'Accueil</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;700&display=swap" rel="stylesheet">
</head>


<body>


    <header>
        <div class="header">
            <div>
                <img class="logo" src="/images/logo.png" alt="Logo Camagru">
            </div>
            <nav>
                <ul>
                    <li><a href="/controllers/login.php">Connexion</a></li>
                    <li><a href="/controllers/register.php">Inscription</a></li>
                </ul>
            </nav>
        </div>
    </header>


    <main>
        <div class="home">
            <h1>Inscription</h1>

            <?php if (!empty($errors)): ?>
                <div class="error-message"><?= htmlspecialchars($errors[0] ?? "") ?></div>
            <?php endif; ?>
            <script>
                setTimeout(() => {
                    document.querySelector('.error-message').style.display = 'none';
                }, 5000);
            </script>

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


    <footer>
        <p>© Camagru 2025</p>
    </footer>


</body>

</html>