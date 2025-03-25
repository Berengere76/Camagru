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
                    <li><a href="/controllers/home.php">Accueil</a></li>
                    <li><a href="/controllers/galerie.php">Galerie</a></li>
                    <li><a href="/controllers/profil.php">Profil</a></li>
                    <li><a href="/controllers/logout.php">Se déconnecter</a></li>
                </ul>
            </nav>
        </div>
    </header>


    <main>

    <div class="gallery">
        <?php foreach ($images as $image): ?>
            <div class="gallery-item">
                <img src="/<?= htmlspecialchars($image['image_url']) ?>" alt="Image de la galerie">
                <p>Photo prise par : <span class="username"><?= htmlspecialchars($image['username']) ?></span></p>
                <p><?= ($image['created_at']) ?></p>
            </div>
        <?php endforeach; ?>
    </div>
     
    </main>


    <footer>
        <p>© Camagru 2025</p>
    </footer>


</body>

</html>
