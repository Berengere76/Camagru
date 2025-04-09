document.addEventListener("DOMContentLoaded", () => {
    fetch("/controllers/get_messages.php")
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById("message-container");

            if (data.errors) {
                const errorMessage = document.createElement("div");
                errorMessage.classList.add("error-message");
                errorMessage.textContent = data.errors;
                container.appendChild(errorMessage);
            }

            if (data.success) {
                const successMessage = document.createElement("div");
                successMessage.classList.add("success-message");
                successMessage.textContent = data.success;
                container.appendChild(successMessage);
            }

            setTimeout(() => {
                document.querySelectorAll('.success-message, .error-message')
                    .forEach(el => el.style.display = 'none');
            }, 5000);
        })
        .catch(error => console.error("Erreur de chargement des messages :", error));
});
