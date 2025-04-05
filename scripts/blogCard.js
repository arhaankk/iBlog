document.addEventListener("DOMContentLoaded", function () {
	const form = document.querySelector("#commentForm");
	const formSubmit = document.querySelector("#commentForm button");
	const commentInput = document.querySelector('#commentInput');
	const commentHeading = document.querySelector('.comments h3');
	const commentsContainer = document.createElement('div');
	const placeholder = document.querySelector(".comments .placeholder");

	let loadComments = async() => {
		placeholder.value = 'Loading comments…';
		placeholder.style['display'] = 'inherit';
		const id = form.dataset.post;
		const resp = await fetch('../actions/comments.php?post='+id);
		if (!resp.ok) {
			placeholder.value = 'Failed to load comments.';
			return false;
		}
		data = await resp.json();
		commentsContainer.innerHTML = '';
		for (let i = 0; i < data.length; i++) {
			const v = data[i];
			const comment = document.createElement('div');
			comment.className = 'comment';
			const pfp = document.createElement('img');
			pfp.src = `../actions/avatar.php?user=${v['userId']}`;
			pfp.alt = `${v['displayname']}'s Profile Picture`;
			pfp.className = 'comment-avatar';
			comment.appendChild(pfp);
			const name = document.createElement('strong');
			name.innerText = `${v['displayname']}:`;
			name.innerHTML += '&nbsp;';
			comment.appendChild(name);
			const content = document.createElement('span');
			content.innerText = v['content'];
			comment.appendChild(content);
			commentsContainer.appendChild(comment);
		}
		placeholder.style['display'] = 'none';
		return true;
	}

	let validateComment = async () => {
		const commentText = commentInput.value.trim();
		if (commentText === '') {
			alert('Please enter a comment.');
			return false;
		}
		if (commentText.length >= 256) {
			alert('Your comment must be 256 characters or less.');
			return false;
		}
		return true;
	}

	let sendComment = async () => {
		const text = commentInput.value.trim();
		const id = form.dataset.post;
		const resp = await fetch('../actions/comments.php?post='+id, {
			method: 'POST',
			body: text
		});
		if (resp.ok) {
			alert('Comment sent successfully.');
			return true;
		} else {
			alert('Failed to send comment.');
			return false;
		}
	}

	let handleForm = async (e) => {
		e.preventDefault();
		if (!await validateComment())
			return;
		if (!await sendComment())
			return;
		commentInput.value = '';
		await loadComments();
	}

	form.addEventListener('submit', handleForm);
	formSubmit.addEventListener('click', handleForm);
	commentHeading.after(commentsContainer);
	loadComments();

	setInterval(loadComments, 5000);
});
