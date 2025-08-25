function changeMainImage(imageSrc) {
  const main = document.getElementById("main-image");
  if (!main) return;
  main.src = imageSrc;

  document.querySelectorAll(".thumbnail").forEach((thumb) => {
    thumb.classList.remove("active");
    const a = new URL(thumb.src, window.location.href);
    const b = new URL(imageSrc, window.location.href);
    if (a.pathname.split("/").pop() === b.pathname.split("/").pop()) {
      thumb.classList.add("active");
    }
  });
}

document.addEventListener("DOMContentLoaded", function () {
  const thumbnails = document.querySelectorAll(".thumbnail");
  if (thumbnails.length > 0) thumbnails[0].classList.add("active");

  const monthYearEl = document.getElementById("month-year");
  const calendarGrid = document.getElementById("calendar-grid");
  const prevMonthBtn = document.getElementById("prev-month");
  const nextMonthBtn = document.getElementById("next-month");
  const todayBtn = document.getElementById("today");
  const bookingPanel = document.getElementById("booking-panel");
  const selectedDateEl = document.getElementById("selected-date");
  const timeSlots = Array.from(document.querySelectorAll(".time-slot"));
  const bookBtn = document.getElementById("book-btn");

  let current = new Date();
  let selectedDate = null;
  let selectedSlot = null;
  let bookingMap = {};
  let refreshInterval = null;

  const basePrice = chaletData?.price ? Number(chaletData.price) : 0;
  const monthNames = [
    "January",
    "February",
    "March",
    "April",
    "May",
    "June",
    "July",
    "August",
    "September",
    "October",
    "November",
    "December",
  ];

  function ymd(date) {
    const y = date.getFullYear();
    const m = String(date.getMonth() + 1).padStart(2, "0");
    const d = String(date.getDate()).padStart(2, "0");
    return `${y}-${m}-${d}`;
  }

  function ymdFromParts(year, month0, day) {
    const m = String(month0 + 1).padStart(2, "0");
    const d = String(day).padStart(2, "0");
    return `${year}-${m}-${d}`;
  }

  function getDateBookings(key) {
    const defaults = { MORNING: true, EVENING: true, FULL_DAY: true };
    return bookingMap[key] || defaults;
  }

  function priceForSlot(slot) {
    return slot === "FULL_DAY" ? basePrice * 2 : basePrice;
  }

  function setSlotLabels() {
    const labels = {
      MORNING: `Morning (8am-8pm) - $${priceForSlot("MORNING")}`,
      EVENING: `Evening (8pm-8am) - $${priceForSlot("EVENING")}`,
      FULL_DAY: `Full Day - $${priceForSlot("FULL_DAY")}`,
    };
    timeSlots.forEach((el) => {
      const s = el.dataset.slot;
      if (labels[s]) el.textContent = labels[s];
    });
  }

  function clearGridDays() {
    while (calendarGrid.children.length > 7) {
      calendarGrid.removeChild(calendarGrid.lastChild);
    }
  }

  function dayCellClassFor(dateKey) {
    const b = getDateBookings(dateKey);
    const allTrue = b.MORNING && b.EVENING && b.FULL_DAY;
    const allFalse = !b.MORNING && !b.EVENING && !b.FULL_DAY;

    if (allTrue) return "available";
    if (allFalse) return "fully-booked";
    return "partially-available";
  }

  async function refreshBookingData() {
    try {
      const response = await fetch(`getBookings.php?chaletId=${chaletData.id}`);
      if (!response.ok) throw new Error("Network response was not ok");

      const newBookings = await response.json();

      bookingMap = newBookings;

      renderCalendar(current);

      if (selectedDate) {
        const dateKey = ymd(selectedDate);
        const b = getDateBookings(dateKey);

        timeSlots.forEach((slotEl) => {
          slotEl.classList.remove("selected", "unavailable");
          const slotType = slotEl.dataset.slot;
          if (b[slotType] === false) {
            slotEl.classList.add("unavailable");
          }
        });
      }

      return true;
    } catch (error) {
      console.error("Failed to refresh booking data:", error);
      return false;
    }
  }

  function renderCalendar(monthDate = current) {
    if (!monthYearEl || !calendarGrid) return;

    const year = monthDate.getFullYear();
    const month0 = monthDate.getMonth();
    monthYearEl.textContent = `${monthNames[month0]} ${year}`;

    clearGridDays();

    const firstDayIndex = new Date(year, month0, 1).getDay();
    const daysInMonth = new Date(year, month0 + 1, 0).getDate();
    const prevMonthDays = new Date(year, month0, 0).getDate();

    for (let i = 0; i < firstDayIndex; i++) {
      const dayEl = document.createElement("div");
      dayEl.className = "day-cell empty";
      dayEl.innerHTML = `<div class="day-number">${
        prevMonthDays - firstDayIndex + i + 1
      }</div>`;
      calendarGrid.appendChild(dayEl);
    }

    const today = new Date();
    for (let day = 1; day <= daysInMonth; day++) {
      const dayEl = document.createElement("div");
      dayEl.className = "day-cell";

      const isToday =
        day === today.getDate() &&
        month0 === today.getMonth() &&
        year === today.getFullYear();
      const dateKey = ymdFromParts(year, month0, day);
      const statusClass = dayCellClassFor(dateKey);

      if (isToday) dayEl.classList.add("today");
      dayEl.classList.add(statusClass);
      dayEl.dataset.date = dateKey;

      dayEl.innerHTML = `
        <div class="day-number">${day}</div>
        <div class="tooltip">Click to view availability</div>
      `;

      dayEl.addEventListener("click", () => {
        if (dayEl.classList.contains("fully-booked")) return;

        selectedDate = new Date(year, month0, day);
        selectedSlot = null;

        if (selectedDateEl) {
          selectedDateEl.textContent = selectedDate.toLocaleDateString(
            "en-US",
            {
              month: "long",
              day: "numeric",
              year: "numeric",
            }
          );
        }

        const b = getDateBookings(dateKey);
        timeSlots.forEach((slotEl) => {
          slotEl.classList.remove("selected", "unavailable");
          const slotType = slotEl.dataset.slot;
          if (b[slotType] === false) {
            slotEl.classList.add("unavailable");
          }
        });

        if (bookingPanel) bookingPanel.style.display = "block";
      });

      calendarGrid.appendChild(dayEl);
    }

    const totalCells = firstDayIndex + daysInMonth;
    const remain = totalCells % 7 === 0 ? 0 : 7 - (totalCells % 7);
    for (let i = 1; i <= remain; i++) {
      const dayEl = document.createElement("div");
      dayEl.className = "day-cell empty";
      dayEl.innerHTML = `<div class="day-number">${i}</div>`;
      calendarGrid.appendChild(dayEl);
    }
  }

  timeSlots.forEach((slotEl) => {
    slotEl.addEventListener("click", function () {
      if (this.classList.contains("unavailable")) return;
      timeSlots.forEach((s) => s.classList.remove("selected"));
      this.classList.add("selected");
      selectedSlot = this.dataset.slot;
    });
  });

  if (bookBtn) {
    bookBtn.addEventListener("click", async function () {
      if (!selectedDate) {
        alert("Please select a date.");
        return;
      }
      if (!selectedSlot) {
        alert("Please select a time slot.");
        return;
      }

      const dateKey = ymd(selectedDate);

      const refreshed = await refreshBookingData();
      if (!refreshed) {
        alert("Could not verify availability. Please try again.");
        return;
      }

      const b = getDateBookings(dateKey);
      if (b[selectedSlot] === false) {
        alert(
          "This slot was just booked by someone else. Please select another time."
        );
        refreshBookingData();
        return;
      }

      const body = new URLSearchParams({
        chalet_id: String(chaletData.id),
        date: dateKey,
        slot: selectedSlot,
      }).toString();

      try {
        const response = await fetch("booking.php", {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body,
        });

        const data = await response.json();

        if (data.status === "success") {
          alert(`Booking confirmed! Price: $${data.price}`);
          await refreshBookingData();
          if (bookingPanel) bookingPanel.style.display = "none";
        } else {
          alert(data.message || "Booking failed.");
          await refreshBookingData();
        }
      } catch (err) {
        console.error(err);
        alert("Unexpected error. Please try again.");
      }
    });
  }

  if (prevMonthBtn) {
    prevMonthBtn.addEventListener("click", () => {
      current.setMonth(current.getMonth() - 1);
      renderCalendar(current);
    });
  }

  if (nextMonthBtn) {
    nextMonthBtn.addEventListener("click", () => {
      current.setMonth(current.getMonth() + 1);
      renderCalendar(current);
    });
  }

  if (todayBtn) {
    todayBtn.addEventListener("click", () => {
      current = new Date();
      renderCalendar(current);
    });
  }

  async function initializeCalendar() {
    await refreshBookingData();

    refreshInterval = setInterval(refreshBookingData, 120000);

    setSlotLabels();
    renderCalendar(current);
  }

  initializeCalendar();

  window.addEventListener("beforeunload", () => {
    if (refreshInterval) clearInterval(refreshInterval);
  });
});
function toggleReviewForm() {
  const reviewForm = document.getElementById("review-form");
  if (reviewForm.style.display === "block") {
    reviewForm.style.display = "none";
  } else {
    reviewForm.style.display = "block";
    reviewForm.scrollIntoView({ behavior: "smooth" });
  }
}
