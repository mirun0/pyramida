const filmId = document.getElementById("film_id").value;
const filmLength = document.getElementById("film_length").value;
const screeningId = document.getElementById("screening_id").value;
const screeningTime = document.getElementById("screening_time").value;

const date_input = document.getElementById("date");
const hall_select = document.getElementById("hall");
const time_select = document.getElementById("time");

const first_screening_time = "8:00";
const last_screening_time = "23:00";
const gap_between_screenings = 15;

async function getOccupiedTimesForScreening(date, hallId) {
    const res = await fetch('occupied_time_screening.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded', // nebo application/json
        },
        body: new URLSearchParams({
            date: date,
            hallId: hallId
        })
    })
    const response = await res.json();
    if (response.ok === true && Array.isArray(response.times)) {
        return response.times;
    } else {
        return [];
    }
}

function getTimeString(time) {
    return time.getHours().toString().padStart(2, '0') + ":" + time.getMinutes().toString().padStart(2, '0');
}

function createTimeElementOption(timeStr) {
    const option = document.createElement("option");
    if (timeStr === screeningTime) option.selected = true;
    option.value = timeStr;
    option.textContent = timeStr;
    return option;
}

function createTimeOptions(occupiedTimes) {
    const [startHour, startMinute] = first_screening_time.split(":").map(Number);

    const startTime = new Date(0);
    startTime.setHours(startHour);
    startTime.setMinutes(startMinute);
    let startTimeStr = getTimeString(startTime);

    const endTime = new Date(0);
    endTime.setHours(startHour);
    endTime.setMinutes(startMinute + filmLength);
    let endTimeStr = getTimeString(endTime);

    while (startTimeStr <= last_screening_time) {
        let doesOverlap = false;
        occupiedTimes.forEach(occTime => {
            if (occTime.screeningId != screeningId && startTimeStr < occTime.endTime && endTimeStr > occTime.startTime) doesOverlap = true;
        });

        if (!doesOverlap) {
            time_select.appendChild(createTimeElementOption(startTimeStr));
        }

        startTime.setMinutes(startTime.getMinutes() + gap_between_screenings);
        endTime.setMinutes(endTime.getMinutes() + gap_between_screenings);
        startTimeStr = getTimeString(startTime);
        endTimeStr = getTimeString(endTime);
    }
}

async function refreshTimes() {
    const date = date_input.value;
    const hallId = hall_select.value;
    if (date && hallId) {
        const occupiedTimes = await getOccupiedTimesForScreening(date, hallId);
        createTimeOptions(occupiedTimes);
    }
}

refreshTimes();

date_input.addEventListener("change", () => {
    time_select.innerHTML = "";
    refreshTimes();
});
hall_select.addEventListener("change", () => {
    time_select.innerHTML = "";
    refreshTimes();
});