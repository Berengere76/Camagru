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

    galleryContainer.addEventListener("click", function (e) {
        if (e.target.classList.contains("delete-btn")) {
            const imageUrl = e.target.dataset.url;

            const confirmation = confirm("Êtes-vous sûr de vouloir supprimer cette image ?");
            if (!confirmation) return;

            fetch('/controllers/profil.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    delete: true,
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

    fetch('/controllers/profil.php?info_profil', {
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
                        <p><strong>Email</strong> ${user.email}</p>
                        <p><strong>Inscrit depuis le</strong> ${formattedDate}</p>
                    </div>
                    <div class="user-actions">
                        <button class="edit-username-btn">Modifier le profil</button>
                    </div>
                </div>
            `;

            const editUsernameBtn = document.querySelector(".edit-username-btn");
            editUsernameBtn.addEventListener("click", () => {
                openUpdateForm(user.username);
            });
        })
        .catch(error => console.error("Erreur:", error));

    function openUpdateForm() {
        const formHTML = `
            <div id="updateForm">
                <h3>Modifier votre profil</h3>
                <form id="updateProfileForm" method="POST" action="/controllers/profil.php">
                    <input type="text" id="username" name="username" placeholder="Nouveau nom d'utilisateur">
                    <input type="password" id="password" name="password" placeholder="Mot de passe actuel">
                    <input type="password" id="new_password" name="new_password" placeholder="Nouveau mot de passe">
                    <button type="submit" name="update" class="btn btn-primary">Mettre à jour</button>
                    <button type="button" class="btn btn-secondary" id="cancelUpdate">Annuler</button>
                </form>
            </div>
        `;

        const userrContainer = document.getElementById("userr");
        userrContainer.innerHTML += formHTML;

        const cancelButton = document.getElementById("cancelUpdate");
        cancelButton.addEventListener("click", () => {
            document.getElementById("updateForm").style.display = 'none';
        });

        const updateFormSubmit = document.getElementById("updateProfileForm");
        updateFormSubmit.addEventListener("submit", async function (event) {

            const username = document.getElementById("username").value.trim();
            const password = document.getElementById("password").value.trim();
            const newPassword = document.getElementById("new_password").value.trim();
            const body = {
                username: username,
                password: password,
                new_password: newPassword
            };        
        })
    }
});


function formatDate(dateString) {
    const options = { day: "numeric", month: "long", year: "numeric", hour: "2-digit", minute: "2-digit" };
    return new Date(dateString).toLocaleDateString("fr-FR", options);
}


function escapeHTML(str) {
    return str.replace(/</g, "&lt;").replace(/>/g, "&gt;");
}
