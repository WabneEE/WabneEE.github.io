window.addEventListener("DOMContentLoaded", () => {
  const t = document.getElementById("courses");
  t &&
    (t.onload = () => {
      try {
        t.contentDocument.querySelectorAll("a").forEach((t) => {
          t.setAttribute("target", "_top");
        });
      } catch (t) {
        console.warn(
          "Could not access iframe content (CORS or browser restriction).",
        );
      }
    });
});
const t = document.querySelector(".hamburger"),
  e = document.querySelector(".nav-menu");
function hideElements() {
  [
    "contact-us",
    "contact-link",
    "nav-login-link",
    "nav-register-link",
    "footer-buttons",
    "fqc",
    "fqc2",
  ].forEach((t) => {
    const e = document.getElementById(t);
    e && (e.style.display = "none");
  }),
    ["fkmau", "fkmau2"].forEach((t) => {
      const e = document.getElementById(t);
      e && (e.style.display = "block");
    });
}
t.addEventListener("click", () => {
  t.classList.toggle("active"), e.classList.toggle("active");
}),
  window.addEventListener("DOMContentLoaded", () => {
    const t = document.getElementById("contact-us");
    fetch("contact.php")
      .then((t) => t.text())
      .then((e) => {
        !e || e.length < 20 || e.includes("<?php") || e.includes("Fatal error")
          ? hideElements()
          : t && ((t.src = "contact.php"), (t.style.display = "block"));
      })
      .catch(() => hideElements());
  });
