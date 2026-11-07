document.addEventListener("DOMContentLoaded", function () {
  const toggleButton = document.querySelector(
    '[data-collapse-toggle="navbar-sticky"]'
  );
  const menu = document.getElementById("navbar-sticky");

  // Toggle mobile menu
  toggleButton.addEventListener("click", function (event) {
    event.stopPropagation();
    menu.classList.toggle("hidden");
    this.setAttribute(
      "aria-expanded",
      menu.classList.contains("hidden") ? "false" : "true"
    );
  });

  // Close mobile menu when clicking outside
  document.addEventListener("click", function (event) {
    if (!menu.contains(event.target) && !toggleButton.contains(event.target)) {
      menu.classList.add("hidden");
      toggleButton.setAttribute("aria-expanded", "false");
    }
  });

  // Dropdown functionality
  const dropdowns = document.querySelectorAll(".dropdown");

  dropdowns.forEach((dropdown) => {
    const dropdownToggle = dropdown.querySelector(".dropdown-link");
    const dropdownMenu = dropdown.querySelector(".dropdown-menu");

    if (!dropdownToggle || !dropdownMenu) return;

    let timeout;

    function openDropdown() {
      clearTimeout(timeout);
      dropdownMenu.classList.remove("hidden");
      dropdownToggle.setAttribute("aria-expanded", "true");
    }

    function closeDropdown() {
      timeout = setTimeout(() => {
        dropdownMenu.classList.add("hidden");
        dropdownToggle.setAttribute("aria-expanded", "false");
      }, 200);
    }

    // Desktop: Open on hover
    dropdownToggle.addEventListener("mouseenter", openDropdown);
    dropdown.addEventListener("mouseleave", closeDropdown);

    // Mobile: Open on click
    dropdownToggle.addEventListener("click", function (event) {
      // Only prevent default on mobile if clicking inside the dropdown link (to avoid opening the link)
      if (event.target !== dropdownToggle) {
        event.stopPropagation(); // Prevent closing the dropdown when clicked outside the dropdown link
        dropdownMenu.classList.toggle("hidden");
        dropdownToggle.setAttribute(
          "aria-expanded",
          dropdownMenu.classList.contains("hidden") ? "false" : "true"
        );
      }
    });

    dropdownMenu.addEventListener("mouseenter", openDropdown);
  });

  // Close dropdowns when clicking outside
  document.addEventListener("click", function (event) {
    dropdowns.forEach((dropdown) => {
      const dropdownMenu = dropdown.querySelector(".dropdown-menu");
      if (dropdownMenu && !dropdown.contains(event.target)) {
        dropdownMenu.classList.add("hidden");
        dropdown
          .querySelector(".dropdown-link")
          .setAttribute("aria-expanded", "false");
      }
    });
  });

  // Navbar active link highlight
  const currentUrl = window.location.href;
  const links = document.querySelectorAll(".nav-menu a");

  links.forEach((link) => {
    if (link.href === currentUrl) {
      link.classList.add("text-primary-600");
    }
  });
});
