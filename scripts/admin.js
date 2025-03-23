let placeholder = null;
const panes = document.createElement('div');
panes.className = 'panes';
const pane = [];
const paneStates = [null, null, null];
let users = {};
let user = {};

const linkSelect = async (p, name, event) => {
	/* This isn't exactly ideal for now */
	paneStates[p] = name;
	switch (p) {
	case 0:
		pane[1].innerHTML = '';
		pane[2].innerHTML = '';
		switch (name) {
		case 'General':
			pane[1].innerHTML = 'Work in progress.';
			break;
		case 'Users':
			buildPane(1, 'Find User', ['By Username', 'By Email']);
			break;
		}
		selectPane(p, name);
		break;
	case 1:
		pane[2].innerHTML = '';
		switch (paneStates[0]) {
		case 'Users':
			url = '../actions/users.php';
			switch (name) {
			case 'By Username':
				url += '?username='+encodeURIComponent(prompt('Enter the username:'));
				break;
			case 'By Email':
				url += '?email='+encodeURIComponent(prompt('Enter the email:'));
				break;
			default:
				if (!name in users) {
					return; /* Not a user */
				}
				user = users[name];
				console.log(user);
				return;
			}
			const resp = await fetch(url);
			if (!resp.ok) {
				alert('Failed to perform search. Please try a different query.');
				return;
			}
			results = await resp.json();
			if (results.length < 1) {
				alert('No results found for this search.');
				return;
			}
			users = {};
			const names = [];
			for (let i = 0; i < results.length; i++) {
				names[i] = results[i]['username'];
				users[results[i]['username']] = results[i];
			}
			buildPane(1, 'Results', names);
		}
	case 2:
	}
}

const buildPane = (i, title, links) => {
	pane[i].innerHTML = '';
	const paneTitle = document.createElement('h2');
	paneTitle.innerText = title;
	pane[i].appendChild(paneTitle);
	const paneLinks = document.createElement('ul');
	for (let j = 0; j < links.length; j++) {
		const item = document.createElement('li');
		const link = document.createElement('a');
		link.href='#';
		link.innerText = links[j];
		link.addEventListener('click', (e) => { linkSelect(i, links[j], e) });
		item.appendChild(link);
		paneLinks.appendChild(item);
	}
	pane[i].appendChild(paneLinks);
}

const selectPane = (i, selected) => {
	paneStates[i] = selected;
	const options = pane[i].querySelectorAll('li > a');
	for (let j = 0; j < options.length; j++) {
		if (options[j].innerText === selected)
			options[j].parentNode.classList.add('active');
		else
			options[j].parentNode.classList.remove('active');
	}
}

const startPanel = () => {

	for (let i = 0; i < 3; i++) {
		pane[i] = document.createElement('section');
		pane[i].className = 'pane';
		panes.appendChild(pane[i]);
	}
	buildPane(0, 'Options', ['General', 'Users']);
	selectPane(0, 'General');
	linkSelect(0, 'General', null);
	/* Hide placeholder */
	placeholder = document.querySelector('.placeholder');
	placeholder.style['display'] = 'none';
	placeholder.after(panes);
}

document.addEventListener("DOMContentLoaded", () => {
	startPanel();
});
