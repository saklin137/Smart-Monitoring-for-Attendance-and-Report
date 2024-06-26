<?php
session_start();

if (!isset($_SESSION['username'])) {
    
    header("Location: login.html");
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
    <link rel="stylesheet" href="get_details.css">
    <title>Document</title>

  <style>
  #inactivity-alert {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background-color: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    z-index: 9999;
    max-width: 300px;
    text-align: center;
  }

  #inactivity-alert p {
    margin-bottom: 15px;
    font-size: 16px;
    color: #333;
  }

  #timer {
    font-size: 20px;
    font-weight: bold;
    color: #007bff;
  }

      #loader {
            display: none; /* Hidden by default */
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 100px; /* Adjust the size as needed */
            height: 100px; /* Adjust the size as needed */
        }
</style>

</head>

<body>
    <div class="container">
        <div class="screen"> 

        <div class="button-container" style="display: flex; justify-content:space-between;;">
         <i class="fas fa-arrow-left back-button" style="font-size: 24px; color: white; cursor: pointer; margin-right: 10px;" onclick="goBack()" title="Back"></i>
         <i class="fas fa-home home-button" style="font-size: 24px; color: white; cursor: pointer; margin-right: 10px;" onclick="goHome()" title="Home"></i>
         </div>

            <h1>Fetch Student Details</h1>
              <div id="loader"></div>

            <div id="inactivity-alert">
            <p>Hey there! It seems you've been inactive for a while. Would you like to continue?</p>
            <p id="countdown">Time left: <span id="timer"></span></p>
            </div>

            <div class="fade-line"></div>
            <div class="row">
                <div class="username" id="username" style="font-size: 20px; color: #fff;">User Name</div>
                <button class="logout-btn" onclick="logout()">Log Out</button>
            </div>
            <br>
            <div class="row">
                <div class="current-time">Current Time: 
                    <span class="hour" id="hour1" style="font-weight: 800;">0</span>
                    <span class="hour" id="hour2"  style="font-weight: 800;">0</span>:
                    <span class="minute" id="minute1"  style="font-weight: 800;">0</span>
                    <span class="minute" id="minute2" style="font-weight: 800;">0</span>
                </div>
                <button class="pdf-btn" onClick="pdf_ge()">Export PDF</button>
                <button class="timetable-btn" onClick="time_table()">Time Table</button>
            </div>
            <div class="fade-line"></div>

            <div class="row">

                <div>
                    <label for="department" class="option-label" style="color: #fff; font-size: 25px; font-weight:600">Department *</label>

                    <div class="wrapper">
                        <div class="select-btn">
                          <span id="departmentSpan">Select Department</span>
                          <i class="uil uil-angle-down"></i>
                        </div>
                        <div class="content">
                          <div class="search">
                            <i class="uil uil-search"></i>
                            <input spellcheck="false" type="text" placeholder="Search">
                          </div>
                          <ul class="options"></ul>
                        </div>
                    </div>
                </div>
                
                <div>
                    <label for="Subject" class="option-label" style="color: #fff; font-size: 25px; font-weight:600">Subject *</label>

                    <div class="wrapper">
                        <div class="select-btn">
                          <span id="subjectSpan">Select Subject</span>
                          <i class="uil uil-angle-down"></i>
                        </div>
                        <div class="content">
                          <div class="search">
                            <i class="uil uil-search"></i>
                            <input spellcheck="false" type="text" placeholder="Search">
                          </div>
                          <ul class="options"></ul>
                        </div>
                    </div>
                </div>
            </div> 
            
            <div class="row">

                <div>
                    <label for="semester" class="option-label" style="color: #fff; font-size: 25px; font-weight:600">Semester *</label>

                    <div class="wrapper">
                        <div class="select-btn">
                          <span id="semesterSpan">Select Semester</span>
                          <i class="uil uil-angle-down"></i>
                        </div>
                        <div class="content">
                          <div class="search">
                            <i class="uil uil-search"></i>
                            <input spellcheck="false" type="text" placeholder="Search">
                          </div>
                          <ul class="options"></ul>
                        </div>
                    </div>
                </div>
                
                <div>
                    <label for="section" class="option-label" style="color: #fff; font-size: 25px; font-weight:600">Section</label>

                    <div class="wrapper">
                        <div class="select-btn">
                          <span id="sectionSpan">Select Section</span>
                          <i class="uil uil-angle-down"></i>
                        </div>
                        <div class="content">
                          <div class="search">
                            <i class="uil uil-search"></i>
                            <input spellcheck="false" type="text" placeholder="Search">
                          </div>
                          <ul class="options"></ul>
                        </div>
                    </div>
                </div>
            </div>  

    <div class="row_new">
    <div class="time-slot">
        <label class="label" style="color: #fff; font-size: 25px; font-weight:600">Time Slot *</label>
        <br>
        <select id="timeSlot" class="input-box">
            <option value="time_slot_1">Time Slot 1(9.50-10.40)</option>
            <option value="time_slot_2">Time Slot 2(10.40-11.30)</option>
            <option value="time_slot_3">Time Slot 3(12.00-1.00)</option>
            <option value="time_slot_4">Time Slot 4(01.00-1.20)</option>
            <option value="time_slot_5">Time Slot 5(01.20-02.00)</option>
            <option value="time_slot_6">Time Slot 6(02.10-02.40)</option>
        </select>
    </div>

    <div class="date-input">
        <label class="label" style="color: #fff; font-size: 25px; font-weight:600">Date *</label>
        <br>
        <input type="date" id="dateInput" class="input-box">
    </div>
</div>

            <div class="row_new">
                <div class="st_Roll">
                    <label class="label" style="color: #fff; font-size: 25px; font-weight:600" >Start Roll Number</label>
                    <input autocomplete="off" type="number"  id="st_Roll" placeholder="Enter Start Roll Number"  />
                    <div id="st_roll_Message" class="message"></div>
                    <br>
                </div>

                <div class="en_Roll">
                    <label class="label" style="color: #fff; font-size: 25px; font-weight:600; display: block; margin-right: 160px;">End Roll Number</label>
                    <input autocomplete="off" id="end_roll", type="number" style="margin-right: 0%; padding-right: 0%;" placeholder="Enter End Roll Number "  />
                    <div id="end_roll_Message" class="message"></div>
                    <br>
                </div>
            </div>

            <div id="container-button">
                <button type="button" onclick="validateAndSubmit()" class="submit">
                    <span class="circle" aria-hidden="true">
                        <span class="icon arrow"></span>
                    </span>
                    <span class="button-text">Submit</span>
                </button>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bodymovin/5.7.14/lottie.min.js"></script>
    <script>
        const username = "<?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest'; ?>";

        localStorage.setItem('username', username);
        document.getElementById('username').innerText = username;
        let departments = ["Computer Science", "Electrical Engineering", "Mechanical Engineering", "Civil Engineering", "Chemical Engineering", "Aerospace Engineering", "Biomedical Engineering"];

        let semesters = ["First Semester", "Second Semester", "Third Semester", "Fourth Semester", "Fifth Semester", "Sixth Semester", "Seventh Semester", "Eighth Semester"];
        let sections = ["A", "B", "C", "D", "E"]; 
        
        let subjects = ["sutt4","suut66"];

            var animation = lottie.loadAnimation({
            container: document.getElementById('loader'), 
            renderer: 'svg',
            loop: true,
            autoplay: true,
            path: 'Animation.json' 
        });

        
        function fetchSubjects() {
            fetch('get_subjects.php')
              .then(response => response.json())
              .then(data => {
                subjects = data.map(subject => `${subject.name} (${subject.code})`);
                
                console.log(subjects); 
                console.log(departments);

                document.querySelectorAll(".wrapper").forEach((wrapper, index) => {
          const selectBtn = wrapper.querySelector(".select-btn");
          const searchInp = wrapper.querySelector("input");
          const options = wrapper.querySelector(".options");
        
          let optionsArray;
          let label;
        
          switch (index) {
            case 0:
                
              optionsArray = departments;
              label = "Department";
              break;
              
            case 1:
              optionsArray = subjects;
              label = "Subject";
              break;
            case 2:
              optionsArray = semesters;
              label = "Semester";
              break;
            case 3:
              optionsArray = sections; 
              label = "Section";
              break;  
            default:
              optionsArray = [];
              label = "";
          }
        
          function populateOptions(optionsArray) {
            let optionsHtml = "";
            optionsArray.forEach(option => {
              optionsHtml += `<li>${option}</li>`;
            });
            return optionsHtml;
          }
        
          function updateName(selectedLi) {
            searchInp.value = selectedLi.innerText;
            selectBtn.firstElementChild.innerText = selectedLi.innerText;
            wrapper.classList.remove("active");
          }
        
        
         
          function attachOptionListeners() {
            options.querySelectorAll("li").forEach(option => {
              option.addEventListener("click", () => {
                updateName(option);
              });
            });
          }
        
          attachOptionListeners(); 
        
          selectBtn.addEventListener("click", () => {
            wrapper.classList.toggle("active");
            searchInp.focus();
        
            searchInp.value = "";
            options.innerHTML = populateOptions(optionsArray);
            attachOptionListeners();
          });
        
          document.addEventListener("click", (event) => {
            const isClickedInside = wrapper.contains(event.target);
            if (!isClickedInside) {
              wrapper.classList.remove("active");
            }
          });
        
          searchInp.addEventListener("keyup", () => {
            let searchWord = searchInp.value.trim().toLowerCase();
            let filteredOptions = optionsArray.filter(option => option.toLowerCase().includes(searchWord));
            options.innerHTML = populateOptions(filteredOptions);
            attachOptionListeners(); 
          });
        });
                
               
              
                // const subjectOptions = document.querySelector('.wrapper:nth-child(2) .options');
                // subjectOptions.innerHTML = populateOptions(subjects);
              })
              .catch(error => console.error('Error fetching subjects:', error));
        }
        
   
        

    window.addEventListener('load', function() {
    fetchSubjects(); 
    selectCurrentDate();
    updateTime(); 
    selectTimeSlot(); 
    fetchSubjectForCurrentUser();
    });
        
        
        

        
        
        
        function updateTime() {
            const now = new Date();
            const hours = ('0' + now.getHours()).slice(-2); 
            const minutes = ('0' + now.getMinutes()).slice(-2); 
        
            // Get individual digit elements
            const hour1 = document.getElementById('hour1');
            const hour2 = document.getElementById('hour2');
            const minute1 = document.getElementById('minute1');
            const minute2 = document.getElementById('minute2');
        
            // Update hours and minutes with animation
            animateDigit(hour1, hours.charAt(0));
            animateDigit(hour2, hours.charAt(1));
            animateDigit(minute1, minutes.charAt(0));
            animateDigit(minute2, minutes.charAt(1));
        }
        
        function animateDigit(element, newValue) {
            const oldValue = element.textContent;
            if (oldValue !== newValue) {
                element.classList.add('animating');
                setTimeout(() => {
                    element.textContent = newValue;
                    element.classList.remove('animating');
                }, 500); 
            }
        }
        
        setInterval(updateTime, 1000);

        function selectTimeSlot() {
            const now = new Date();
            const currentHour = now.getHours();
            const currentMinute = now.getMinutes();

            console.log("Current Hour:", currentHour);
            console.log("Current Minute:", currentMinute);

            let timeSlot;

    // Determine time slot based on current time
    if ((currentHour === 9 && currentMinute >= 50) || (currentHour === 10 && currentMinute <= 40)) {
        timeSlot = "time_slot_1";
    } else if ((currentHour === 10 && currentMinute >= 40) || (currentHour === 11 && currentMinute <= 30)) {
        timeSlot = "time_slot_2";
    } else if (currentHour === 12 && currentMinute >= 0 && currentMinute <= 59) {
        timeSlot = "time_slot_3";
    } else if (currentHour === 13 && currentMinute >= 0 && currentMinute <= 19) {
        timeSlot = "time_slot_4";
    } else if (currentHour === 13 && currentMinute >= 20 && currentMinute <= 59) {
        timeSlot = "time_slot_5";
    } else if ((currentHour === 14 && currentMinute >= 10) || (currentHour === 15 && currentMinute <= 40)) {
        timeSlot = "time_slot_6";
    } else {
        // Set default time slot
        timeSlot = "time_slot_1";
    }

    console.log("Selected Time Slot:", timeSlot);

    document.getElementById("timeSlot").value = timeSlot;
   
  }

  function selectCurrentDate() {
    const now = new Date();
    const year = now.getFullYear();
    const month = ('0' + (now.getMonth() + 1)).slice(-2);
    const day = ('0' + now.getDate()).slice(-2);
    const currentDate = `${year}-${month}-${day}`;

    // Set current date in the date input
    document.getElementById("dateInput").value = currentDate;
}


function validateAndSubmit() {
    const department = document.getElementById('departmentSpan').textContent.trim();
    const subject = document.getElementById('subjectSpan').textContent.trim();
    const timeslot = document.getElementById('timeSlot').value.trim();
    const semester = document.getElementById('semesterSpan').textContent.trim();
    const section = document.getElementById('sectionSpan').textContent.trim();
    const startRoll = document.getElementById('st_Roll').value.trim();
    const endRoll = document.getElementById('end_roll').value.trim();
    const hasSession = localStorage.getItem('username');

    console.log('Department:', department);
    console.log('Subject:', subject); 
    console.log('Time Slot:', timeslot);
    console.log('Semester:', semester);
    console.log('Section:', section);
    console.log('Start Roll:', startRoll);
    console.log('End Roll:', endRoll);
    console.log('Session:', hasSession);

    // Check if session is present or start roll and end roll are present
    if ((!section && (!startRoll || !endRoll)) ||
    (section && (!department || department === "Select Department" ||
                 !subject || subject === "Select Subject" ||
                 !semester || semester === "Select Semester"))) {
    console.log("One or more fields are empty or not selected.");
    alert("One or more fields are empty or not selected.");
} else {
            // AJAX request to send data to PHP script
         const formData = new FormData();
formData.append('department', department);
formData.append('subject', subject);
formData.append('timeslot', timeslot);
formData.append('semester', semester);
formData.append('section', section);
formData.append('startRoll', startRoll);
formData.append('endRoll', endRoll);

fetch('fetch_student_data.php', {
    method: 'POST',
    body: formData
})
.then(response => {
    if (!response.ok) {
        throw new Error('Network response was not ok');
    }
    return response.json();
})
.then(data => {
  if (!data.error) {
    console.log(data);
    localStorage.setItem('jsonData', JSON.stringify(data));
    window.location.href = 'student_attendance.php';
    }else {
        alert("No Student Found");
        console.log("No data found");
        alert(`${data.error}\nDepartment: ${data.department}\nSubject: ${data.subject}\nSemester: ${data.semester}\nSection: ${data.section}\nStart Roll: ${data.startRoll}\nEnd Roll: ${data.endRoll}`);
    }
})
.catch(error => {
    alert("Error: " + error.message);
    console.error('Error fetching student data:', error);
});

        }
    }

    function logout(){
      window.location.href = 'logout.php';
    }

    function time_table(){
        window.location.href='timetable.php';
    }
    

    function pdf_ge(){
      window.location.href='Report_Generator.php';
    }

    window.onbeforeunload = function() {
    destroySession();
};


function fetchSubjectForCurrentUser() {
    const day = getCurrentDay();
    const timeSlot = document.getElementById("timeSlot").value;
    document.getElementById('loader').style.display = 'block';

    const formData = new FormData();
    formData.append('day', day);
    formData.append('timeSlot', timeSlot);

    fetch('fetch_subject_for_user.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            if (response.status === 404) {
                document.getElementById('loader').style.display = 'none';
                throw new Error('Subject not found for the user, day, and time slot. Please update Time Table');
                
            } else {
                throw new Error('Network response was not ok');
                 document.getElementById('loader').style.display = 'none';
            }
        }
        return response.json();
    })
    .then(data => {
        console.log("Fetched subject details:", data);
        // Update the subject dropdown with the fetched subject value
        document.getElementById('subjectSpan').innerText = data.subjectName;
        // Update department and semester
        document.getElementById('departmentSpan').innerText = data.department;
        document.getElementById('semesterSpan').innerText = data.semester;
         document.getElementById('loader').style.display = 'none';
    })
    .catch(error => {
        alert("Error: " + error.message);
        console.error('Error fetching subject for current user:', error);
    });

}


function getCurrentDay() {
    const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    const now = new Date();
    return days[now.getDay()];
}

function goBack() {
    localStorage.clear(); 
    sessionStorage.clear(); 
    window.history.back();
    
}

function goHome() {
    localStorage.clear(); 
    sessionStorage.clear(); 
    logout();
}

const inactivityTimeLimit = 60 * 1000; // 1 minute
const logoutTimeLimit = 2 * 60 * 1000; // 2 minutes
const countdownTimeLimit = 60 * 1000; // 1 minute

let inactivityTimer;
let countdownTimer;
let lastActivityTime = Date.now();

// Function to show the alert
function showAlert(timeLeft) {
    document.getElementById('inactivity-alert').style.display = 'block';
    startCountdown(timeLeft);
}

// Function to start the countdown timer
function startCountdown(timeLeft) {
    clearInterval(countdownTimer);
    let remainingTime = timeLeft;

    countdownTimer = setInterval(function() {
        remainingTime -= 1000;
        document.getElementById('timer').textContent = Math.ceil(remainingTime / 1000);

        if (remainingTime <= 0) {
            clearInterval(countdownTimer);
            document.getElementById('inactivity-alert').style.display = 'none';
            console.log('Log Out');
            // Perform logout or any other action here
            logout();
        }
    }, 1000);
}

// Function to reset the inactivity timer
function resetTimer() {
    clearTimeout(inactivityTimer);
    clearInterval(countdownTimer);
    const currentTime = Date.now();
    const elapsedTime = currentTime - lastActivityTime;

    if (elapsedTime < inactivityTimeLimit) {
        // If less than 1 minute has passed, continue tracking inactivity
        inactivityTimer = setTimeout(function() {
            const newElapsedTime = Date.now() - lastActivityTime;
            if (newElapsedTime >= inactivityTimeLimit && newElapsedTime < logoutTimeLimit) {
                // If between 1 to 2 minutes, show the alert with remaining time
                showAlert(logoutTimeLimit - newElapsedTime);
            } else if (newElapsedTime >= logoutTimeLimit) {
                // If more than 2 minutes, directly log out
                console.log('Log Out');
                // Perform logout or any other action here
                logout();
            } else {
                // If less than 1 minute, continue tracking inactivity
                resetTimer();
            }
        }, inactivityTimeLimit - elapsedTime);
    } else {
        // If more than 1 minute has passed, but less than 2 minutes, show the alert with remaining time
        if (elapsedTime < logoutTimeLimit) {
            const timeLeft = countdownTimeLimit - (elapsedTime - inactivityTimeLimit);
            showAlert(timeLeft);
        } else {
            // If more than 2 minutes, directly log out
            console.log('Log Out');
            // Perform logout or any other action here
            logout();
        }
    }
}

// Function to handle user activity
function handleActivity() {
    lastActivityTime = Date.now();
    document.getElementById('inactivity-alert').style.display = 'none'; // Hide the inactivity alert
    resetTimer();
}

// Function to handle when the window gains focus
function handleFocus() {
    const currentTime = Date.now();
    const elapsedTime = currentTime - lastActivityTime;

    if (elapsedTime >= logoutTimeLimit) {
        console.log('Log Out');
        // Perform logout or any other action here
        logout();
    } else if (elapsedTime >= inactivityTimeLimit) {
        const timeLeft = countdownTimeLimit - (elapsedTime - inactivityTimeLimit);
        startCountdown(timeLeft);
    }
}

// Event listeners to track user activity
document.addEventListener('mousemove', handleActivity);
document.addEventListener('keypress', handleActivity);
document.addEventListener('scroll', handleActivity);

// Event listener for when the window gains focus
window.addEventListener('focus', handleFocus);

// Start the initial timer
resetTimer();


 </script>
</body>
</html>

</html>

