document.addEventListener("DOMContentLoaded", function () {
    const imageId = new URLSearchParams(window.location.search).get("imageid");
    let currentUserId = null;
    const commentForm = document.getElementById("comment-form");

    function loadImageAndComments() {
        fetch(`/controllers/image.php?imageid=${imageId}&ajax=1`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        })
            .then(response => response.json())
            .then(data => {
                const image = data.image;
                const comments = data.comments;
                currentUserId = data.current_user_id;
                let likeCount = data.like_count;
                let isLiked = data.is_liked;

                if (data.error) {
                    document.querySelector(".gallery").innerHTML = `<p>${data.error}</p>`;
                    return;
                }

                const galleryContainer = document.querySelector(".gallery");

                const commentHTML = comments.map(comment => {
                    let deleteButtonHTML = '';
                    if (currentUserId !== null && comment.comment_user_id === currentUserId) {
                        deleteButtonHTML = `<button class="delete-comment-btn" data-comment-id="${comment.id}">Supprimer</button>`;
                    }
                    return `
                        <div class="comment">
                            <div class="author">${comment.username}</div>
                            <div class="text">${comment.comment}</div>
                            ${deleteButtonHTML}
                        </div>
                    `;
                }).join("");

                const likeButtonHTML = currentUserId !== null ? `
                    <button id="like-button" class="like-button ${isLiked ? 'liked' : ''}" data-image-id="${imageId}">
                        ❤️ <span id="like-count">${likeCount}</span> ${isLiked ? 'Unlike' : 'Like'}
                    </button>
                ` : `
                    ❤️ <span id="like-count">${likeCount}</span>
                `;

                galleryContainer.innerHTML = `
                    <div class="container-image">
                        <div class="image-preview">
                            <img src="${image.image_url}" alt="Image de la galerie">
                        </div>
                        <div class="photo-meta">
                            <span>Ajoutée le :  ${formatDate(image.created_at)}</span>
                            <span>Par : <strong>${image.username}</strong></span>
                        </div>
                        <div class="likes-comments">
                            <div class="like-section">
                                ${likeButtonHTML}
                            </div>

                            <div class="comment-section">
                                <h3>Commentaires</h3>
                                ${commentHTML}
                                <form id="comment-form" class="comment-form" method="POST" action="/controllers/image.php">
                                    <input type="hidden" name="imageid" value="${imageId}">
                                    <input type="text" class="comment-form-input" id="comment-input" name="comment-input" placeholder="Ajouter un commentaire">
                                    <button type="submit" class="comment-button" name="sendcomment">Envoyer</button>
                                </form>
                            </div>
                        </div>
                    </div>
                `;

                const likeButton = document.getElementById("like-button");
                if (likeButton) {
                    likeButton.addEventListener('click', handleLikeToggle);
                }

                const deleteButtons = document.querySelectorAll(".delete-comment-btn");
                deleteButtons.forEach(button => {
                    button.addEventListener('click', handleDeleteComment);
                });
                
                const currentCommentForm = document.getElementById("comment-form");
                if (currentCommentForm) {
                    currentCommentForm.addEventListener('submit', handleCommentSubmit);
                }

            })
            .catch(error => {
                console.error("Erreur lors du chargement de l'image :", error);
                document.querySelector(".gallery").innerHTML = "<p>Impossible de charger l'image.</p>";
            });
    }

    function handleLikeToggle() {
        const likeButton = this;
        const imageId = likeButton.dataset.imageId;
        const isCurrentlyLiked = likeButton.classList.contains('liked');
        const action = isCurrentlyLiked ? 'unlikeimage' : 'likeimage';

        fetch(`/controllers/image.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `${action}=1&imageid=${imageId}`
        })
            .then(response => response.json())
            .then(data => {
				console.log("Réponse du serveur :", data);
                if (data.error) {
                    alert(data.error);
                } else if (data.success) {
                    const likeCountElement = document.getElementById("like-count");
                    const likeButton = document.getElementById("like-button");
                    if (likeCountElement && likeButton) {
                        likeCountElement.textContent = data.like_count;
                        likeButton.classList.toggle('liked', data.is_liked);
                        likeButton.innerHTML = `❤️ <span id="like-count">${data.like_count}</span> ${data.is_liked ? 'Unlike' : 'Like'}`;
                    }
                }
            })
            .catch(error => {
                console.error("Erreur lors du like/unlike de l'image :", error);
                alert("Erreur lors du like/unlike de l'image.");
            });
    }

    function handleCommentSubmit(event) {
        event.preventDefault();

        const commentForm = event.target;
        const commentInput = commentForm.querySelector("#comment-input");
        const commentText = commentInput.value;

        if (commentText.trim() === "") {
            alert("Le commentaire ne peut pas être vide.");
            return;
        }

        fetch(`/controllers/image.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `sendcomment=1&imageid=${imageId}&comment-input=${encodeURIComponent(commentText)}`
        })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert(data.error);
                } else if (data.success) {
                    if (commentInput) {
                        commentInput.value = "";
                    }
                    loadImageAndComments();
                }
            })
            .catch(error => {
                console.error("Erreur lors de l'envoi du commentaire :", error);
                alert("Erreur lors de l'envoi du commentaire.");
            });
    }

    function handleDeleteComment(event) {
        const commentId = event.target.dataset.commentId;
        if (confirm("Êtes-vous sûr de vouloir supprimer ce commentaire ?")) {
            fetch(`/controllers/image.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `deletecomment=1&commentid=${commentId}`
            })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        alert(data.error);
                    } else if (data.success) {
                        loadImageAndComments();
                    }
                })
                .catch(error => {
                    console.error("Erreur lors de la suppression du commentaire :", error);
                    alert("Erreur lors de la suppression du commentaire.");
                });
        }
    }
    if (commentForm) {
        commentForm.addEventListener('submit', handleCommentSubmit);
    }

    loadImageAndComments();
});

function formatDate(dateString) {
    const options = { day: "numeric", month: "long", year: "numeric", hour: "2-digit", minute: "2-digit" };
    return new Date(dateString).toLocaleDateString("fr-FR", options);
}