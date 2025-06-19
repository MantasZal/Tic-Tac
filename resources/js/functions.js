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

function ai_respons(board, aiSymbol, gameOver, current, aiActive) {
    const difficulty = $("#difficulty").val();
    $(".grid button").prop("disabled", true);
    $.ajax({
        url: "/ai-move",
        type: "POST",
        data: {
            board: JSON.stringify(board),
            ai_symbol: aiSymbol,
            difficulty: difficulty,
        },
        success: function (response) {
            $("#status").prepend(
                `<div style="color: blue;"><strong>AI says:</strong> ${response.text}</div>`,
            );
            const move = response.move;
            if (board[move] === "") {
                board[move] = aiSymbol;
                $(".grid button").eq(move).text(aiSymbol);
                $("#status").prepend(
                    `<div>AI placed ${aiSymbol} at position ${move + 1}</div>`,
                );
            } else {
                // Optional: Retry AI move if spot occupied
                // if (aiActive) ai_respons(board, aiSymbol, gameOver, current, aiActive);
            }
            gameOver = checkGameOver(board, gameOver, aiSymbol);
            if (gameOver) {
                aiActive = false;
            }
            saveBoardState(gameOver, board, current);
        },
        error: function () {
            $("#status").prepend(
                "<div style='color: red;'>Failed to get AI response.</div>",
            );
        },
        complete: function () {
            // Re-enable interaction after AJAX completes
            $("body").css("pointer-events", "auto");
            $(".grid button").prop("disabled", false); // Optional
        },
    });
}

function checkGameOver(board, gameOver, aiSymbol) {
    const notyf = new Notyf({
        duration: 3000,
        position: {
            x: "left",
            y: "bottom",
        },
    });
    if (gameOver) {
        return gameOver;
    }
    const wins = [
        [0, 1, 2],
        [3, 4, 5],
        [6, 7, 8], // rows
        [0, 3, 6],
        [1, 4, 7],
        [2, 5, 8], // columns
        [0, 4, 8],
        [2, 4, 6], // diagonals
    ];

    // Check for a winner
    for (const [a, b, c] of wins) {
        if (board[a] && board[a] === board[b] && board[a] === board[c]) {
            const symbol = board[a];
            const winner =
                symbol === aiSymbol ? "AI" : playerNameFromServer || "Human";
            $("#wins").prepend(
                `<div><strong>Player ${winner} wins!</strong></div>`,
            );
            notyf.success(`Player ${winner} wins!`);
            const userId = document.querySelector("div.py-12").dataset.userId;
            let change = 0;
            let userwon = false;
            if (winner === "AI") {
                change = -3;
                $.ajax({
                    url: `/users-rank`,
                    method: "POST",
                    data: {
                        id: userId,
                        change: change,
                    },
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content",
                        ),
                    },
                    success: function (response) {
                        console.log(response.message);
                        notyf.success("Your new rank is: " + response.new_rank);
                    },
                    error: function (xhr) {
                        notyf.error("Error updating rank.");
                        console.error(xhr.responseText);
                    },
                });
            } else {
                userwon = true;
                change = 5;
                $.ajax({
                    url: `/users-rank`,
                    method: "POST",
                    data: {
                        id: userId,
                        change: change,
                    },
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content",
                        ),
                    },
                    success: function (response) {
                        console.log(response.message);
                        notyf.success("Your new rank is: " + response.new_rank);
                    },
                    error: function (xhr) {
                        notyf.error("Error updating rank.");
                        console.error(xhr.responseText);
                    },
                });
            }
            $.ajax({
                url: "/game-result",
                method: "Post",
                data: {
                    userId: userId,
                    won: userwon ? 1 : 0,
                },
                success: function (response) {
                    console.log("Game state updated:", response);
                },
                error: function (xhr) {
                    console.error(
                        "Error updating game state:",
                        xhr.responseText,
                    );
                },
            });

            showAchievementNotification();

            return true;
        }
    }

    if (board.every((cell) => cell !== "")) {
        $("#wins").prepend("<div><strong>It's a draw!</strong></div>");
        notyf.success("It's a draw!");
        return true;
    }

    // Game is still ongoing
    return false;
}

// Assuming you have a global var playerNameFromServer from Blade, e.g.:
// const playerNameFromServer = "John Doe";

export { saveBoardState, ai_respons, checkGameOver };
