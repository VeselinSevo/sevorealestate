// Add event listener to the "Resend Code" link
document.getElementById("resendCode").addEventListener("click", async function(event) {
    event.preventDefault(); // Prevent default behavior of the link
    try {
        // Send request to email-resend-code.php
        const response = await fetch("services/user/email-resend-code.php", {
            method: "GET"
        });
        // Check if response status is ok
        if (response.ok) {
            // Get response text
            const responseData = await response.text();
            // Update message on the page
            document.getElementById("message").innerHTML = responseData;
        } else {
            throw new Error("Failed to resend verification code.");
        }
    } catch (error) {
        // console.error("Error:", error);
        // Handle errors if needed
        document.getElementById("message").innerHTML = error;
    }
});


document.getElementById("verifyCode").addEventListener("click", async function(event) {
    event.preventDefault(); // Prevent default behavior of the link
    const verificationCode = document.getElementById("verificationCode").value;
    try {
        // Send request to email-resend-code.php
        const response = await fetch("services/user/email-verify.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ verificationCode: verificationCode }) // Include the verification code in the request body
        });
        // Check if response status is ok
        if (response.ok) {
            // Check if the response is a redirection
            if (response.redirected) {
                // Redirect the browser to the specified location
                window.location.href = response.url;
            } else {
                // Get response text
                const responseData = await response.text();
                // Update message on the page
                document.getElementById("message").innerHTML = responseData;
            }
        } else {
            throw new Error("Failed to resend verification code.");
        }
    } catch (error) {
        // console.error("Error:", error);
        // Handle errors if needed
        document.getElementById("message").innerHTML = error;
    }
});
