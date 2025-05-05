const video = document.getElementById('video');
const canvas = document.getElementById('canvas');
const captureButton = document.getElementById('capture');
const filterSelector = document.getElementById('filter-selector');
const message = document.getElementById('message');
const latestPhotosContainer = document.querySelector('.latest-photos'); // Sélectionne le conteneur des miniatures
let selectedFilter = null;
let selectedFilterImage = null;
let isCanvasActive = false;

const constraints = { video: true };

navigator.mediaDevices.getUserMedia(constraints)
    .then((stream) => {
        video.srcObject = stream;
        isCanvasActive = true;
        requestAnimationFrame(drawFrame);
        loadLatestImages();
    })
    .catch((err) => console.error("Erreur d'accès à la webcam:", err));

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

const uploadInput = document.getElementById('upload');
let uploadedImage = null;

uploadInput.addEventListener('change', (event) => {
    const file = event.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = (e) => {
        const img = new Image();
        img.onload = () => {
            uploadedImage = img;
            video.srcObject = null;
            isCanvasActive = true;
            requestAnimationFrame(drawUploadedFrame);
        };
        img.src = e.target.result;
    };
    reader.readAsDataURL(file);
});

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
        .catch(error => console.error("Erreur lors du chargement des dernières images :", error));
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
                message.textContent = data.success;
                message.style.display = 'block';
                setTimeout(() => {
                    message.style.display = 'none';
                }, 2000);
                loadLatestImages();
            } else if (data.error) {
                console.error("Erreur lors de l'enregistrement :", data.error);
                message.textContent = data.error;
                message.style.backgroundColor = 'red';
                message.style.color = 'white';
                message.style.display = 'block';
                setTimeout(() => {
                    message.style.display = 'none';
                    message.style.backgroundColor = '';
                    message.style.color = '';
                }, 3000);
            }
        })
        .catch(error => console.error("Erreur :", error));
});