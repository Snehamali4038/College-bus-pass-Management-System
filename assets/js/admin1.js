function toggleProfileCard() {
    const card = document.getElementById("profileCard");
    card.classList.toggle("active");
}


document.addEventListener("click", function(event) {
    const card = document.getElementById("profileCard");
    const icon = document.querySelector(".profile-icon");

    if (!card.contains(event.target) && event.target !== icon) {
        card.classList.remove("active");
    }
});