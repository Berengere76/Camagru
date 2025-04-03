document.addEventListener("DOMContentLoaded", function () {
    fetch('/controllers/get_profil.php')
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

            const main = document.querySelector("main");

            const userContainer = document.createElement("div");
            userContainer.classList.add("user-info");
            main.appendChild(userContainer);

			const formattedDate = formatDate(user.created_at);

            userContainer.innerHTML = `
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