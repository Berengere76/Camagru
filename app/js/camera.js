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
    if (selectedFilter) {
        const filterImage = new Image();
        filterImage.onload = () => {
            selectedFilterImage = filterImage;
            captureButton.disabled = false;
        };
        filterImage.src = `/images/filters/${selectedFilter}`;
    } else {
        selectedFilter = null;
        selectedFilterImage = null;
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

    if (selectedFilterImage) {
        drawFilterOnCanvas(ctx);
    }

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

    if (selectedFilterImage) {
        drawFilterOnCanvas(ctx);
    }
    requestAnimationFrame(drawUploadedFrame);
}

function drawFilterOnCanvas(ctx) {
    if (!selectedFilterImage) return;

    let filterX, filterY, filterWidth, filterHeight;
    const filename = selectedFilterImage.src.split('/').pop();
    const canvasWidth = ctx.canvas.width;
    const canvasHeight = ctx.canvas.height;

    if (filename === 'chapeau_rigolo.png') {
        filterWidth = canvasWidth / 2.5;
        filterHeight = (filterWidth / selectedFilterImage.naturalWidth) * selectedFilterImage.naturalHeight;
        filterX = (canvasWidth - filterWidth) / 2;
        filterY = canvasHeight / 50;
    } else if (filename === 'lunettes_soleil.png') {
        filterWidth = canvasWidth / 2;
        filterHeight = (filterWidth / selectedFilterImage.naturalWidth) * selectedFilterImage.naturalHeight;
        filterX = (canvasWidth - filterWidth) / 2;
        filterY = canvasHeight / 3;
    } else if (filename === 'cadre1.png') {
        filterX = 0;
        filterY = 0;
        filterWidth = canvasWidth;
        filterHeight = canvasHeight;
    } else {
        filterWidth = canvasWidth / 3;
        filterHeight = canvasHeight / 3;
        filterX = (canvasWidth - filterWidth) / 2;
        filterY = (canvasHeight - filterHeight) / 2;
    }

    ctx.drawImage(selectedFilterImage, filterX, filterY, filterWidth, filterHeight);
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
