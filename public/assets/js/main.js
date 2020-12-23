const feeds = document.getElementById('feeds');

if (feeds) {

    feeds.addEventListener('click', e => {
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

const categories = document.getElementById('categories');

if (categories) {

    categories.addEventListener('click', e => {
        if (e.target.className === 'btn btn-danger delete-entry') {
            if (confirm('Are you sure ?')) {
                const id = e.target.getAttribute('data-id');

                fetch(`/category/delete/${id}`, {
                    method: 'DELETE'
                }).then(res => window.location.reload());

            }
        }
        if (e.target.className === 'btn btn-primary edit-entry') {
            const id = e.target.getAttribute('data-id');
            fetch(`/category/edit/${id}`, {
                method: 'DELETE'
            }).then(openModal());
        }
    });

}
