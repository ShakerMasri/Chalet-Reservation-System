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
function filterTable() {
  const searchInput = document.getElementById("table-search");
  if (!searchInput) return;

  const filter = searchInput.value.toLowerCase();
  let tableBody;

  if (document.getElementById("chalet-table-body")) {
    tableBody = document.getElementById("chalet-table-body");
  } else if (document.getElementById("owner-table-body")) {
    tableBody = document.getElementById("owner-table-body");
  } else {
  }

  const rows = tableBody.getElementsByTagName("tr");
  const searchColumn = tableBody.id === "owner-table-body" ? 1 : 1;

  for (let i = 0; i < rows.length; i++) {
    const nameCell = rows[i].getElementsByTagName("td")[searchColumn];
    if (nameCell) {
      const name = nameCell.textContent || nameCell.innerText;
      if (name.toLowerCase().includes(filter)) {
        rows[i].style.display = "";
      } else {
        rows[i].style.display = "none";
      }
    }
  }
}

let currentSortColumn = -1;
let sortDirection = 1;

function sortTable(columnIndex) {
  let tableBody;

  if (document.getElementById("chalet-table-body")) {
    tableBody = document.getElementById("chalet-table-body");
  } else if (document.getElementById("owner-table-body")) {
    tableBody = document.getElementById("owner-table-body");
    if (columnIndex > 1) return;
  } else {
    return;
  }

  const rows = Array.from(tableBody.querySelectorAll("tr"));

  document.querySelectorAll(".sort-icon").forEach((icon) => {
    icon.classList.remove("active", "asc", "desc");
  });

  if (currentSortColumn === columnIndex) {
    sortDirection *= -1;
  } else {
    currentSortColumn = columnIndex;
    sortDirection = 1;
  }

  const sortIcon = document.getElementById(`sort-icon-${columnIndex}`);
  if (sortIcon) {
    sortIcon.classList.add("active");
    sortIcon.classList.add(sortDirection === 1 ? "asc" : "desc");
  }

  rows.sort((a, b) => {
    let aValue, bValue;

    const aCell = a.cells[columnIndex];
    const bCell = b.cells[columnIndex];

    aValue = aCell.getAttribute("data-sort-value") || aCell.textContent;
    bValue = bCell.getAttribute("data-sort-value") || bCell.textContent;

    if (columnIndex === 0 || columnIndex === 4 || columnIndex === 5) {
      aValue = isNaN(aValue) ? aValue : Number(aValue);
      bValue = isNaN(bValue) ? bValue : Number(bValue);
    }

    if (columnIndex === 6 && aValue === "N/A") aValue = 0;
    if (columnIndex === 6 && bValue === "N/A") bValue = 0;

    if (aValue < bValue) return -1 * sortDirection;
    if (aValue > bValue) return 1 * sortDirection;
    return 0;
  });

  while (tableBody.firstChild) {
    tableBody.removeChild(tableBody.firstChild);
  }

  rows.forEach((row) => tableBody.appendChild(row));
}
