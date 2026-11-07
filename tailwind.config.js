/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./*.php",
    "./components/**/*.php",
    "./include/**/*.php",
    "./adminpanel/**/*.php",
    "./assets/js/**/*.js",
  ],
  ...require("./assets/tailwind.theme.json"),
};
