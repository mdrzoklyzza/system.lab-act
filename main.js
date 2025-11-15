const commentLikeModal = document.getElementById('commentLikeModal');
const closeCommentLikeModal = document.getElementById('closeCommentLikeModal');
const commentInput = document.getElementById('commentInput');
const modalComments = document.getElementById('modalComments');
const likeCountSpan = document.getElementById('likeCount');
const likeButton = document.getElementById('likeButton');
const modalImageContainer = document.getElementById('modalImageContainer');
const submitCommentButton = document.getElementById('submitComment');
let currentPostId = null;

// Handle post image click to open comment/like modal
document.querySelectorAll('.post-image').forEach(img => {
    img.addEventListener('click', () => {
        currentPostId = img.getAttribute('data-post-id');
        commentLikeModal.style.display = 'flex';
        modalImageContainer.innerHTML = `<img src="${img.src}" alt="Post Image" style="max-width: 100%; max-height: 300px; border-radius: 10px;">`;

        fetch(`fetch_comments.php?post_id=${currentPostId}`)
            .then(response => response.text())
            .then(data => {
                modalComments.innerHTML = data;
            });

        fetch(`fetch_likes.php?post_id=${currentPostId}`)
            .then(response => response.json())
            .then(data => {
                likeCountSpan.textContent = data.like_count;
                likeButton.textContent = (data.liked_by_user ? '♥' : '♡') + ` (${data.like_count})`;
                likeButton.setAttribute('data-liked', data.liked_by_user);
            });
    });
});

closeCommentLikeModal.onclick = () => {
    commentLikeModal.style.display = "none";
};

submitCommentButton.onclick = () => {
    const comment = commentInput.value.trim();
    if (!comment || !currentPostId) return;

    fetch('submit_comment.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `post_id=${currentPostId}&comment_text=${encodeURIComponent(comment)}`
    })
    .then(response => response.text())
    .then(() => {
        commentInput.value = '';
        return fetch(`fetch_comments.php?post_id=${currentPostId}`);
    })
    .then(response => response.text())
    .then(data => {
        modalComments.innerHTML = data;
    });
};

likeButton.onclick = () => {
    if (!currentPostId) return;

    fetch('like_post.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `post_id=${currentPostId}`
    })
    .then(response => response.json())
    .then(data => {
        const liked = data.liked_by_user;
        likeCountSpan.textContent = data.like_count;
        likeButton.textContent = (liked ? '♥' : '♡') + ` (${data.like_count})`;
        likeButton.setAttribute('data-liked', liked);
    });
};

// Close modal when clicking outside
window.onclick = (event) => {
    if (event.target === commentLikeModal) {
        commentLikeModal.style.display = "none";
    }
};

// Blog modal handlers
document.addEventListener('DOMContentLoaded', function () {
    const openModalBtn = document.getElementById("openModalBtn");
    const blogModal = document.getElementById("blogModal");
    const closeModalBtn = document.getElementById("closeModalBtn");
    const cancelModalBtn = document.getElementById("cancelModalBtn");

    if (openModalBtn && blogModal) {
        openModalBtn.onclick = () => {
            blogModal.style.display = "flex";
        };

        closeModalBtn.onclick = () => {
            blogModal.style.display = "none";
        };

        cancelModalBtn.onclick = () => {
            blogModal.style.display = "none";
        };

        window.addEventListener("click", (event) => {
            if (event.target === blogModal) {
                blogModal.style.display = "none";
            }
        });
    }

    // Affiliate link modal handlers
    const linkModal = document.getElementById("linkModal");
    const closeLinkModalBtn = document.getElementById("closeLinkModalBtn");
    const cancelLinkModalBtn = document.getElementById("cancelLinkModalBtn");
    const saveLinkBtn = document.getElementById("saveLinkBtn");
    const openLinkModalBtn = document.getElementById("openLinkModalBtn");

    if (openLinkModalBtn && linkModal) {
        openLinkModalBtn.onclick = () => {
            linkModal.style.display = "flex";
        };
    }

    if (closeLinkModalBtn) {
        closeLinkModalBtn.onclick = () => {
            linkModal.style.display = "none";
        };
    }

    if (cancelLinkModalBtn) {
        cancelLinkModalBtn.onclick = () => {
            linkModal.style.display = "none";
        };
    }

    if (saveLinkBtn) {
        saveLinkBtn.onclick = () => {
            const linkInput = document.getElementById("linkInput");
            const url = linkInput.value.trim();

            if (!url) {
                // Replace with toast/snackbar/inline alert if desired
                document.getElementById("linkError").textContent = "Please enter a URL.";
                return;
            }

            const hiddenInput = document.getElementById("hiddenAffiliateLink");
            if (hiddenInput) {
                hiddenInput.value = url;
            }

            linkModal.style.display = "none";
            linkInput.value = '';
            document.getElementById("linkError").textContent = "";
        };
    }

    window.addEventListener("click", (event) => {
        if (event.target === linkModal) {
            linkModal.style.display = "none";
        }
    });
});

function confirmDelete(postId) {
    const modal = document.getElementById('deleteConfirmModal');
    const deleteBtn = document.querySelector('#deleteConfirmModal .done-btn');
    const cancelBtn = document.getElementById('cancelDeleteBtn');

    modal.style.display = 'flex';

    deleteBtn.onclick = () => {
        window.location.href = `delete_post.php?id=${postId}`;
    };

    cancelBtn.onclick = () => {
        modal.style.display = 'none';
    };
}


function closeSuccessModal() {
    document.getElementById('successModal').style.display = 'none';
    history.replaceState(null, "", window.location.pathname);
}

window.onload = function () {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('submitted') === '1') {
        document.getElementById('successModal').style.display = 'flex';
    }
};
document.addEventListener('DOMContentLoaded', function () {
    const deleteIcons = document.querySelectorAll('.delete-post-icon');
    const deleteModal = document.getElementById('deleteConfirmModal');
    const cancelDeleteBtn = document.getElementById('cancelDeleteBtn');
    const deletePostIdInput = document.getElementById('deletePostId');

    deleteIcons.forEach(icon => {
        icon.addEventListener('click', function () {
            const postId = this.getAttribute('data-post-id');
            deletePostIdInput.value = postId;
            deleteModal.style.display = 'flex';
        });
    });

    cancelDeleteBtn.addEventListener('click', function () {
        deleteModal.style.display = 'none';
    });

    window.addEventListener('click', function (event) {
        if (event.target === deleteModal) {
            deleteModal.style.display = 'none';
        }
    });
});
