function addNote(){

let title=document.getElementById("title").value;

let notes=JSON.parse(localStorage.getItem("notes")) || [];

notes.push(title);

localStorage.setItem("notes",JSON.stringify(notes));

showNotes();

}

function showNotes(){

let notes=JSON.parse(localStorage.getItem("notes")) || [];

let html="";

notes.forEach(function(n){

html+=`<div class="note">${n}</div>`;

});

document.getElementById("notes").innerHTML=html;

}

showNotes();