window.addEventListener("DOMContentLoaded", () => {
  const iframeCourses = document.getElementById("courses");
  if (iframeCourses) {
    iframeCourses.onload = () => {
      try {
        const links = iframeCourses.contentDocument.querySelectorAll("a");
        links.forEach((link) => {
          link.setAttribute("target", "_top");
        });
      } catch (e) {
        console.warn("Could not access iframe content (CORS or browser restriction).");
      }
    };
  }
});
const hamburger = document.querySelector(".hamburger");
const navMenu = document.querySelector(".nav-menu");
hamburger.addEventListener("click", () => {
  hamburger.classList.toggle("active");
  navMenu.classList.toggle("active");
});
function hideElements() {
  const idsToHide = ["contact-us", "contact-link", "nav-login-link", "nav-register-link", "footer-buttons", "fqc", "fqc2"];
  const idsToShow = ["fkmau", "fkmau2"];
  idsToHide.forEach((id) => {
    const el = document.getElementById(id);
    if (el) el.style.display = "none";
  });
  idsToShow.forEach((id) => {
    const el = document.getElementById(id);
    if (el) el.style.display = "block";
  });
}
window.addEventListener("DOMContentLoaded", () => {
  const iframe = document.getElementById("contact-us");
  fetch("contact.php")
    .then((r) => r.text())
    .then((html) => {
      if (!html || html.length < 20 || html.includes("<?php") || html.includes("Fatal error")) {
        hideElements();
      } else if (iframe) {
        iframe.src = "contact.php";
        iframe.style.display = "block";
      }
    })
    .catch(() => hideElements());
});
