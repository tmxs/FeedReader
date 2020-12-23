const books = document.getElementById('books');

if (books) {

  books.addEventListener('click', e => {
    if (e.target.className === 'btn btn-danger delete-entry') {
        if (confirm('Are you sure ?')) {
          const id = e.target.getAttribute('data-id');

          fetch(`/library/delete/${id}`, {
            method: 'DELETE'
          }).then(res => window.location.reload());

        }
    }
    if (e.target.className === 'btn btn-primary edit-entry') {
          const id = e.target.getAttribute('data-id');
          fetch(`/library/edit/${id}`, {
            method: 'DELETE'
          }).then(openModal());
    }
  });

}


// var modal = document.getElementById('simpleModl');
// var btn = document.getElementsByClassName("add-book");
// var addCalendarEntry = document.getElementById('modalBtn');
// btn.addEventListener('click', openModal);
//
//
// var closeBtn = document.getElementsByClassName('closeBtn')[0];
// addCalendarEntry.addEventListener('click', openModal);
// closeBtn.addEventListener('click', closeModal);
// window.addEventListener('click',clickOutside);
//
// function openModal (){
//   modal.style.display = 'block';
// }
//
// function closeModal() {
//   modal.style.display = 'none';
// }
//
// function clickOutside(e) {
//   if (e.target == modal) {
//     modal.style.display = 'none';
//   }
// }

// function insertAfter(addCalendarEntryButton, toolbarButton) {
// 	    toolbarButton.parentNode.insertBefore(addCalendarEntryButton, toolbarButton.nextSibling);
// }
//
// var addCalendarEntryButton = document.getElementById('addCalendarEntry');
// var toolbarButton = document.getElementsByClassName('fc-today-button');
//
// insertAfter(addCalendarEntryButton, toolbarButton);
// submitData()

const sources = document.getElementById('sources');

if (sources) {

  sources.addEventListener('click', e => {
    if (e.target.className === 'btn btn-danger delete-entry') {
        if (confirm('Are you sure ?')) {
          const id = e.target.getAttribute('data-id');

          fetch(`/feeds/delete/${id}`, {
            method: 'DELETE'
          }).then(res => window.location.reload());

        }
    }
    if (e.target.className === 'btn btn-primary edit-entry') {
          const id = e.target.getAttribute('data-id');
          fetch(`/feeds/edit/${id}`, {
            method: 'DELETE'
          }).then(openModal());
    }
  });

}

/* Set the width of the side navigation to 250px */
function openNav() {
  document.getElementById("feed-menu").style.width = "250px";
}

/* Set the width of the side navigation to 0 */
function closeNav() {
  document.getElementById("feed-menu").style.width = "0";
  document.getElementById("feed-menu").classList.add("d-none");
}


var dropdown = document.getElementsByClassName("dropdown-toggle");
var i;

for (i = 0; i < dropdown.length; i++) {
  dropdown[i].addEventListener("click", function() {
    this.classList.toggle("active");
    var dropdownContent = this.nextElementSibling;
    if (dropdownContent.style.display === "block") {
      dropdownContent.style.display = "none";
    } else {
      dropdownContent.style.display = "block";
    }
  });
}
