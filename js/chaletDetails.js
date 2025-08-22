function changeMainImage(imageSrc) {
  document.getElementById("main-image").src = imageSrc;

  document.querySelectorAll(".thumbnail").forEach((thumb) => {
    thumb.classList.remove("active");
    if (thumb.src.includes(imageSrc)) {
      thumb.classList.add("active");
    }
  });
}

document.addEventListener("DOMContentLoaded", function () {
  const thumbnails = document.querySelectorAll(".thumbnail");
  if (thumbnails.length > 0) {
    thumbnails[0].classList.add("active");
  }

  let currentDate = new Date();
  let currentMonth = currentDate.getMonth();
  let currentYear = currentDate.getFullYear();

  const monthYearElement = document.getElementById("month-year");
  const calendarGrid = document.getElementById("calendar-grid");
  const prevMonthBtn = document.getElementById("prev-month");
  const nextMonthBtn = document.getElementById("next-month");
  const todayBtn = document.getElementById("today");
  const bookingPanel = document.getElementById("booking-panel");
  const selectedDateElement = document.getElementById("selected-date");
  const timeSlots = document.querySelectorAll(".time-slot");
  const bookBtn = document.getElementById("book-btn");

  let selectedDate = null;
  let selectedSlot = null;

  // Sample booking data
  const bookings = {
    "2025-1-15": { morning: false, night: true, full: false },
    "2025-1-16": { morning: true, night: true, full: true },
    "2025-1-20": { morning: false, night: false, full: true },
    "2025-1-25": { morning: true, night: false, full: false },
  };

  renderCalendar(currentMonth, currentYear);

  if (prevMonthBtn) {
    prevMonthBtn.addEventListener("click", () => {
      currentMonth--;
      if (currentMonth < 0) {
        currentMonth = 11;
        currentYear--;
      }
      renderCalendar(currentMonth, currentYear);
    });
  }

  if (nextMonthBtn) {
    nextMonthBtn.addEventListener("click", () => {
      currentMonth++;
      if (currentMonth > 11) {
        currentMonth = 0;
        currentYear++;
      }
      renderCalendar(currentMonth, currentYear);
    });
  }

  if (todayBtn) {
    todayBtn.addEventListener("click", () => {
      currentDate = new Date();
      currentMonth = currentDate.getMonth();
      currentYear = currentDate.getFullYear();
      renderCalendar(currentMonth, currentYear);
    });
  }

  if (timeSlots) {
    timeSlots.forEach((slot) => {
      slot.addEventListener("click", function () {
        if (this.classList.contains("unavailable")) return;

        timeSlots.forEach((s) => s.classList.remove("selected"));
        this.classList.add("selected");
        selectedSlot = this.dataset.slot;
      });
    });
  }

  if (bookBtn) {
    bookBtn.addEventListener("click", function () {
      if (!selectedDate || !selectedSlot) {
        alert("Please select a date and time slot");
        return;
      }

      alert(
        `Booking confirmed for ${selectedDateElement.textContent} (${selectedSlot})`
      );
      if (bookingPanel) {
        bookingPanel.style.display = "none";
      }
    });
  }

  const addReviewBtn = document.getElementById("add-review-btn");
  const reviewForm = document.getElementById("review-form");

  if (addReviewBtn && reviewForm) {
    addReviewBtn.addEventListener("click", function () {
      reviewForm.style.display =
        reviewForm.style.display === "block" ? "none" : "block";
    });
  }

  function renderCalendar(month, year) {
    if (!monthYearElement || !calendarGrid) return;

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
    monthYearElement.textContent = `${monthNames[month]} ${year}`;

    while (calendarGrid.children.length > 7) {
      calendarGrid.removeChild(calendarGrid.lastChild);
    }

    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const prevMonthDays = new Date(year, month, 0).getDate();

    for (let i = 0; i < firstDay; i++) {
      const dayElement = document.createElement("div");
      dayElement.className = "day-cell empty";
      dayElement.innerHTML = `<div class="day-number">${
        prevMonthDays - firstDay + i + 1
      }</div>`;
      calendarGrid.appendChild(dayElement);
    }

    const today = new Date();
    for (let i = 1; i <= daysInMonth; i++) {
      const dayElement = document.createElement("div");
      dayElement.className = "day-cell";

      const isToday =
        i === today.getDate() &&
        month === today.getMonth() &&
        year === today.getFullYear();
      const dateKey = `${year}-${month + 1}-${i}`;

      const dateBookings = bookings[dateKey] || {
        morning: true,
        night: true,
        full: true,
      };

      let availabilityClass = "";
      if (!dateBookings.morning && !dateBookings.night && !dateBookings.full) {
        availabilityClass = "fully-booked";
      } else if (
        dateBookings.morning &&
        dateBookings.night &&
        dateBookings.full
      ) {
        availabilityClass = "available";
      } else {
        availabilityClass = "partially-available";
      }

      if (isToday) dayElement.classList.add("today");
      dayElement.classList.add(availabilityClass);

      dayElement.dataset.date = dateKey;

      dayElement.innerHTML = `
                <div class="day-number">${i}</div>
                <div class="tooltip">Click to view availability</div>
            `;

      dayElement.addEventListener("click", function () {
        if (this.classList.contains("fully-booked")) return;

        selectedDate = new Date(year, month, i);
        const formattedDate = selectedDate.toLocaleDateString("en-US", {
          month: "long",
          day: "numeric",
          year: "numeric",
        });

        if (selectedDateElement) {
          selectedDateElement.textContent = formattedDate;
        }

        if (timeSlots) {
          timeSlots.forEach((slot) => {
            const slotType = slot.dataset.slot;
            if (dateBookings[slotType] === false) {
              slot.classList.add("unavailable");
            } else {
              slot.classList.remove("unavailable");
            }
            slot.classList.remove("selected");
          });
        }

        selectedSlot = null;
        if (bookingPanel) {
          bookingPanel.style.display = "block";
        }
      });

      calendarGrid.appendChild(dayElement);
    }

    const totalCells = firstDay + daysInMonth;
    const remainingCells = totalCells % 7 === 0 ? 0 : 7 - (totalCells % 7);

    for (let i = 1; i <= remainingCells; i++) {
      const dayElement = document.createElement("div");
      dayElement.className = "day-cell empty";
      dayElement.innerHTML = `<div class="day-number">${i}</div>`;
      calendarGrid.appendChild(dayElement);
    }
  }
});
