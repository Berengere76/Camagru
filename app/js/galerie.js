document.addEventListener("DOMContentLoaded", function () {
    fetch('/controllers/galerie.php?ajax', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json'
        }
    })
        .then(response => response.json())
        .then(images => {
            const galleryContainer = document.querySelector(".gallery");

            images.forEach(image => {
                const galleryItem = document.createElement("div");
                galleryItem.classList.add("gallery-item");

                const img = document.createElement("img");
                img.src = `/${image.image_url}`;
                img.alt = "Image de la galerie";

                const usernameText = document.createElement("span");
				usernameText.classList.add("photo-info");
                usernameText.textContent = "Photo prise par : ";

                const usernameSpan = document.createElement("span");
                usernameSpan.classList.add("username");
                usernameSpan.textContent = image.username;

                const dateText = document.createElement("span");
				dateText.classList.add("photo-date");
                dateText.textContent = timeAgo(image.created_at);

                usernameText.appendChild(usernameSpan);
                galleryItem.appendChild(img);
                galleryItem.appendChild(usernameText);
                galleryItem.appendChild(dateText);
                galleryContainer.appendChild(galleryItem);
            });
        })
        .catch(error => {
            console.error("Erreur lors du chargement des images :", error);
            document.querySelector(".gallery").innerHTML = "<p>Impossible de charger les images.</p>";
        });
});

function timeAgo(dateString) {
    const now = new Date();
    const date = new Date(dateString);
    const diffMs = now - date;
    const diffSec = Math.floor(diffMs / 1000);
    const diffMin = Math.floor(diffSec / 60);
    const diffHrs = Math.floor(diffMin / 60);
    const diffDays = Math.floor(diffHrs / 24);

    if (diffDays > 0) return `il y a ${diffDays} jour${diffDays > 1 ? "s" : ""}`;
    if (diffHrs > 0) return `il y a ${diffHrs} heure${diffHrs > 1 ? "s" : ""}`;
    if (diffMin > 0) return `il y a ${diffMin} minute${diffMin > 1 ? "s" : ""}`;
    return "à l'instant";
}

function formatDate(dateString) {
    const options = { day: "numeric", month: "long", year: "numeric", hour: "2-digit", minute: "2-digit" };
    return new Date(dateString).toLocaleDateString("fr-FR", options);
}
