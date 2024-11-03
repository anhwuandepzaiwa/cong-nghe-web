const body = document.querySelector("body"),
  sidebar = body.querySelector("nav"),
  toggle = body.querySelector(".toggle"),
  modeSwitch = body.querySelector(".toggle-switch"),
  modeText = body.querySelector(".mode-text");

toggle.addEventListener("click", () => {
  sidebar.classList.toggle("close");
});


modeSwitch.addEventListener("click", () => {
  body.classList.toggle("dark");

  if (body.classList.contains("dark")) {
    modeText.innerText = "Light mode";
  } else {
    modeText.innerText = "Dark mode";
  }
});

// JavaScript để hiển thị/ẩn form
const createProgramBtn = document.getElementById('createProgramBtn');
const createProgramForm = document.getElementById('createProgramForm');
const cancelBtn = document.getElementById('cancelBtn');

createProgramBtn.addEventListener('click', () => {
    createProgramForm.style.display = 'block'; // Hiển thị form
});

document.getElementById('cancelBtn').addEventListener('click', function() {
  document.getElementById('createProgramForm').reset(); // Reset form nếu muốn
});