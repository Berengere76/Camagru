document.addEventListener("DOMContentLoaded", function () {
    let allImages = [];
    const galleryContainer = document.querySelector(".gallery");
    const paginationContainer = document.createElement('div');
    paginationContainer.classList.add('pagination');
    galleryContainer.parentNode.insertBefore(paginationContainer, galleryContainer.nextSibling);

    const imagesPerPage = 8;
    let currentPage = 1;

    function displayImages(page) {
        galleryContainer.innerHTML = '';

        const startIndex = (page - 1) * imagesPerPage;
        const endIndex = startIndex + imagesPerPage;
        const imagesToDisplay = allImages.slice(startIndex, endIndex);

        if (imagesToDisplay.length === 0 && allImages.length > 0) {
            currentPage = Math.ceil(allImages.length / imagesPerPage);
            displayImages(currentPage);
            return;
        } else if (imagesToDisplay.length === 0 && allImages.length === 0) {
            galleryContainer.innerHTML = "<p>Aucune image à afficher.</p>";
            paginationContainer.innerHTML = '';
            return;
        }

        const imageElements = imagesToDisplay.map(image => `
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

        galleryContainer.innerHTML = imageElements;
        renderPagination();
    }

    function renderPagination() {
        paginationContainer.innerHTML = '';
        const totalPages = Math.ceil(allImages.length / imagesPerPage);

        if (totalPages <= 1) {
            return;
        }

        const prevButton = document.createElement('button');
        prevButton.textContent = 'Précédent';
        prevButton.disabled = currentPage === 1;
        prevButton.addEventListener('click', () => {
            if (currentPage > 1) {
                currentPage--;
                displayImages(currentPage);
            }
        });
        paginationContainer.appendChild(prevButton);

        for (let i = 1; i <= totalPages; i++) {
            const pageButton = document.createElement('button');
            pageButton.textContent = i;
            pageButton.classList.add('page-number');
            if (i === currentPage) {
                pageButton.classList.add('active');
            }
            pageButton.addEventListener('click', () => {
                currentPage = i;
                displayImages(currentPage);
            });
            paginationContainer.appendChild(pageButton);
        }

        const nextButton = document.createElement('button');
        nextButton.textContent = 'Suivant';
        nextButton.disabled = currentPage === totalPages;
        nextButton.addEventListener('click', () => {
            if (currentPage < totalPages) {
                currentPage++;
                displayImages(currentPage);
            }
        });
        paginationContainer.appendChild(nextButton);
    }

    fetch('/controllers/galerie.php?ajax', {
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
            allImages = images;
            displayImages(currentPage);
        })
        .catch(error => {
            console.error("Erreur lors du chargement des images :", error);
            galleryContainer.innerHTML = "<p>Impossible de charger les images.</p>";
        });
});

function timeAgo(dateString) {
    const now = new Date();
    const date = new Date(dateString);
    const localDate = new Date(date.getTime() - date.getTimezoneOffset() * 60000);

    const diffMs = now - localDate;
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