function addComment(event) {
    const commentInput = document.getElementById('commentInput');
    const commentText = commentInput.value.trim();
    if (commentText === '') {
        event.preventDefault();
        alert('Please enter a comment.');
    } else {
        document.getElementById('commentForm').submit();
    }
}