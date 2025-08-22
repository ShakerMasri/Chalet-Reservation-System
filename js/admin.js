document.addEventListener("DOMContentLoaded", function () {
  const fileInput = document.getElementById("image-upload");
  const uploadLabel = document.querySelector(".image-upload-label");

  if (!fileInput || !uploadLabel) {
    console.error("Error: Could not find file input or label.");
    return;
  }

  const preview = document.createElement("div");
  preview.id = "preview";
  preview.style.display = "flex";
  preview.style.flexWrap = "wrap";
  preview.style.gap = "8px";
  fileInput.parentNode.appendChild(preview);

  uploadLabel.addEventListener("dragover", (e) => {
    e.preventDefault();
    uploadLabel.classList.add("dragover");
  });

  uploadLabel.addEventListener("dragleave", () => {
    uploadLabel.classList.remove("dragover");
  });

  uploadLabel.addEventListener("drop", (e) => {
    e.preventDefault();
    uploadLabel.classList.remove("dragover");
    handleFiles(e.dataTransfer.files);
  });

  fileInput.addEventListener("change", () => {
    handleFiles(fileInput.files);
  });

  function handleFiles(files) {
    Array.from(files).forEach((file) => {
      if (!file.type.startsWith("image/")) {
        alert(`"${file.name}" is not an image`);
        return;
      }

      const reader = new FileReader();
      reader.onload = (e) => {
        const img = document.createElement("img");
        img.src = e.target.result;
        img.style.maxWidth = "80px";
        img.style.maxHeight = "80px";
        img.style.objectFit = "cover";
        img.style.borderRadius = "6px";
        img.style.border = "2px solid #ccc";
        preview.appendChild(img);
      };
      reader.readAsDataURL(file);
    });
  }
});

function logout() {
  if (confirm("Are you sure you want to log out?")) {
    fetch("./html/logout.php").then(
      () => (window.location.href = "./html/login.php")
    );
  }
}
function logout2() {
  if (confirm("Are you sure you want to log out?")) {
    fetch("logout.php").then(() => (window.location.href = "login.php"));
  }
}
