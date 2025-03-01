function addComment(event) {
    const commentInput = document.getElementById('commentInput');
    const commentText = commentInput.value.trim();
    if (commentText === '') {
        event.preventDefault();
        alert('Please enter a comment.');
    } else {
        // 如果验证通过，手动提交表单
        document.getElementById('commentForm').submit();
    }
}