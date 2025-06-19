import { Notyf } from "notyf";
import "notyf/notyf.min.css";

const notyf = new Notyf({
    duration: 5000,
    position: { x: "right", y: "top" },
});

function showAchievementNotification() {
    fetch("/api/dashboard", {
        method: "GET",
        headers: {
            Accept: "application/json",
            "X-Requested-With": "XMLHttpRequest",
            "X-CSRF-TOKEN": document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content"),
            "Cache-Control": "no-cache",
        },
        credentials: "same-origin",
    })
        .then((response) => response.json())
        .then((data) => {
            const achievements = data.lastAchievement;
            console.log(data.lastAchievement);

            if (achievements && achievements.length > 0) {
                achievements.forEach((title, index) => {
                    setTimeout(() => {
                        notyf.success(
                            "🏆 You have unlocked new achievement: " + title,
                        );
                    }, index * 1000);
                });
            } else {
                console.log("No achievements found.");
            }
        })
        .catch((error) => {
            console.error("Error:", error);
            notyf.error("Failed to fetch achievements.");
        });
}

// Example trigger
// You must call this function from inside some game logic
// showAchievementNotification();

export { showAchievementNotification };
