import { showAchievementNotification } from "./notifications";
import { Notyf } from "notyf";
function saveBoardState(gameOver, board, player) {
    $.ajax({
        url: "add",
        method: "POST",
        data: {
            gameOver: gameOver ? 1 : 0,
            data: JSON.stringify(board),
            player: player,
        },
        success: function (response) {
            console.log("Game state updated:", response);
        },
        error: function (xhr) {
            console.error("Error updating game state:", xhr.responseText);
        },
    });
}

export { saveBoardState };
