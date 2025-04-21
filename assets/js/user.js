
function toggleProfileCard() {
    const card = document.getElementById("profileCard");
    card.style.display = (card.style.display === "block") ? "none" : "block";
}

// Optional: Hide when clicking outside
document.addEventListener("click", function (event) {
    const card = document.getElementById("profileCard");
    const icon = document.querySelector(".profile-icon");

    if (!card.contains(event.target) && !icon.contains(event.target)) {
        card.style.display = "none";
    }
});




document.addEventListener("DOMContentLoaded", function () {
    fetch("fetch_routes.php")
        .then(response => response.json())
        .then(data => {
            let tableBody = document.querySelector("#busRoutesTable tbody");
            tableBody.innerHTML = ""; // Clear existing table rows

            data.forEach((route) => {
                let row = `<tr>
                            <td>${route.sub_stops}</td>
                            <td>₹${route.cost}</td>
                        </tr>`;
                tableBody.innerHTML += row;
            });
        })
        .catch(error => console.error("Error fetching data:", error));
});


function navigateToContact() {
    document.querySelector('[data-target="contact-content"]').click();
}

document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', function (e) {
        const target = this.getAttribute('data-target');

        if (target === 'home-content') {
            // Redirect to the external home.html page
            window.location.href = 'home.html';
        } else if (target === 'about-content') {
            document.getElementById('content').innerHTML = `
                    <section class="about_us">
                        <h2>Why Choose Us?</h2>
                        <p>At our College Bus System, we aim to make your daily commute hassle-free and convenient. Our platform offers:</p>
                        <ul>
                            <li><strong>Efficient Bus Management:</strong> Stay updated with real-time bus schedules and driver information.</li>
                            <li><strong>Easy Pass Application:</strong> Apply for your bus pass online without the need for lengthy paperwork.</li>
                            <li><strong>Secure Payments:</strong> Multiple payment methods ensure a smooth and secure transaction.</li>
                            <li><strong>Digital Bus Pass:</strong> Forget the hassle of carrying a physical pass. Your digital bus pass is always accessible.</li>
                            <li><strong>Notifications & Alerts:</strong> Receive timely updates about bus delays, pass renewal reminders, and payment confirmations.</li>
                            <li><strong>User-Friendly Interface:</strong> Our dashboard is designed to be intuitive and easy to navigate.</li>
                            <li><strong>Customer Support:</strong> Got any queries? We are here to assist you anytime.</li>
                        </ul>
                        <p>If you have any questions or need assistance, please don't hesitate to contact us. We are dedicated to providing you with the best support.</p>
                        <button onclick="navigateToContact()" style="display: block; margin: 20px auto; background-color:rgb(157, 113, 178);color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;">
                        Contact Us
                        </button>

                        <p>We prioritize your comfort and convenience. Join us and make your college commute stress-free!</p>
                    </section>
                `;
        } else if (target === 'contact-content') {
            e.preventDefault();
            document.getElementById('content').innerHTML = `
                        <section class="contact_us">
                            <h2>Contact Us!</h2>
                            <form id="contactForm" action="submit_contact.php" method="POST">
                            <input type="text" name="name" placeholder="Enter your name" required><br>
                            <input type="email" name="email" placeholder="Enter your email" required><br>
                            <input type="tel" name="phone" placeholder="Enter your number" required><br>
                            <textarea name="message" placeholder="Enter your message" rows="4" required></textarea><br>
                            <input type="submit" value="Send Message">
                            </form>

                        </section>
                    `;
        }
    });
});
