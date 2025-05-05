const video = document.getElementById('video');
const canvas = document.getElementById('canvas');
const captureButton = document.getElementById('capture');
const filterSelector = document.getElementById('filter-selector');
const message = document.getElementById('message');
let selectedFilterImage = null;
let isCanvasActive = false; 

const constraints = { video: true };

navigator.mediaDevices.getUserMedia(constraints)
    .then((stream) => {
        video.srcObject = stream;
        isCanvasActive = true;
        requestAnimationFrame(drawFrame);
    })
    .catch((err) => console.error("Erreur d'accès à la webcam:", err));

filterSelector.addEventListener('change', (event) => {
    const selectedFilter = event.target.value;
    if (selectedFilter) { 
        const filterImage = new Image();
        filterImage.onload = () => {
            selectedFilterImage = filterImage;
            captureButton.disabled = false;
        };
        filterImage.src = `/images/filters/${selectedFilter}`;
    } else {
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
        let filterX, filterY, filterWidth, filterHeight;
        const filename = selectedFilterImage.src.split('/').pop();

        if (filename === 'chapeau_rigolo.png') {
            filterWidth = canvas.width / 2.5;
            filterHeight = (filterWidth / selectedFilterImage.naturalWidth) * selectedFilterImage.naturalHeight;
            filterX = (canvas.width - filterWidth) / 2;
            filterY = canvas.height / 50;
        } else if (filename === 'lunettes_soleil.png') {
            filterWidth = canvas.width / 2;
            filterHeight = (filterWidth / selectedFilterImage.naturalWidth) * selectedFilterImage.naturalHeight;
            filterX = (canvas.width - filterWidth) / 2;
            filterY = canvas.height / 3;
        } else if (filename === 'cadre1.png') {
            filterX = 0;
            filterY = 0;
            filterWidth = canvas.width;
            filterHeight = canvas.height;
        } else {
            filterWidth = canvas.width / 3;
            filterHeight = canvas.height / 3;
            filterX = (canvas.width - filterWidth) / 2;
            filterY = (canvas.height - filterHeight) / 2;
        }

        ctx.drawImage(selectedFilterImage, filterX, filterY, filterWidth, filterHeight);
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
        let filterX, filterY, filterWidth, filterHeight;
        const filename = selectedFilterImage.src.split('/').pop();

        if (filename === 'chapeau_rigolo.png') {
            filterWidth = canvas.width / 2.5;
            filterHeight = (filterWidth / selectedFilterImage.naturalWidth) * selectedFilterImage.naturalHeight;
            filterX = (canvas.width - filterWidth) / 2;
            filterY = canvas.height / 50;
        } else if (filename === 'lunettes_soleil.png') {
            filterWidth = canvas.width / 2;
            filterHeight = (filterWidth / selectedFilterImage.naturalWidth) * selectedFilterImage.naturalHeight;
            filterX = (canvas.width - filterWidth) / 2;
            filterY = canvas.height / 3;
        } else if (filename === 'cadre1.png') {
            filterX = 0;
            filterY = 0;
            filterWidth = canvas.width;
            filterHeight = canvas.height;
        } else {
            filterWidth = canvas.width / 3;
            filterHeight = canvas.height / 3;
            filterX = (canvas.width - filterWidth) / 2;
            filterY = (canvas.height - filterHeight) / 2;
        }

        ctx.drawImage(selectedFilterImage, filterX, filterY, filterWidth, filterHeight);
    }

    requestAnimationFrame(drawFrame);
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
        body: JSON.stringify({ image: imageData }),
        headers: { 'Content-Type': 'application/json' }
    })
        .then(response => response.json())
        .then(data => {
            console.log("Réponse du serveur :", data);
            message.style.display = 'block';
            setTimeout(() => {
                message.style.display = 'none';
            }, 2000);
        })
        .catch(error => console.error("Erreur :", error));
});