function printDocument() {
    document.getElementById('print_panel').style.display='none'; 
    document.getElementById('print_content').classList.toggle("print-padding");
    window.print(); 
    document.getElementById('print_panel').style.display='block';
    document.getElementById('print_content').classList.toggle("print-padding");
}