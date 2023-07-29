let gridBox = document.querySelector(".gridBox");
let slider = document.querySelector("#slider");
let currValue = document.querySelector(".currValue");
let setGrid = document.querySelector(".clickSetGrid");
let reset = document.querySelector(".reset-button");


// default is set to 5 (from index.html)
let userChoice = 5; 
makeGrid(userChoice);


// sets the grid according the slider value
setGrid.addEventListener("click", ()=>{
	location.reload();
	makeGrid(slider.value);
	userChoice = slider.value;

});


// ready to draw
let trail = document.querySelectorAll(".item");
trail.forEach(function (currentValue) {
  currentValue.addEventListener("mouseover", () => {
    currentValue.classList.add("touched");
  });
});

// ready to reset
trail.forEach(function (currentValue) {
  reset.addEventListener("click", () => {
    currentValue.classList.remove("touched");
  });
});

function makeGrid(userChoice) {
  gridBox.setAttribute(
    "style",
    `grid-template: repeat(${userChoice}, 1fr) / repeat(${userChoice}, 1fr);`
  );

  for (let i = 1; i <= userChoice ** 2; i++) {
    let item = document.createElement("div");
    item.classList.add("item");
    gridBox.appendChild(item);
  }
}
