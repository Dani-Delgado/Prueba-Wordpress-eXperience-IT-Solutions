jQuery(function ($) {

    function loadUsers(page = 1) {
        $.post(LU.ajax_url, {
            action: 'lu_get_users',
            nonce: LU.nonce,
            search: $('input[name="search"]').val(),
            fields: $('input[name="fields[]"]:checked')
                .map(function () { return $(this).val(); })
                .get(),
            page: page
        }, function (response) {
            $('#lu-results').html(response.html);

            let pagination = '';
            for (let i = 1; i <= response.total_pages; i++) {
                let activeClass = (i === response.current) ? 'active' : '';
                pagination += `<a href="#" class="lu-page ${activeClass}" data-page="${i}">${i}</a> `;
            }
            $('#lu-pagination').html(pagination);
        });
    }

    // Carga inicial con AJAX
    loadUsers();

    // Buscar
    $('#lu-search-form').on('submit', function (e) {
        e.preventDefault();
        loadUsers(1);
    });

    // Paginación
    $(document).on('click', '.lu-page', function (e) {
        e.preventDefault();
        loadUsers($(this).data('page'));
    });

});
