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
                    <a href="/controllers/image.php?imageid=${image.id}">
                    <img src="/${image.image_url}" alt="Image de la galerie">
                    </a>
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
            const userProfilCard = document.getElementById("user-profil-card");
            userProfilCard.innerHTML = `
                    <h2>Profil de ${user.username}</h2>
                    <div class="user-details">
                        <p><strong>Email</strong> ${user.email}</p>
                        <p><strong>Inscrit depuis le</strong> ${formattedDate}</p>
                    </div>
                    <div class="user-actions">
                        <button class="edit-username-btn">Modifier le nom d'utilisateur</button>
                        <button class="edit-password-btn">Modifier le mot de passe</button>
                    </div>
                
            `;

            const editUsernameBtn = document.querySelector(".edit-username-btn");
            editUsernameBtn.addEventListener("click", () => {
                openUpdateUsernameForm(user.username);
            });

            const editPasswordBtn = document.querySelector(".edit-password-btn");
            editPasswordBtn.addEventListener("click", () => {
                openUpdatePasswordForm(user.username);
            });
        })
        .catch(error => console.error("Erreur:", error));

        function openUpdatePasswordForm() {
            const updatePasswordForm = document.getElementById("updatePassword");
            updatePasswordForm.style.display = "block";
            const cancelButton = document.getElementById("cancelUpdatePassword");
            cancelButton.addEventListener("click", () => {
                updatePasswordForm.style.display = "none";
            });
        
            const updateFormSubmit = document.getElementById("updatePasswordForm");
            updateFormSubmit.addEventListener("submit", async function () {
                const password = document.getElementById("password").value.trim();
                const newPassword = document.getElementById("new_password").value.trim();
                const body = {
                    password: password,
                    new_password: newPassword
                };
            });
        }
        
        function openUpdateUsernameForm() {
            const updateUsernameForm = document.getElementById("updateUsername");
            updateUsernameForm.style.display = "block";
            const cancelButton = document.getElementById("cancelUpdate");
            cancelButton.addEventListener("click", () => {
                updateUsernameForm.style.display = "none";
            });
        
            const updateFormSubmit = document.getElementById("updateUsernameForm");
            updateFormSubmit.addEventListener("submit", async function () {
                if (user.username === username) {
                    alert("Le nom d'utilisateur est identique à l'ancien.");
                    return;
                }
                const username = document.getElementById("username").value.trim();
                const body = {
                    username: username
                };
            });
        }        
});


function formatDate(dateString) {
    const options = { day: "numeric", month: "long", year: "numeric", hour: "2-digit", minute: "2-digit" };
    return new Date(dateString).toLocaleDateString("fr-FR", options);
}


function escapeHTML(str) {
    return str.replace(/</g, "&lt;").replace(/>/g, "&gt;");
}
