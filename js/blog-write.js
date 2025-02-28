document.getElementById('blogForm').addEventListener('submit', function(event) {
    event.preventDefault();

    var title = document.getElementById('title').value.trim();
    var content = document.getElementById('content').value.trim();
    var image = document.getElementById('image').files[0];

    // 验证字段是否为空
    if (title === '' || content === '') {
        alert('Please fill in all fields.');
        return;
    }

    var formData = new FormData();
    formData.append('title', title);
    formData.append('content', content);
    formData.append('image', image);

    console.log('Blog post submitted:', { title: title, content: content, image: image ? image.name : 'No image uploaded' });

    alert('Blog post published successfully!');

    document.getElementById('blogForm').reset();
});