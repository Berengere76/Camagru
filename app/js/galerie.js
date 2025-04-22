document.addEventListener("DOMContentLoaded", function () {
    fetch('/controllers/galerie.php?ajax', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json'
        }
    })
        .then(response => response.json())
        .then(images => {
            if (images.error) {
                document.querySelector(".gallery").innerHTML = `<p>${images.error}</p>`;
                return;
            }
            const galleryContainer = document.querySelector(".gallery");
            galleryContainer.innerHTML = images.map(image => `
                <div class="gallery-item">
                    <a href="/controllers/image.php?imageid=${image.id}">
                        <img src="/${image.image_url}" alt="Image de la galerie">
                    </a>
                    <div class="comments-likes">
                        <div class="likes-section">
                            <span class="count">${image.like_count}</span>
                            <img src="/images/like.png" alt="Likes" class="logo">
                        </div>
                        <div class="comments-section">
                            <span class="count">${image.comment_count}</span>
                            <img src="/images/comment.png" alt="Comments" class="logo">
                        </div>
                    </div>
                    <div class="image-info">
                        <span class="username">${image.username}</span>
                        <span class="photo-date">${timeAgo(image.created_at)}</span>
                    </div>
                </div>
            `).join("");
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