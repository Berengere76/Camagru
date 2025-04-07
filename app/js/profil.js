document.addEventListener("DOMContentLoaded", () => {

    const galleryContainer = document.getElementById("gallery");

	fetch('/controllers/profil.php?imageid', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json'
        }
    })
        .then(response => response.json())
        .then(images => {
            if (images.error) {
                galleryContainer.innerHTML = `<p>${images.error}</p>`;
                return;
            }

            galleryContainer.innerHTML = images.map(image => `
                <div class="gallery-item">
                    <img src="/${image.image_url}" alt="Image de la galerie">
                    <button class="delete-btn" data-url="${image.image_url}">Supprimer</button>
                </div>
            `).join("");
        })
        .catch(error => {
            console.error("Erreur lors du chargement des images:", error);
            galleryContainer.innerHTML = "<p>Impossible de charger les images.</p>";
        });

        galleryContainer.addEventListener("click", function(e) {
            if (e.target.classList.contains("delete-btn")) {
                const imageUrl = e.target.dataset.url;

                const confirmation = confirm("Êtes-vous sûr de vouloir supprimer cette image ?");
                if (!confirmation) return;
        
                fetch('/controllers/profil.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        action: 'delete',
                        image_url: imageUrl
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        e.target.parentElement.remove();
                    } else {
                        alert("Erreur : " + data.error);
                    }
                })
                .catch(error => {
                    console.error("Erreur de suppression :", error);
                });
            }
        });        

    fetch('/controllers/profil.php?test', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json'
        }
    })
        .then(response => {
            if (!response.ok) {
                throw new Error("Erreur de chargement du profil");
            }
            return response.json();
        })
        .then(user => {
            if (user.error) {
                console.error(user.error);
                return;
            }

            const formattedDate = formatDate(user.created_at);
            const userr = document.getElementById("userr");
            userr.innerHTML = `
                <div class="user-profile-card">
                    <h2>Profil de ${user.username}</h2>
                    <div class="user-details">
                        <p><strong>Email :</strong> ${user.email}</p>
                        <p><strong>Inscrit depuis :</strong> ${formattedDate}</p>
                    </div>
                    <div class="user-actions">
                        <button class="edit-username-btn">Modifier le nom d'utilisateur</button>
                    </div>
                </div>
            `;
        })
        .catch(error => console.error("Erreur:", error));
});

function formatDate(dateString) {
    const options = { day: "numeric", month: "long", year: "numeric", hour: "2-digit", minute: "2-digit" };
    return new Date(dateString).toLocaleDateString("fr-FR", options);
}

function escapeHTML(str) {
    return str.replace(/</g, "&lt;").replace(/>/g, "&gt;");
}
