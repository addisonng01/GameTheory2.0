require('dotenv').config();

const BASE_URL = process.env.BASE_URL || 'http://localhost:3000/api'; // Adjust the base URL if needed

let firstNameValue = "";
let lastNameValue = "";
let emailValue = "";

// Function to submit the form and save data to the database
async function submitForm() {
    // Get values from input fields
    const firstNameInput = document.getElementById('firstName');
    const lastNameInput = document.getElementById('lastName');
    const emailInput = document.getElementById('email');

    if (firstNameInput && lastNameInput && emailInput) {
        firstNameValue = firstNameInput.value || "n/a";
        lastNameValue = lastNameInput.value || "n/a";
        emailValue = emailInput.value || "n/a";

        try {
            const response = await fetch(`${BASE_URL}/students`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    first_name: firstNameValue,
                    last_name: lastNameValue,
                    email: emailValue,
                }),
            });

            if (!response.ok) {
                throw new Error(`Error submitting user data: ${response.statusText}`);
            }

            const responseData = await response.json();
            console.log('User data submitted successfully:', responseData);
            alert('Student profile created successfully');
        } catch (error) {
            console.error('Error submitting user data:', error.message);
            alert('Error submitting user data');
        }
    }
}

// Function to display user information
async function displayInfo() {
    try {
        const response = await fetch(`${BASE_URL}/students`);

        if (!response.ok) {
            throw new Error(`Error fetching user data: ${response.statusText}`);
        }

        const students = await response.json();

        // Assuming the server returns an array of students and you want to display the first one
        if (students.length > 0) {
            const student = students[0]; // Display first student's info for example
            document.getElementById('firstNameDisplay').innerHTML = "First Name: " + student.first_name;
            document.getElementById('lastNameDisplay').innerHTML = "Last Name: " + student.last_name;
            document.getElementById('emailDisplay').innerHTML = "CSS Email: " + student.email;
        } else {
            document.getElementById('firstNameDisplay').innerHTML = "First Name: N/A";
            document.getElementById('lastNameDisplay').innerHTML = "Last Name: N/A";
            document.getElementById('emailDisplay').innerHTML = "CSS Email: N/A";
        }
    } catch (error) {
        console.error('Error fetching user data:', error.message);
    }
}
