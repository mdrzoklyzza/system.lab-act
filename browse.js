const videos = document.querySelectorAll("#slider video");
let current = 0;

function showVideo(index) {
    videos.forEach((vid, i) => {
        vid.classList.toggle("active", i === index);
    });
}

function nextVideo() {
    current = (current + 1) % videos.length;
    showVideo(current);
}

function prevVideo() {
    current = (current - 1 + videos.length) % videos.length;
    showVideo(current);
}
