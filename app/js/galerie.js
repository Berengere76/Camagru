// document.addEventListener("DOMContentLoaded", () => {
//     const galleryContainer = document.getElementById("gallery");

//     fetch("/controllers/get_images.php")
//         .then(response => response.json())
//         .then(images => {
//             if (images.error) {
//                 galleryContainer.innerHTML = `<p>${images.error}</p>`;
//                 return;
//             }

//             galleryContainer.innerHTML = images.map(image => `
//                 <div class="gallery-item">
//                     <img src="/${image.image_url}" alt="Image de la galerie">
//                     <p>Photo prise par : <span class="username">${escapeHTML(image.username)}</span></p>
//                     <p>${image.created_at}</p>
//                 </div>
//             `).join("");
//         })
//         .catch(error => {
//             console.error("Erreur lors du chargement des images:", error);
//             galleryContainer.innerHTML = "<p>Impossible de charger les images.</p>";
//         });
// });

// function escapeHTML(str) {
//     return str.replace(/</g, "&lt;").replace(/>/g, "&gt;");
// }


document.addEventListener("DOMContentLoaded", function () {
    fetch('/controllers/get_images.php')
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
