// Toggle Mobile Menu Functionality
const toggleMenuBtn = document.getElementById('toggleMenuBtn');
const mobileMenu = document.getElementById('mobileMenu');
const closeMenuBtn = document.getElementById('closeMenuBtn');

toggleMenuBtn.addEventListener('click', () => {
    document.body.classList.toggle('overflow-hidden');
    mobileMenu.classList.toggle('hidden');
});

closeMenuBtn.addEventListener('click', () => {
    document.body.classList.toggle('overflow-hidden');
    mobileMenu.classList.add('hidden');
});


function initializeAccount(userFullName) {
    // Use userFullName in your script

    // Example usage
    const userImgs = document.querySelectorAll(".userImg");
    userImgs.forEach(img => {
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