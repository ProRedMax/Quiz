"use strict";

let answers = [];

function addItem() {
    let answerList = document.createElement("div");
    let list = document.querySelector("div.list");
    let newInput = document.createElement("input");
    let newCheckBox = document.createElement("input");
    newCheckBox.type = "radio";
    newCheckBox.required = true;
    newCheckBox.name = "correctAnswer";
    newCheckBox.value = answers.length + "";
    newInput.type = "text";
    newInput.className = "centered";
    newInput.name = "answers[]";
    answerList.className = "answerEntry";
    list.appendChild(answerList);
    answerList.appendChild(newInput);
    answerList.appendChild(newCheckBox)
    answers.push(answerList);
}

function removeItem() {
    let list = document.querySelector("div.list");
    list.removeChild(answers.pop());
}