

// User info
function initializeAccount(userFullName) {
    // Use userFullName in your script

    // Example usage
    const userImgs = document.querySelectorAll(".userImg");
    userImgs.forEach(img => {
        console.log(123)
        img.src = getAvatarImage(userFullName);
    })

}

function getAvatarImage(fullName) {
    // Split the full name into first name and last name
    console.log(fullName)
    const [firstName, lastName] = fullName.split(" ");

    // Construct the URL to fetch the avatar image
    const apiUrl = `https://ui-avatars.com/api/?background=random&color=fff&name=${firstName}+${lastName}`;

    // Return the URL
    return apiUrl;
}

// Call the function when the DOM content is loaded
document.addEventListener("DOMContentLoaded", () => {
    const fullNameElement = document.getElementById("userName");
    const userFullName = fullNameElement.textContent.trim();
    initializeAccount(userFullName);
});





// User option navigation
function showSection(sectionName) {
    const sections = document.querySelectorAll('.user-section');
    sections.forEach(section => {
        if (section.id === `${sectionName}Section`) {
            section.classList.remove('hidden');
        } else {
            section.classList.add('hidden');
        }
    });

    const buttons = document.querySelectorAll('.account-options-navigation button');
    buttons.forEach(button => {
        if (button.id === `${sectionName}Btn`) {
            button.classList.add('border', 'border-indigo-600', 'border-2' , 'bg-indigo-100');
        } else {
            button.classList.remove('border', 'border-indigo-600', 'border-2', 'bg-indigo-100');
        }
    });
}


document.querySelectorAll('.account-options-navigation button').forEach(btn => {
    btn.addEventListener('click', function() {
        // Get the section name from the button's id
        const sectionName = this.id.replace('Btn', ''); // Remove 'Btn' from the button's id
        showSection(sectionName); // Call the showSection function with the section name
    });
});
