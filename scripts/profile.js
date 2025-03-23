document.addEventListener("DOMContentLoaded", function () {
	const editButton = document.getElementById("edit-button");
	const saveButton = document.getElementById("save-button");
	const inputs = document.querySelectorAll(".login__input");
	const form = document.querySelector('form');

	let uploadImage = async () => {
		const avatar = document.querySelector('#avatar');
		if (avatar.files.length < 1)
			return true;
		const data = await avatar.files[0].arrayBuffer();
		const resp = await fetch('../actions/set-avatar.php', {
			method: "POST",
			body: data
		});
		if (resp.ok) {
			avatar.value = null;
			alert('Profile image updated successfully.');
			return true;
		} else {
			alert('Failed to upload profile image.');
			return false;
		}
	}

	let updateProfile = async () => {
		const f = form.elements;
		let profile = {
			firstname: f['first-name'].value,
			lastname: f['last-name'].value,
			email: f['email'].value,
			gender: f['gender'].value,
			age: parseInt(f['age'].value),
		};
		const resp = await fetch('../actions/set-profile.php', {
			method: "POST",
			body: JSON.stringify(profile),
		});
		if (resp.ok) {
			avatar.value = null;
			alert('Profile updated successfully.');
			return true;
		} else {
			alert('Failed to update profile.');
			return false;
		}
	}

	let validateForm = () => {
		let valid = true;
		let err = '';
		/* Dirty reimplementation of HTML checks */
		for (i of inputs) {
			if (i.value.length === 0 && i.hasAttribute('required')) {
				i.classList.add('aria-invalid');
				valid = false;
				err = `The ${i.name} is required.`;
			} else if (i.type === 'number' && parseInt(i.value,10).toString() !== i.value) {
				i.classList.add('aria-invalid');
				valid = false;
				err = `The ${i.name} must be a number.`;
			} else if (i.type === 'email' && i.value.indexOf('@') < 0) {
				i.classList.add('aria-invalid');
				valid = false;
				err = `The ${i.name} must be a valid email.`;
			} else {
				i.classList.remove('aria-invalid');
			}
		}
		const f = form.elements;
		if (parseInt(f['age'].value) < 18 || parseInt(f['age'].value) > 150) {
			err = 'Invalid age.'
			valid = false;
		}
		if (!valid)
			alert(err);
		return valid;
	}

	editButton.addEventListener("click", function () {
		inputs.forEach(input => input.removeAttribute("disabled"));
		saveButton.style.display = "block";
		editButton.style.display = "none";
	});

	saveButton.addEventListener("click", async function () {
		if (!validateForm())
			return;
		if (!await uploadImage())
			return;
		if (!await updateProfile())
			return;
		inputs.forEach(input => input.setAttribute("disabled", true));
		saveButton.style.display = "none";
		editButton.style.display = "block";
	});
});
