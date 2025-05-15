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
                        <img src="${image.image_url}" alt="Image de la galerie">
                    </a>
                    <button class="delete-btn" data-image-id="${image.id}">Supprimer</button>
                </div>
            `).join("");
        })
        .catch(error => {
            console.error("Erreur lors du chargement des images:", error);
            galleryContainer.innerHTML = "<p>Impossible de charger les images.</p>";
        });

    galleryContainer.addEventListener("click", function (e) {
        if (e.target.classList.contains("delete-btn")) {
            const imageIdToDelete = e.target.dataset.imageId;

            const confirmation = confirm("Êtes-vous sûr de vouloir supprimer cette image ?");
            if (!confirmation) return;

            fetch('/controllers/profil.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    delete: true,
                    image_id: imageIdToDelete
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
                        <button class="edit-email-btn">Modifier l'email</button>
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

            const editEmailBtn = document.querySelector(".edit-email-btn");
            editEmailBtn.addEventListener("click", () => {
                openUpdateEmailForm(user.username);
            });
        })
        .catch(error => console.error("Erreur:", error));

        function hideallForms() {
            document.getElementById("updateUsername").style.display = "none";
            document.getElementById("updatePassword").style.display = "none";
            document.getElementById("updateEmail").style.display = "none";
        }

        function openUpdatePasswordForm() {
            hideallForms();
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
            hideallForms();
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

        function openUpdateEmailForm() {
            hideallForms();
            const updateEmailForm = document.getElementById("updateEmail");
            updateEmailForm.style.display = "block";
            const cancelButton = document.getElementById("cancelUpdateEmail");
            cancelButton.addEventListener("click", () => {
                updateEmailForm.style.display = "none";
            });

            const updateFormSubmit = document.getElementById("updateEmailForm");
            updateFormSubmit.addEventListener("submit", async function () {
                // e.preventDefault();
                const email = document.getElementById("email").value.trim();
                const body = {
                    email: email
                };
            });
        }

        const toggleComMailButton = document.querySelector(".toggle_com_mail");

    if (toggleComMailButton) {
        toggleComMailButton.addEventListener("click", () => {
            fetch('/controllers/profil.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'com_mail=toggle'
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error("Erreur lors de la mise à jour des notifications par e-mail");
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    toggleComMailButton.setAttribute("data-com-mail", data.new_com_mail);
                    toggleComMailButton.textContent = parseInt(data.new_com_mail) === 1 ? "Désactiver les notifications par e-mail" : "Activer les notifications par e-mail";
                } else if (data.error) {
                    alert("Erreur : " + data.error);
                }
            })
            .catch(error => {
                console.error("Erreur:", error);
                alert("Une erreur s'est produite lors de la mise à jour.");
            });
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
            if (user && typeof user.com_mail !== 'undefined') {
                toggleComMailButton.setAttribute("data-com-mail", user.com_mail);
                toggleComMailButton.textContent = user.com_mail === 1 ? "Désactiver les notifications par e-mail" : "Activer les notifications par e-mail";
            }
        })
        .catch(error => console.error("Erreur:", error));
    }
        
});


function formatDate(dateString) {
    const options = { day: "numeric", month: "long", year: "numeric", hour: "2-digit", minute: "2-digit" };
    return new Date(dateString).toLocaleDateString("fr-FR", options);
}

function escapeHTML(str) {
    return str.replace(/</g, "&lt;").replace(/>/g, "&gt;");
}