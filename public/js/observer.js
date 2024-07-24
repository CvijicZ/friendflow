
import { getNumberOfUnseenMessages } from '/friendflow/public/js/chat.js';

// Observer
export function initializeObserver() {
    const observer = new IntersectionObserver(handleIntersect, { threshold: 0.5 });
    const unseenMessages = document.querySelectorAll('.unseen');

    unseenMessages.forEach(message => {
        observer.observe(message);
    });
}
initializeObserver();

let hasBeenUpdated = new Set(); // To keep track of processed messages

function handleIntersect(entries, observer) {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const messageElement = $(entry.target);
            const messageId = messageElement.data('id');

            if (!hasBeenUpdated.has(messageId)) {
                hasBeenUpdated.add(messageId);

                $.ajax({
                    url: '/friendflow/update-message-status',
                    method: 'POST',
                    data: {
                        message_id: messageId,
                        csrf_token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        if (response.status === 'success') {
                            getNumberOfUnseenMessages();
                            messageElement.removeClass('unseen');
                            hasBeenUpdated.add(messageId); // Mark message as updated
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('AJAX Error:', error);
                    }
                });
                observer.unobserve(entry.target);
            }
        }
    });
}
