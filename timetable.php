<?php
session_start();

if (!isset($_SESSION['username'])) {
    
    header("Location: index.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
<title>Timetable Management</title>
<link rel="stylesheet" href="styles.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>

<div class="container">
    <div class="button-container" style="display: flex; justify-content:space-between;;">
        <i class="fas fa-arrow-left back-button" style="font-size: 24px; color: rgb(242, 238, 27); cursor: pointer; margin-right: 10px;" onclick="goBack()" title="Back"></i>
        <i class="fas fa-home home-button" style="font-size: 24px; color: rgb(242, 238, 27); cursor: pointer; margin-right: 10px;" onclick="goHome()" title="Home"></i>
        </div>
    <h2>Timetable Management</h2>
    <div id="timetable"></div>
    <div id="form-container">
        <form id="timetable-form">
            <div class="form-group">
                <label for="day">Day:</label>
                <select name="day" id="day" class="day" style="height: 50px; font-size: 22px; padding: 10px 15px; border-radius: 5px; border: 1px solid #ccc; box-sizing: border-box; transition: border-color 0.3s ease;">
                    <option value="MONDAY">Monday</option>
                    <option value="TUESDAY">Tuesday</option>
                    <option value="WEDNESDAY">Wednesday</option>
                    <option value="THURSDAY">Thursday</option>
                    <option value="FRIDAY">Friday</option>
                </select>
            </div>

            <!-- Time Slot 1 -->
            <div class="form-group">
                <div id="time-slot-1" class="wrapper">
                    <div class="select-btn">
                        <span id="timeSlotsSpan1">Select Subject for Time Slot 1</span>
                        <i class="uil uil-angle-down"></i>
                    </div>
                    <div class="content">
                        <div class="search">
                            <i class="uil uil-search"></i>
                            <input id="timeSlotsInput1" spellcheck="false" type="text" placeholder="Search">
                        </div>
                        <ul class="options"></ul>
                    </div>
                </div>
            </div>

            <!-- Time Slot 2 -->
            <div class="form-group">
                <div id="time-slot-2" class="wrapper">
                    <div class="select-btn">
                        <span id="timeSlotsSpan2">Select Subject for Time Slot 2</span>
                        <i class="uil uil-angle-down"></i>
                    </div>
                    <div class="content">
                        <div class="search">
                            <i class="uil uil-search"></i>
                            <input id="timeSlotsInput2" spellcheck="false" type="text" placeholder="Search">
                        </div>
                        <ul class="options"></ul>
                    </div>
                </div>
            </div>

            <!-- Time Slot 3 -->
            <div class="form-group">
                <div id="time-slot-3" class="wrapper">
                    <div class="select-btn">
                        <span id="timeSlotsSpan3">Select Subject for Time Slot 3</span>
                        <i class="uil uil-angle-down"></i>
                    </div>
                    <div class="content">
                        <div class="search">
                            <i class="uil uil-search"></i>
                            <input id="timeSlotsInput3" spellcheck="false" type="text" placeholder="Search">
                        </div>
                        <ul class="options"></ul>
                    </div>
                </div>
            </div>

            <!-- Time Slot 4 -->
            <div class="form-group">
                <div id="time-slot-4" class="wrapper">
                    <div class="select-btn">
                        <span id="timeSlotsSpan4">Select Subject for Time Slot 4</span>
                        <i class="uil uil-angle-down"></i>
                    </div>
                    <div class="content">
                        <div class="search">
                            <i class="uil uil-search"></i>
                            <input id="timeSlotsInput4" spellcheck="false" type="text" placeholder="Search">
                        </div>
                        <ul class="options"></ul>
                    </div>
                </div>
            </div>

            <!-- Time Slot 5 -->
            <div class="form-group">
                <div id="time-slot-5" class="wrapper">
                    <div class="select-btn">
                        <span id="timeSlotsSpan5">Select Subject for Time Slot 5</span>
                        <i class="uil uil-angle-down"></i>
                    </div>
                    <div class="content">
                        <div class="search">
                            <i class="uil uil-search"></i>
                            <input id="timeSlotsInput5" spellcheck="false" type="text" placeholder="Search">
                        </div>
                        <ul class="options"></ul>
                    </div>
                </div>
            </div>

            <!-- Time Slot 6 -->
            <div class="form-group">
                <div id="time-slot-6" class="wrapper">
                    <div class="select-btn">
                        <span id="timeSlotsSpan6">Select Subject for Time Slot 6</span>
                        <i class="uil uil-angle-down"></i>
                    </div>
                    <div class="content">
                        <div class="search">
                            <i class="uil uil-search"></i>
                            <input id="timeSlotsInput6" spellcheck="false" type="text" placeholder="Search">
                        </div>
                        <ul class="options"></ul>
                    </div>
                </div>
            </div>

            <button type="button" id="add-data">Add Data</button>
        </form>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const form = document.getElementById("timetable-form");
    const addDataButton = document.getElementById("add-data");
    const timetableContainer = document.getElementById("timetable");
    const timeSlotWrappers = document.querySelectorAll(".wrapper");
    const searchInps = document.querySelectorAll(".search input"); // Define searchInps as a NodeList
    let optionsArray = []; // Array to store options

    function fetchSubjects() {
        return fetch("get_subjects.php")
            .then(response => {
                if (!response.ok) {
                    throw new Error("Failed to fetch subjects");
                }
                return response.json();
            })
            .catch(error => {
                console.error("Error fetching subjects:", error.message);
                return [];
            });
    }

    async function createTimeSlots() {
        try {
            const subjects = await fetchSubjects();
            optionsArray = subjects.map(subject => `${subject.name} (${subject.code})`);
            timeSlotWrappers.forEach((wrapper, index) => {
                populateOptions(wrapper.querySelector(".options"), optionsArray);
                attachOptionListeners(wrapper);
            });
        } catch (error) {
            console.error("Error creating time slots:", error.message);
        }
    }

    function populateOptions(optionsElement, optionsArray) {
        let optionsHtml = "";
        optionsArray.forEach(option => {
            optionsHtml += `<li>${option}</li>`;
        });
        optionsElement.innerHTML = optionsHtml;
    }

    function attachOptionListeners(wrapper) {
        const selectBtn = wrapper.querySelector(".select-btn");
        const options = wrapper.querySelector(".options");
        const searchInput = wrapper.querySelector(".search input");

        selectBtn.addEventListener("click", () => {
            closeAllTimeSlots(); // Close all other time slots
            wrapper.classList.toggle("active");
            searchInput.focus();
            searchInput.value = "";
            populateOptions(options, optionsArray);
        });

        options.addEventListener("click", (event) => {
            const selectedOption = event.target.closest("li");
            if (selectedOption) {
                updateName(selectedOption.innerText, selectBtn, searchInput);
                wrapper.classList.remove("active");
            }
        });

        searchInput.addEventListener("keyup", () => {
            let searchWord = searchInput.value.trim().toLowerCase();
            let filteredOptions = optionsArray.filter(option => option.toLowerCase().includes(searchWord));
            populateOptions(options, filteredOptions);
        });

        document.addEventListener("click", (event) => {
            const isClickedInside = wrapper.contains(event.target);
            if (!isClickedInside) {
                wrapper.classList.remove("active");
            }
        });

        function updateName(text, selectBtn, searchInput) {
            selectBtn.querySelector("span").innerText = text;
            searchInput.value = "";
        }
    }

    function closeAllTimeSlots() {
        timeSlotWrappers.forEach(wrapper => {
            wrapper.classList.remove("active");
        });
    }

    createTimeSlots(); // Initialize time slots


    function collectTimeSlotData() {
    const timeSlotData = {};
    timeSlotWrappers.forEach((wrapper, index) => {
        const selectedSubject = wrapper.querySelector(".select-btn span").innerText.trim();
        timeSlotData[`timeSlot${index + 1}`] = selectedSubject !== `Select Subject for Time Slot ${index + 1}` ? selectedSubject : null;
        
        // Reset the span text if it's not the default
        if (selectedSubject !== `Select Subject for Time Slot ${index + 1}`) {
            wrapper.querySelector(".select-btn span").innerText = `Select Subject for Time Slot ${index + 1}`;
        }
    });
    return timeSlotData;
}


addDataButton.addEventListener("click", function() {
    console.log("Add Data button clicked"); // Debug statement
    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());
    const timeSlotData = collectTimeSlotData();
    const postData = { ...data, ...timeSlotData };

    // Log the data before sending
    console.log("Data sent to server:", postData);

    // Send data to server via AJAX
    fetch("add_data.php", {
            method: "POST",
            body: JSON.stringify(postData)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error("Network response was not ok");
            }
            return response.json(); // Convert response to JSON
        })
        .then(json => {
            console.log("Response from server:", json); // Debug statement

            // Check if table was created or data was inserted successfully
            if (json.create_table) {
                console.log("Table creation message:", json.create_table); // Debug statement
            }
            if (json.insert_data) {
                console.log("Data insertion message:", json.insert_data); // Debug statement
            }

            // Fetch and display updated data
            fetchAndDisplayData();
        })
        .catch(error => console.error("Error:", error));
});

    function fetchAndDisplayData() {
        console.log("Fetching and displaying data"); // Debug statement
        fetch("fetch_data.php")
            .then(response => response.text()) // Convert response to text
            .then(text => {
                // Update timetable display with fetched data
                timetableContainer.innerHTML = text;
            })
            .catch(error => console.error("Error:", error));
    }

    // Initialize time slots and fetch/display existing data
    createTimeSlots();
    fetchAndDisplayData();
});

function goBack() {
    window.history.back();
}

function goHome() {
    localStorage.clear(); // Clear localStorage
    sessionStorage.clear(); // Clear sessionStorage
    window.location.href = 'logout.php'; // Navigate to home page
}

</script>
</body>
</html>
