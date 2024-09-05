// public/js/comments.js
/* document.addEventListener('DOMContentLoaded', function() {
    const commentForm = document.querySelector('#comment-form');
    const replyLinks = document.querySelectorAll('.reply');

    replyLinks.forEach(link => {
        link.addEventListener('click', function(event) {
            event.preventDefault();
            const commentId = this.getAttribute('data-comment-id');
            const replyFormContainer = document.querySelector(`#reply-form-container-${commentId}`);
            replyFormContainer.innerHTML = `
                <form id="reply-form-${commentId}" class="reply-form">
                    <div class="row">
                      <div class="col form-group">
                        <textarea name="content" class="form-control" placeholder="Your Comment*"></textarea>
                      </div>
                      <input type="hidden" name="parent_id" value="${commentId}">
                    </div>
                    <div class="text-center">
                      <button type="submit" class="btn btn-primary">Reply</button>
                    </div>
                </form>
            `;

            const replyForm = document.querySelector(`#reply-form-${commentId}`);
            replyForm.addEventListener('submit', function(event) {
                event.preventDefault();
                const formData = new FormData(replyForm);
                fetch(commentForm.action, {
                    method: 'POST',
                    body: formData,
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (data.commentHtml) {
                            const commentsContainer = document.querySelector(`#reply-form-container-${commentId}`);
                            commentsContainer.innerHTML += data.commentHtml;
                        } else if (data.message) {
                            alert(data.message);
                        }
                        replyForm.remove();
                    } else {
                        alert('Erreur lors de la soumission du commentaire');
                    }
                })
                .catch(error => console.error('Erreur:', error));
            });
        });
    });

    if (commentForm) {
        commentForm.addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(commentForm);
            fetch(commentForm.action, {
                method: 'POST',
                body: formData,
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.commentHtml) {
                        const commentsContainer = document.querySelector('#comments');
                        commentsContainer.innerHTML += data.commentHtml;
                    } else if (data.message) {
                        alert(data.message);
                    }
                    commentForm.reset();
                } else {
                    alert('Erreur lors de la soumission du commentaire');
                }
            })
            .catch(error => console.error('Erreur:', error));
        });
    }
}); */

/**
 * Code ajouté le 04 sept 2024 à 00h49 à KCC, qui remplace le code en commentaire
 */

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.reply').forEach(button => {
        button.addEventListener('click', function(event) {
            event.preventDefault();
            const parentId = this.getAttribute('data-parent-id');
            const form = document.querySelector('#comment-form-template').content.cloneNode(true);
            form.querySelector('form').setAttribute('data-parent-id', parentId);
            this.closest('.comment').appendChild(form);
        });
    });

    document.getElementById('comment-button').addEventListener('click', function() {
        const form = document.querySelector('#comment-form-template').content.cloneNode(true);
        document.getElementById('comment-form-container').appendChild(form);
    });

    document.addEventListener('submit', function(event) {
        if (event.target.matches('#comment-form')) {
            event.preventDefault();
            const form = event.target;
            const parentId = form.getAttribute('data-parent-id');
            const formData = new FormData(form);

            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const commentHtml = data.commentHtml;
                    if (parentId) {
                        document.querySelector(`[data-parent-id="${parentId}"]`).closest('.comment').querySelector('.replies').innerHTML += commentHtml;
                    } else {
                        document.querySelector('#comments').innerHTML += commentHtml;
                    }
                    form.remove();
                } else {
                    alert('Error: ' + data.errors);
                }
            });
        }
    });
});

