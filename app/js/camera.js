const video = document.getElementById('video');
const canvas = document.getElementById('canvas');
const captureButton = document.getElementById('capture');
const filterSelector = document.getElementById('filter-selector');
const messageContainer = document.getElementById('message');
const latestPhotosContainer = document.querySelector('.latest-photos');
const uploadInput = document.getElementById('upload');

let selectedFilter = null;
let selectedFilterImage = null;
let isCanvasActive = false;
let uploadedImage = null;

const constraints = { video: true };

navigator.mediaDevices.getUserMedia(constraints)
    .then((stream) => {
        video.srcObject = stream;
        isCanvasActive = true;
        requestAnimationFrame(drawFrame);
        loadLatestImages();
    })
    .catch((err) => {
        console.error("Erreur d'accès à la webcam:", err);
        displayErrorMessage("Erreur d'accès à la webcam: " + err.message);
        captureButton.disabled = true;
    });

filterSelector.addEventListener('change', (event) => {
    selectedFilter = event.target.value;
    const filterPreview = document.getElementById('filter-preview');
    const canvasWrapper = document.querySelector('.canvas-wrapper');
    const rectWrapper = canvasWrapper.getBoundingClientRect();

    if (selectedFilter) {
        const tempFilterImage = new Image();
        tempFilterImage.onload = () => {
            const naturalFilterWidth = tempFilterImage.naturalWidth;
            const naturalFilterHeight = tempFilterImage.naturalHeight;
            const filterAspectRatio = naturalFilterWidth / naturalFilterHeight;

            filterPreview.src = tempFilterImage.src;
            filterPreview.style.display = 'block';
            filterPreview.style.position = 'absolute';
            filterPreview.style.pointerEvents = 'none';
            filterPreview.style.zIndex = '3';
            filterPreview.style.left = '';
            filterPreview.style.top = '';
            filterPreview.style.width = '';
            filterPreview.style.height = '';

            captureButton.disabled = false;

            switch (selectedFilter) {
                case 'chapeau_rigolo.png':
                    const chapeauRatioLargeurSurCanvas = 0.4;
                    const chapeauLargeurPx = rectWrapper.width * chapeauRatioLargeurSurCanvas;
                    const chapeauHauteurPx = chapeauLargeurPx / filterAspectRatio;
                    const chapeauHauteurPourcentage = (chapeauHauteurPx / rectWrapper.height) * 100;

                    filterPreview.style.width = `${chapeauRatioLargeurSurCanvas * 100}%`;
                    filterPreview.style.height = `${chapeauHauteurPourcentage}%`;
                    filterPreview.style.top = `${0.03 * 100}%`;
                    filterPreview.style.left = `${(0.5 - (chapeauRatioLargeurSurCanvas / 2)) * 100}%`;
                    break;

                case 'lunettes_soleil.png':
                    const lunettesRatioLargeurSurCanvas = 0.5;
                    const lunettesLargeurPx = rectWrapper.width * lunettesRatioLargeurSurCanvas;
                    const lunettesHauteurPx = lunettesLargeurPx / filterAspectRatio;
                    const lunettesHauteurPourcentage = (lunettesHauteurPx / rectWrapper.height) * 100;

                    filterPreview.style.width = `${lunettesRatioLargeurSurCanvas * 100}%`;
                    filterPreview.style.height = `${lunettesHauteurPourcentage}%`;
                    filterPreview.style.top = `${0.35 * 100}%`;
                    filterPreview.style.left = `${(0.5 - (lunettesRatioLargeurSurCanvas / 2)) * 100}%`;
                    break;

                case 'cadre1.png':
                    filterPreview.style.width = `100%`;
                    filterPreview.style.height = `100%`;
                    filterPreview.style.top = `0%`;
                    filterPreview.style.left = `0%`;
                    break;

                default:
                    const defaultTargetWidthRatio = 0.33;
                    const defaultLargeurPx = rectWrapper.width * defaultTargetWidthRatio;
                    const defaultHauteurPx = defaultLargeurPx / filterAspectRatio;
                    const defaultHauteurPourcentage = (defaultHauteurPx / rectWrapper.height) * 100;

                    filterPreview.style.width = `${defaultTargetWidthRatio * 100}%`;
                    filterPreview.style.height = `${defaultHauteurPourcentage}%`;
                    filterPreview.style.top = `${(0.5 - (defaultHauteurPourcentage / 2))}%`;
                    filterPreview.style.left = `${(0.5 - (defaultTargetWidthRatio / 2))}%`;
            }
        };
        tempFilterImage.src = `/images/filters/${selectedFilter}`;
    } else {
        filterPreview.src = '';
        filterPreview.style.display = 'none';
        captureButton.disabled = true;
    }
});

function drawFrame() {
    if (!video.srcObject) {
        isCanvasActive = false;
        return;
    }

    isCanvasActive = true;
    const ctx = canvas.getContext('2d');
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
    requestAnimationFrame(drawFrame);
}

function drawUploadedFrame() {
    if (!uploadedImage) {
        isCanvasActive = false;
        return;
    }

    isCanvasActive = true;
    const ctx = canvas.getContext('2d');
    canvas.width = 600;
    canvas.height = 450;
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    ctx.drawImage(uploadedImage, 0, 0, canvas.width, canvas.height);
    requestAnimationFrame(drawUploadedFrame);
}

function loadLatestImages() {
    fetch('/controllers/camera.php?action=latest')
        .then(response => response.json())
        .then(images => {
            latestPhotosContainer.innerHTML = '';
            if (images.length > 0) {
                images.forEach(image => {
                    const thumbnailDiv = document.createElement('div');
                    thumbnailDiv.classList.add('thumbnail');
                    const imgElement = document.createElement('img');
                    imgElement.src = `/${image.image_url}`;
                    imgElement.alt = 'Photo récente';
                    imgElement.width = 100;
                    thumbnailDiv.appendChild(imgElement);
                    latestPhotosContainer.appendChild(thumbnailDiv);
                });
            } else {
                const noPhotosMessage = document.createElement('p');
                noPhotosMessage.textContent = 'Aucune photo prise pour le moment.';
                latestPhotosContainer.appendChild(noPhotosMessage);
            }
        })
        .catch(error => {
            console.error("Erreur lors du chargement des dernières images :", error);
            displayErrorMessage("Erreur lors du chargement des dernières images.");
        });
}

function displayErrorMessage(message) {
    messageContainer.textContent = message;
    messageContainer.style.color = 'white';
    messageContainer.style.display = 'block';
    setTimeout(() => {
        messageContainer.style.display = 'none';
        messageContainer.style.backgroundColor = '';
        messageContainer.style.color = '';
    }, 5000);
}

captureButton.addEventListener('click', () => {
    if (!isCanvasActive) {
        console.error("Le canvas n'est pas actif.");
        alert("Il faut une image pour prendre une photo !");
        return;
    }

    const imageData = canvas.toDataURL('image/png');

    fetch('/controllers/camera.php', {
        method: 'POST',
        body: JSON.stringify({ image: imageData, filter: selectedFilter }),
        headers: { 'Content-Type': 'application/json' }
    })
    .then(response => response.json())
    .then(data => {
        console.log("Réponse du serveur :", data);
        if (data.success) {
            messageContainer.textContent = data.success;
            messageContainer.style.display = 'block';
             setTimeout(() => {
                messageContainer.style.display = 'none';
                messageContainer.style.backgroundColor = '';
            }, 2000);
            loadLatestImages();
        } else if (data.error) {
            displayErrorMessage(data.error);
        }
    })
    .catch(error => {
        console.error("Erreur réseau:", error);
        displayErrorMessage("Erreur réseau lors de l'envoi de la photo.");
    });
});

uploadInput.addEventListener('change', (event) => {
    const file = event.target.files[0];
    if (!file) return;

    const allowedTypes = ['image/png', 'image/jpeg'];

     if (!allowedTypes.includes(file.type)) {
        displayErrorMessage("Type de fichier non autorisé. Veuillez uploader une image PNG, JPEG, ou GIF.");
        uploadInput.value = '';
        uploadedImage = null;
        if (video.srcObject) {
            isCanvasActive = true;
            requestAnimationFrame(drawFrame);
        }
        return;
    }

    const reader = new FileReader();
    reader.onload = (e) => {
        const img = new Image();
        img.onload = () => {
            uploadedImage = img;
             if (video.srcObject) {
                video.srcObject = null;
             }
            isCanvasActive = true;
            requestAnimationFrame(drawUploadedFrame);
        };
        img.src = e.target.result;
    };
    reader.onerror = () => {
        displayErrorMessage("Erreur lors de la lecture du fichier.");
        uploadInput.value='';
        uploadedImage=null;
         if (video.srcObject) {
            isCanvasActive = true;
            requestAnimationFrame(drawFrame);
        }
    }
    reader.readAsDataURL(file);
});
