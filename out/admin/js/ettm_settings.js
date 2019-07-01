window.addEventListener('load', function() {

    document.querySelectorAll('.activate-simple-view').forEach(function(item) {
        var parent = item.parentNode.parentNode;

        item.addEventListener('click', function(e) {
            e.preventDefault();
            item.classList.add('active');
            parent.querySelector('.activate-ext-view').classList.remove('active');

            // Show simple view and hide ext view.
            parent.querySelector('.ettm-view--simple').style.display = 'block';
            parent.querySelector('.ettm-view--ext').style.display = 'none';
        })
    });

    document.querySelectorAll('.activate-ext-view').forEach(function(item) {
        var parent = item.parentNode.parentNode;

        item.addEventListener('click', function(e) {
            e.preventDefault();
            item.classList.add('active');
            parent.querySelector('.activate-simple-view').classList.remove('active');

            // Show ext view and hide simple view.
            parent.querySelector('.ettm-view--simple').style.display = 'none';
            parent.querySelector('.ettm-view--ext').style.display = 'block';
        })
    });

    document.querySelectorAll('.double-binding').forEach(function(item) {

        var target = document.getElementById(item.dataset.target);
        if (target) {
            item.addEventListener('change', function(e) {
                target.checked = item.checked;
            });

            target.addEventListener('change', function(e) {
                item.checked = target.checked;
            })

            item.checked = target.checked;
        }
    });

});
