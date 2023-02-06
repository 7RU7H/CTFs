function myFunction() {
  document.getElementById("myDropdown").classList.toggle("show");
}

// Close the dropdown menu if the user clicks outside of it
window.onclick = function(event) {
  if (!event.target.matches('.dropbtn')) {
    var dropdowns = document.getElementsByClassName("dropdown-content");
    var i;
    for (i = 0; i < dropdowns.length; i++) {
      var openDropdown = dropdowns[i];
      if (openDropdown.classList.contains('show')) {
        openDropdown.classList.remove('show');
      }
    }
  }
} 

function sendRequest(a){
switch(a){
case '1':{
   data={"object":"/var/upload/games/object.pkl"}
   $.ajax({
    type: 'POST',
    url: '/fetch',
    data: JSON.stringify(data),
    contentType: 'application/json',
    success: function (responseText) {
     details=JSON.parse(responseText);
   var tag = document.createElement("p");
   var text = document.createTextNode("Game Name: "+details['Game']+" | Rating: "+details['Rating']+" | Review: "+details['Review']);
   tag.appendChild(text);
   var element = document.getElementById("insert");
   element.replaceChild(tag,element.childNodes[0]);
   }
});
          }
break;
case '2': {
   data={"object":"/var/upload/games/object1.pkl"}
   $.ajax({
    type: 'POST',
    url: '/fetch',
    data: JSON.stringify(data),
    contentType: 'application/json',
    success: function (responseText) {
     details=JSON.parse(responseText);
   var tag = document.createElement("p");
   var text = document.createTextNode("Game Name: "+details['Game']+" | Rating: "+details['Rating']+" | Review: "+details['Review']);
   tag.appendChild(text);
   var element = document.getElementById("insert");
   element.replaceChild(tag,element.childNodes[0]);
   }
});
          }
break;
case '3':{
   data={"object":"/var/upload/games/object2.pkl"}
   $.ajax({
    type: 'POST',
    url: '/fetch',
    data: JSON.stringify(data),
    contentType: 'application/json',
    success: function (responseText) {
     details=JSON.parse(responseText);
   var tag = document.createElement("p");
   var text = document.createTextNode("Game Name: "+details['Game']+" | Rating: "+details['Rating']+" | Review: "+details['Review']);
   tag.appendChild(text);
   var element = document.getElementById("insert");
   element.replaceChild(tag,element.childNodes[0]);
   }
});
          }
break;
}
}
