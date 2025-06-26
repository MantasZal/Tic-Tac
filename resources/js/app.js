import $ from "jquery";
window.$ = $;
window.jQuery = $;
import "./bootstrap";
import { saveBoardState, sendGameLogicRequest } from "./functions";
let aiActive = true; // AI is allowed to run by default

let playerName = playerNameFromServer;
let currentname = playerName;
var playermove;
var game_id = game_idFromServer;

$("#reset").click(function () {
    aiActive = false;
});

$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });
    var aiSymbol = "O";
    if (gameOverFromServer) {
        var current = "X";
        var board = Array(9).fill(""); // initialize from PHP
        var gameOver = 0;
    } else {
        var current = playerFromServer === "X" ? "O" : "X";
        var board = JSON.parse(boardFromServer); // initialize from PHP
        var gameOver = 0;
    }
    $("#continueGame, #continueGameMobile").click(function () {
        // Restore the previous board state
        $(".grid button").each(function (index) {
            $(this).text(board[index]);
        });
        $(".grid button").prop("disabled", false);
        aiActive = true;
        playermove = true;

        // Remove both Continue buttons from DOM
        $("#continue-wrapper").remove();
        $("#continue-wrapper-mobile").remove();
    });

    $("#startGame").click(function () {
        const starter = $("#starter").val();
        const difficulty = $("#difficulty").val();
        $.ajax({
            url: "/startGame",
            method: "POST",
            data: {
                starter: starter,
                difficulty: difficulty,
            },
            success: function (response) {
                // Redirect to the new game URL using the returned game_id
                window.location.href = `/${response.game_id}`;
                console.log("Game started:", response);
            },
            error: function (xhr) {
                console.error("Error starting the game:", xhr.responseText);
            },
        });
        // window.location.href = `/${game_id}`;

        $(".grid button").prop("disabled", false);
        $("#startGame").prop("disabled", true);
        playermove = false;
        aiActive = false;
        $(".grid button").text("");
        board = Array(9).fill("");
        current = "X";
        gameOver = false;
        setTimeout(() => {
            aiActive = true;
        }, 200);

        saveBoardState(gameOver, board, current, game_id);

        if (starter === "ai") {
            aiSymbol = "X";
            current = "O";
            playermove = true;
            const index = 0;
            const userId = document.querySelector("div.py-12").dataset.userId;
            sendGameLogicRequest({
                index,
                current,
                gameOver,
                aiSymbol,
                playerName,
                userId,
                board,
                aiActive,
                game_id,
            });
            $("#startGame").prop("disabled", false);
        } else {
            current = "X";
            aiSymbol = "O";
            playermove = true;
            $("#startGame").prop("disabled", false);
        }
        $("#continue-wrapper").remove();
    });

    $(".grid button").each(function (index) {
        $(this).text(board[index]);
    });

    $("#reset").click(function () {
        playermove = false;
        aiActive = false;
        $(".grid button").text("");
        board = Array(9).fill("");
        current = "X";
        gameOver = false;
        setTimeout(() => {
            aiActive = true;
        }, 200);
        $("#status").empty();
        saveBoardState(gameOver, board, current, game_id);
        $("#continue-wrapper").remove();
    });
    $(".grid button").click(function () {
        if (!playermove) return;

        const index = $(".grid button").index(this);
        const userId = document.querySelector("div.py-12").dataset.userId;
        sendGameLogicRequest({
            index,
            current,
            gameOver,
            aiSymbol,
            playerName,
            userId,
            board,
            aiActive,
            game_id,
        });
    });
});
// Alpine Logic

document.addEventListener("DOMContentLoaded", () => {
    const applyBtn = document.getElementById("applySettingsMobile");
    if (!applyBtn) return;

    applyBtn.addEventListener("click", () => {
        const starter = document.getElementById("mobile-starter").value;
        const difficulty = document.getElementById("mobile-difficulty").value;

        // Update main dropdowns
        document.getElementById("starter").value = starter;
        document.getElementById("difficulty").value = difficulty;

        // Start the game
        document.getElementById("startGame").click();

        // Close modal via Alpine
        document.querySelector("[x-data]").__x.$data.showGameSettings = false;
    });
});
