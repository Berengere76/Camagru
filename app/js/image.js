document.addEventListener("DOMContentLoaded", function () {
	const imageId = new URLSearchParams(window.location.search).get("imageid");
	fetch(`/controllers/image.php?imageid=${imageId}&ajax=1`, {
		method: 'GET',
		headers: {
			'Content-Type': 'application/json'
		}
	})
		.then(response => response.json())
		.then(image => {
			if (image.error) {
				document.querySelector(".gallery").innerHTML = `<p>${image.error}</p>`;
				return;
			}
			const galleryContainer = document.querySelector(".gallery");

			galleryContainer.innerHTML = `
                <div class="container-image">
					<div class="image-preview">
						<img src="/${image.image_url}" alt="Image de la galerie">
					</div>
					<div class="photo-meta">
       					<span>Ajoutée le :  ${formatDate(image.created_at)}</span>
        				<span>Par : <strong>${image.username}</strong></span>
   					</div>
					<div class="likes-comments">
						<div class="like-section">
							❤️ 15 likes
						</div>

						<div class="comment-section">
							<h3>Commentaires</h3>

							<div class="comment">
								<div class="author">Alex</div>
								<div class="text">Très belle photo !</div>
							</div>

							<div class="comment">
								<div class="author">Charlie</div>
								<div class="text">J’adore la lumière 🧡</div>
							</div>
						</div>
					</div>
                </div>
            `;
		})
		.catch(error => {
			console.error("Erreur lors du chargement de l'image :", error);
			document.querySelector(".gallery").innerHTML = "<p>Impossible de charger l'image.</p>";
		});
});

function formatDate(dateString) {
	const options = { day: "numeric", month: "long", year: "numeric", hour: "2-digit", minute: "2-digit" };
	return new Date(dateString).toLocaleDateString("fr-FR", options);
}
