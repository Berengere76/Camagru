document.addEventListener("DOMContentLoaded", () => {
    fetch("/controllers/get_messages.php")
        .then(response => response.json())
        .then(data => {
            const homeContainer = document.querySelector(".home");

            if (data.errors) {
                const errorMessage = document.createElement("div");
                errorMessage.classList.add("error-message");
                errorMessage.textContent = data.errors;
                const form = document.querySelector("form");
                form.parentNode.insertBefore(errorMessage, form);
            }

            setTimeout(() => {
                document.querySelectorAll('.success-message, .error-message')
                    .forEach(el => el.style.display = 'none');
            }, 5000);
        })
        .catch(error => console.error("Erreur de chargement des messages :", error));
});
