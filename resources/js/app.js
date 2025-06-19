import $ from "jquery";
window.$ = $;
window.jQuery = $;
import "./bootstrap";
import { saveBoardState, ai_respons, checkGameOver } from "./functions";
let aiActive = true; // AI is allowed to run by default

let playerName = playerNameFromServer;
let currentname = playerName;
var playermove;

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
    $("#startGame").click(function () {
        playermove = false;
        aiActive = false;
        $(".grid button").text("");
        board = Array(9).fill("");
        current = "X";
        gameOver = false;
        setTimeout(() => {
            aiActive = true;
        }, 200);
        saveBoardState(gameOver, board, current);
        const starter = $("#starter").val();
        if (starter === "ai") {
            aiSymbol = "X";
            current = "O";

            ai_respons(board, aiSymbol, gameOver, current, aiActive);
            playermove = true;
        } else {
            current = "X";
            aiSymbol = "O";
            playermove = true;
        }
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
        saveBoardState(gameOver, board, current);
    });
    $(".grid button").click(function () {
        if (playermove) {
            var index = $(".grid button").index(this);
        }

        if (board[index] !== "" || gameOver) return;

        $(this).text(current);
        board[index] = current;

        $("#status").prepend(
            "<div>Player " +
                currentname +
                " placed at position " +
                (index + 1) +
                "</div>",
        );
        gameOver = checkGameOver(board, gameOver, aiSymbol);
        if (gameOver) {
            aiActive = false;
        }
        if (!gameOver) {
            ai_respons(board, aiSymbol, gameOver, current, aiActive);
            playermove = true;
        }

        saveBoardState(gameOver, board, current);
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
