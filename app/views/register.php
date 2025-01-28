
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
        <h1>Inscription</h1>
        <form action="/register" method="POST">
            <input type="text" name="username" placeholder="Nom d'utilisateur" required>
            <input type="email" name="email" placeholder="Adresse e-mail" required>
            <input type="password" name="password" placeholder="Mot de passe" required>
             <button type="submit">S'inscrire</button>
        </form>
        <p>Déjà un compte ? <a href="/views/home.php">Connectez-vous</a></p>
    </header>

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

</body>
</html>
