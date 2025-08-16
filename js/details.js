function getUrlParameter(name) {
  const urlParams = new URLSearchParams(window.location.search);
  return urlParams.get(name);
}

function renderDetailView(chaletData) {
  const commentsHTML = chaletData.comments
    .map(
      (comment) => `
            <div class="comment">
            <div class="comment-header">
            <span class="comment-user">${comment.user}</span>
            <div class="comment-rating">${ratingStars(comment.rating)}</div>
            <span class="comment-date">${comment.date}</span>
             </div>
             <p class="comment-text">${comment.comment}</p>
              </div>
              `
    )
    .join("");

  const featuresHTML = chaletData.amenities
    .map(
      (feature) => `
                        <span class="feature-tag">${feature}</span>
                    `
    )
    .join("");

  const rentalOptionsHTML = rentalHours
    .map((hours) => {
      const totalPrice = (chaletData.price * hours).toFixed(2);
      const days = hours >= 24 ? Math.floor(hours / 24) : 0;
      const remainingHours = hours % 24;
      let timeLabel = "";

      if (days > 0) {
        timeLabel = `${days} day${days > 1 ? "s" : ""}`;
        if (remainingHours > 0) {
          timeLabel += ` ${remainingHours}h`;
        }
      } else {
        timeLabel = `${hours} hours`;
      }

      return `
                            <div class="rental-option" data-hours="${hours}" data-price="${totalPrice}">
                                <div class="rental-time">${timeLabel}</div>
                                <div class="rental-price">$${totalPrice}</div>
                            </div>
                        `;
    })
    .join("");

  return `
                <div class="modal-header">
                    <h1>${chaletData.name}</h1>
                    <div class="rating">
                        <div class="stars">
                            ${ratingStars(chaletData.rating)}
                        </div>
                        <span class="rating-value">${chaletData.rating} (${
    chaletData.comments.length
  } reviews)</span>
                    </div>
                </div>

                <div class="modal-body">
                    <div class="modal-images">
                        <div class="main-image">
                            <img src="${chaletData.images[0]}" alt="${
    chaletData.name
  }" id="mainImage">
                        </div>
                        <div class="thumbnail-images">
                            ${chaletData.images
                              .map(
                                (img, index) => `
                                    <img src="${img}" alt="${chaletData.name} ${
                                  index + 1
                                }" 
                                         class="thumbnail ${
                                           index === 0 ? "active" : ""
                                         }" 
                                         onclick="changeMainImage('${img}', this)">
                                `
                              )
                              .join("")}
                        </div>
                    </div>

                    <div class="moadal-info">
                        <div class="description-section">
                            <h3>Description</h3>
                            <p>${chaletData.description}</p>
                        </div>

                        <div class="features-section">
                            <h3>Features</h3>
                            <div class="features-list">
                                ${featuresHTML}
                            </div>
                        </div>
              <div class="reservation-section">
            <h3>Make a Reservation</h3>
            
            <div class="calendar-header">
                <div class="nav-buttons">
                    <button id="prev-month">&lt;</button>
                    <button id="today">Today</button>
                </div>
                <div class="month-year" id="month-year">June 2023</div>
                <div class="nav-buttons">
                    <button id="next-month">&gt;</button>
                </div>
            </div>
            
            <div class="calendar-grid" id="calendar-grid">
                <div class="day-header">Sun</div>
                <div class="day-header">Mon</div>
                <div class="day-header">Tue</div>
                <div class="day-header">Wed</div>
                <div class="day-header">Thu</div>
                <div class="day-header">Fri</div>
                <div class="day-header">Sat</div>
            </div>
            
            <div class="legend">
                <div class="legend-item">
                    <div class="legend-color" style="background-color: #d4edda; border: 1px solid #28a745;"></div>
                    <span>Available</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background-color: #fff3cd; border: 1px solid #ffc107;"></div>
                    <span>Partially Booked</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background-color: #f8d7da; border: 1px solid #dc3545;"></div>
                    <span>Fully Booked</span>
                </div>
            </div>
            
            <div class="booking-panel" id="booking-panel">
                <h3>Book for <span id="selected-date">June 15, 2023</span></h3>
                <div class="time-slots">
                    <div class="time-slot" data-slot="morning">Morning (8am-8pm) - $100</div>
                    <div class="time-slot" data-slot="night">Night (8pm-8am) - $120</div>
                    <div class="time-slot" data-slot="full">Full Day - $200</div>
                </div>
                <div class="booking-details">
                    <button class="book-btn" id="book-btn">Book Now</button>
                </div>
            </div>
        </div>

                        
                            <button class="rent-button" onclick="rentItem(${
                              chaletData.id
                            })">Rent Now</button>
                        </div>

                        <div class="comments-section">
                            <h3>Customer Reviews</h3>
                            <div class="comments-list">
                                ${commentsHTML}
                            </div>
                        </div>
                    </div>
                </div>
            `;
}

document.addEventListener("DOMContentLoaded", () => {
  const chaletId = getUrlParameter("id");

  if (!chaletId) {
    document.getElementById("chaletDetails").innerHTML =
      "<p>No chalet ID provided.</p>";
    return;
  }

  const chalet = chaletData.find((item) => item.id == chaletId);

  if (!chalet) {
    document.getElementById("chaletDetails").innerHTML =
      "<p>Chalet not found.</p>";
    return;
  }

  document.getElementById("chaletDetails").innerHTML = renderDetailView(chalet);
  addRentalOptionListeners();
  document.title = `${chalet.name} - Chalet Details`;

  let currentDate = new Date();
  let currentMonth = currentDate.getMonth();
  let currentYear = currentDate.getFullYear();

  const bookings = {
    "2023-6-15": { morning: false, night: true, full: false },
    "2023-6-16": { morning: true, night: true, full: true },
    "2023-6-20": { morning: false, night: false, full: true },
    "2023-6-25": { morning: true, night: false, full: false },
  };

  let wishlist = ["2023-6-10", "2023-6-18"];

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

  renderCalendar(currentMonth, currentYear);

  prevMonthBtn.addEventListener("click", () => {
    currentMonth--;
    if (currentMonth < 0) {
      currentMonth = 11;
      currentYear--;
    }
    renderCalendar(currentMonth, currentYear);
  });

  nextMonthBtn.addEventListener("click", () => {
    currentMonth++;
    if (currentMonth > 11) {
      currentMonth = 0;
      currentYear++;
    }
    renderCalendar(currentMonth, currentYear);
  });

  todayBtn.addEventListener("click", () => {
    currentDate = new Date();
    currentMonth = currentDate.getMonth();
    currentYear = currentDate.getFullYear();
    renderCalendar(currentMonth, currentYear);
  });

  timeSlots.forEach((slot) => {
    slot.addEventListener("click", function () {
      if (this.classList.contains("unavailable")) return;
      timeSlots.forEach((s) => s.classList.remove("selected"));
      this.classList.add("selected");
      selectedSlot = this.dataset.slot;
    });
  });

  bookBtn.addEventListener("click", function () {
    if (!selectedDate || !selectedSlot) {
      alert("Please select a date and time slot");
      return;
    }

    alert(
      `Booking confirmed for ${selectedDateElement.textContent} (${selectedSlot})`
    );
    bookingPanel.style.display = "none";
  });

  function renderCalendar(month, year) {
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

      const isWishlisted = wishlist.includes(dateKey);

      if (isToday) dayElement.classList.add("today");
      dayElement.classList.add(availabilityClass);
      if (isWishlisted) dayElement.classList.add("wishlisted");

      dayElement.dataset.date = dateKey;

      dayElement.innerHTML = `
                <div class="day-number">${i}</div>
                <div class="wishlist-heart ${
                  isWishlisted ? "active" : ""
                }">♥</div>
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
        selectedDateElement.textContent = formattedDate;

        timeSlots.forEach((slot) => {
          const slotType = slot.dataset.slot;
          if (dateBookings[slotType] === false) {
            slot.classList.add("unavailable");
          } else {
            slot.classList.remove("unavailable");
          }
          slot.classList.remove("selected");
        });

        selectedSlot = null;

        bookingPanel.style.display = "block";
      });

      const heart = dayElement.querySelector(".wishlist-heart");
      heart.addEventListener("click", function (e) {
        e.stopPropagation();
        this.classList.toggle("active");
        dayElement.classList.toggle("wishlisted");

        const dateKey = dayElement.dataset.date;
        if (this.classList.contains("active")) {
          if (!wishlist.includes(dateKey)) {
            wishlist.push(dateKey);
          }
        } else {
          wishlist = wishlist.filter((date) => date !== dateKey);
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
