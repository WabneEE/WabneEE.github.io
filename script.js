const hamburger = document.querySelector(".hamburger");
const navMenu = document.querySelector(".nav-menu");
hamburger.addEventListener("click", () => {
	hamburger.classList.toggle("active");
	navMenu.classList.toggle("active");
});
function hideElements() {
	document.getElementById("contact-us").style.display = "none";
	document.getElementById("contact-link").style.display = "none";
	document.getElementById("nav-login-link").style.display = "none";
	document.getElementById("nav-register-link").style.display = "none";
	document.getElementById("footer-buttons").style.display = "none";
	document.getElementById("fqc").style.display = "none";
	document.getElementById("fkmau").style.display = "block";
	document.getElementById("fqc2").style.display = "none";
	document.getElementById("fkmau2").style.display = "block";
}
window.addEventListener("DOMContentLoaded", () => {
	fetch("contact.php")
		.then((r) => r.text())
		.then((html) => {
			if (
				!html ||
				html.length < 20 ||
				html.includes("<?php") ||
				html.includes("Fatal error")
			) {
				hideElements();
			} else {
				const iframe = document.getElementById("contact-us");
				iframe.src = "contact.php";
				iframe.style.display = "block";
			}
		})
		.catch(() => hideElements());
});
window.addEventListener("DOMContentLoaded", () => {
	const iframeCourses = document.getElementById("courses");
	iframeCourses.onload = () => {
		try {
			const links = iframeCourses.contentDocument.querySelectorAll("a");
			links.forEach((link) => {
				link.setAttribute("target", "_top");
			});
		} catch (e) {
			console.warn(
				"Could not access iframe content (CORS or browser restriction).",
			);
		}
	};
});
