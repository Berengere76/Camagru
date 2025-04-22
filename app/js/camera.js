const video = document.getElementById('video');
const canvas = document.getElementById('canvas');
const captureButton = document.getElementById('capture');
const filterSelector = document.getElementById('filter-selector');
console.log('filterSelector element:', filterSelector); 
const message = document.getElementById('message');
let selectedFilterImage = null;

const constraints = { video: true };

navigator.mediaDevices.getUserMedia(constraints)
    .then((stream) => {
        video.srcObject = stream;
        requestAnimationFrame(drawFrame);
    })
    .catch((err) => console.error("Erreur d'accès à la webcam:", err));

filterSelector.addEventListener('change', (event) => {
    const selectedFilter = event.target.value;
    if (selectedFilter) { 
        const filterImage = new Image();
        filterImage.onload = () => {
            selectedFilterImage = filterImage;
            console.log('Filter image loaded:', selectedFilterImage.src);
        };
        filterImage.src = `/images/filters/${selectedFilter}`;
    } else {
        selectedFilterImage = null;
        console.log('Aucun filtre sélectionné');
    }
});

function drawFrame() {
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