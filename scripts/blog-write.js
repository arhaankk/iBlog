document.getElementById('blogForm').addEventListener('submit', function(event) {
    event.preventDefault();
    
    const title = document.getElementById('title').value;
    const content = document.getElementById('content').value;
    


    console.log('Blog Title:', title);
    console.log('Blog Content:', content);
    
    alert('Blog post published!');
    
    // Clear the form
    document.getElementById('blogForm').reset();
});