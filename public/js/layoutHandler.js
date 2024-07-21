$(document).ready(function () {

    function updateHeader() {
        var width = $(window).width();

        if (width >= 992) { // Large screens (desktops)

            $('.mobile-navbar').addClass('d-none');
            $('.main-content').removeClass('col-12').addClass('col-8');

            $('.desktop-navbar').removeClass('d-none');
            $('.sidebar').removeClass('d-none');
        } else { // Small screens (phones)

            // Create modal from original chat div on the page
            let $chatDiv = $('.chat');

            if ($chatDiv.length) {

                let $clonedChatDiv = $chatDiv.clone();

                $('#chatModalBody').append($clonedChatDiv);

                $chatDiv.remove();
            }
            $('.main-content').removeClass('col-8').addClass('col-12');
            $('.desktop-navbar').addClass('d-none');
            $('.mobile-navbar').removeClass('d-none');
            $('.sidebar').addClass('d-none');
            $('#postText').attr('rows', 2);
        }
    }

    updateHeader();
    $(window).resize(function () {
        updateHeader();
    });
});