import $ from "jquery";
window.$ = $;
window.jQuery = $;
import "./bootstrap";
import { saveBoardState } from "./functions";
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
            playermove = true;
            const index = 0;
            const userId = document.querySelector("div.py-12").dataset.userId;
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
                    // board: board,
                },
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content",
                    ),
                },
                success: function (response) {
                    // if (!response.valid) {
                    //     alert("The place is occupied ");
                    // }
                    $("#status").prepend(`<div>${response.message} </div>`);
                    $(".grid button").each(function (index) {
                        $(this).text(board[index]);
                    });
                    board = response.board;
                    $(".grid button").each(function (index) {
                        $(this).text(board[index]);
                    });
                    if (response.gameOver) {
                        aiActive = false;
                        alert("The winner is " + response.winner);
                        alert("Your new rank is  " + response.new_rank);
                    }

                    gameOver = response.gameOver;
                },
                error: function (xhr) {
                    alert("Error updating rank.");
                },
            });
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
    alert(aiSymbol);
    $(".grid button").click(function () {
        if (!playermove) return;

        const index = $(".grid button").index(this);
        const userId = document.querySelector("div.py-12").dataset.userId;
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
                // board: board,
            },
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                // if (!response.valid) {
                //     alert("The place is occupied ");
                // }
                $("#status").prepend(`<div>${response.message} </div>`);
                $(".grid button").each(function (index) {
                    $(this).text(board[index]);
                });
                board = response.board;
                $(".grid button").each(function (index) {
                    $(this).text(board[index]);
                });
                if (response.gameOver) {
                    aiActive = false;
                    alert("The winner is " + response.winner);
                    alert("Your new rank is  " + response.new_rank);
                }

                gameOver = response.gameOver;
            },
            error: function (xhr) {
                alert("Error updating rank.");
            },
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
