var input = document.querySelectorAll('input');
input.forEach(element => {
    element.addEventListener('input', resizeInput)
});

function resizeInput() {
  this.style.width = this.value.length + "ch";

  if(this.value.length < 1)
    this.style.width = "50px";
}