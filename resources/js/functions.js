import { showAchievementNotification } from "./notifications";
import { Notyf } from "notyf";
function saveBoardState(gameOver, board, player, game_id) {
    $.ajax({
        url: "add",
        method: "POST",
        data: {
            gameOver: gameOver ? 1 : 0,
            data: JSON.stringify(board),
            player: player,
            game_id: game_id,
        },
        success: function (response) {
            console.log("Game state updated:", response);
        },
        error: function (xhr) {
            console.error("Error updating game state:", xhr.responseText);
        },
    });
}
function sendGameLogicRequest({
    index,
    current,
    gameOver,
    aiSymbol,
    playerName,
    userId,
    board,
    aiActive,
    game_id,
}) {
    const notyf = new Notyf({
        duration: 4000,
        position: {
            x: "left",
            y: "bottom",
        },
    });
    index++;
    if (index) {
        $("#status").prepend(
            `<div>${playerName + " has moved to postion : " + index}</div>`,
        );
    }
    index--;
    const difficulty = $("#difficulty").val();
    $(".grid button").prop("disabled", true);
    $("#startGame").prop("disabled", true);
    $.ajax({
        url: "/game-logic",
        method: "POST",
        data: {
            index: index,
            current: current,
            gameOver: gameOver,
            aisymbol: aiSymbol,
            playerNameFromServer: playerName,
            id: userId,
            difficulty: difficulty,
            // board: board,
            game_id: game_id,
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (response) {
            if (response.invalid) {
                notyf.error("The place is occupied");
            }
            if (response.AImove) {
                $("#status").prepend(
                    `<div>${"AI has moved to postion : " + response.AImove}</div>`,
                );
            }

            $(".grid button").each(function (i) {
                $(this).text(response.board[i]);
            });

            board = response.board;

            if (response.gameOver) {
                aiActive = false;
                if (response.winner) {
                    notyf.success("The winner is " + response.winner);
                    notyf.success("Your new rank is " + response.new_rank);

                    //save game results
                    var won = response.winner === "AI" ? 0 : 1;
                    $.ajax({
                        url: "save_game_results",
                        method: "POST",
                        data: {
                            playerID: userId,
                            won: won,
                        },
                        success: function (response) {
                            $.ajax({
                                url: "add",
                                method: "POST",
                                data: {
                                    gameOver: gameOver ? 1 : 0,
                                    data: JSON.stringify(board),
                                    player: "X",
                                    game_id: game_id,
                                },
                                success: function (response) {
                                    showAchievementNotification();
                                    console.log(
                                        "Game state updated:",
                                        response,
                                    );
                                },
                                error: function (xhr) {
                                    console.error(
                                        "Error updating game state:",
                                        xhr.responseText,
                                    );
                                },
                            });

                            console.log("Game results updated:", response);
                        },
                        error: function (xhr) {
                            console.error(
                                "Error updating game results state:",
                                xhr.responseText,
                            );
                        },
                    });
                } else {
                    notyf.success("It is a draw");
                }
            } else {
                $(".grid button").prop("disabled", false);
            }
            $("#startGame").prop("disabled", false);

            gameOver = response.gameOver;
        },
        error: function () {
            alert(response.message);
        },
    });
}

export { saveBoardState, sendGameLogicRequest };
