let placeholder = null;
const panes = document.createElement('div');
panes.className = 'panes';
const pane = [];
const paneStates = [null, null, null];
let users = {};
let target = null;

const setUser = async (username, params) => {
	if (!username in users) {
		alert('Unable to find user '+username);
		return;
	}
	user = users[username];
	const resp = await fetch('../actions/users.php?id='+user['id'], {
		method: 'POST',
		body: JSON.stringify(params),
	});
	if (!resp.ok) {
		alert('Failed to update user.');
		return;
	}
}

const linkSelect = async (p, name, event) => {
	/* This isn't exactly ideal for now */
	paneStates[p] = name;
	switch (p) {
	case 0:
		pane[1].innerHTML = '';
		pane[2].innerHTML = '';
		switch (name) {
		case 'General':
			buildPane(1, 'Options', ['Server Statistics', 'Check Configuration']);
			break;
		case 'Users':
			buildPane(1, 'Find User', ['Show All', 'Search by Username', 'Search by Email']);
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
			case 'Show All':
				break;
			case 'Search by Username':
				url += '?username='+encodeURIComponent(prompt('Enter the username:'));
				break;
			case 'Search by Email':
				url += '?email='+encodeURIComponent(prompt('Enter the email:'));
				break;
			default:
				if (!name in users) {
					return; /* Not a user */
				}
				/* Build next pane */
				selectPane(p, name);
				buildUserPane(2, users[name]);
				return;
			}
			resp = await fetch(url);
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
			break;
		case 'General':
			url = '../actions/info.php';
			switch (name) {
			case 'Server Statistics':
				url += '?query=stats';
				break;
			case 'Check Configuration':
				url += '?query=config';
			}
			resp = await fetch(url);
			if (!resp.ok) {
				alert('Failed to perform check. The server may be misconfigured.');
				return;
			}
			results = await resp.json();
			switch (name) {
			case 'Server Statistics':
				buildCustomPane(2, 'Statistics', null, results);
				break;
			case 'Check Configuration':
				buildCustomPane(2, 'Configuration', null, results);
			}
			selectPane(p, name);
			break;
		}
	case 2:
		switch (paneStates[0]) {
		case 'Users':
			switch (name) {
			case 'Enable User':
				await setUser(target, {'disabled': 0});
				break;
			case 'Disable User':
				await setUser(target, {'disabled': 1});
				break;
			}
			/* Try to fetch specific user and rebuild */
			if (users && target && target in users) {
				const resp = await fetch('../actions/users.php?id='+users[target]['id']);
				if (!resp.ok)
					return;
				const data = await resp.json();
				if (data.length < 1)
					return;
				users[target] = data[0];
				buildUserPane(2, users[target]);
			}
		}
	}
}

const buildPane = (i, title, links) => {
	pane[i].innerHTML = '';
	const paneTitle = document.createElement('h2');
	paneTitle.innerText = title;
	pane[i].appendChild(paneTitle);
	if (links.length < 1)
		return;
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

const buildCustomPane = (i, title, preamble, info) => {
	/* Again, not that ideal */
	pane[i].innerHTML = '';
	const paneTitle = document.createElement('h2');
	paneTitle.innerText = title;
	pane[i].appendChild(paneTitle);
	const paneInfo = document.createElement('ul');
	for (let key of Object.keys(info)) {
		const val = info[key];
		const item = document.createElement('li');
		item.innerText = key+': '+val;
		paneInfo.appendChild(item);
	}
	pane[i].appendChild(paneInfo);
}

const buildUserPane = (i, user) => {
	/* Again, not that ideal */
	target = user['username'];
	pane[i].innerHTML = '';
	const paneTitle = document.createElement('h2');
	paneTitle.innerText = user['username'];
	pane[i].appendChild(paneTitle);
	const paneInfo = document.createElement('ul');
	const info = {
		'ID': user['id'],
		'Username': user['username'],
		'Email': user['email'],
		'Display name': user['displayname'],
		'Admin': user['admin'] === 1 ? 'Yes' : 'No',
		'Disabled': user['disabled'] === 1 ? 'Yes' : 'No',
	};
	for (let key of Object.keys(info)) {
		const val = info[key];
		const item = document.createElement('li');
		item.innerText = key+': '+val;
		paneInfo.appendChild(item);
	}

	/* Disable / enable */
	const item = document.createElement('li');
	const link = document.createElement('a');
	link.href='#';
	link.innerText = user['disabled'] === 1 ? 'Enable User' : 'Disable User';
	link.addEventListener('click', (e) => { linkSelect(i, link.innerText, e) });
	item.appendChild(link);
	paneInfo.appendChild(item);

	pane[i].appendChild(paneInfo);
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
