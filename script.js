function addNote(){

let title=document.getElementById("title").value;

if(title=="") return;

let notes=JSON.parse(localStorage.getItem("notes")) || [];

notes.push(title);

localStorage.setItem("notes",JSON.stringify(notes));

document.getElementById("title").value="";

showNotes();

}

function showNotes(){

let notes=JSON.parse(localStorage.getItem("notes")) || [];

let html="";

notes.forEach(function(n,index){

html+=`

<div class="note">

${n}

<br>

<button class="edit" onclick="editNote(${index})">Edit</button>

<button class="delete" onclick="deleteNote(${index})">Delete</button>

</div>

`;

});

document.getElementById("notes").innerHTML=html;

}

function deleteNote(index){

let notes=JSON.parse(localStorage.getItem("notes")) || [];

notes.splice(index,1);

localStorage.setItem("notes",JSON.stringify(notes));

showNotes();

}

function editNote(index){

let notes=JSON.parse(localStorage.getItem("notes")) || [];

let updated=prompt("Edit note",notes[index]);

if(updated!==null){

notes[index]=updated;

localStorage.setItem("notes",JSON.stringify(notes));

showNotes();

}

}

showNotes();