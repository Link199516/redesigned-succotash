(function($) {
    $(document).ready(function() {
        const $countdownElement = $('#napoleon-trial-countdown');
        const $notice = $('.napoleon-trial-notice');
        const $dismissButton = $notice.find('.notice-dismiss'); // WordPress default dismiss button

        if ($countdownElement.length === 0 || typeof napoleonTrial === 'undefined') {
            return; // Exit if countdown element or localized data is not found
        }

        const expirationTimestamp = napoleonTrial.expirationTimestamp;
        const ajaxurl = napoleonTrial.ajaxurl;
        const dismissNonce = napoleonTrial.dismissNonce;
        const expiredText = napoleonTrial.expiredText;
        const initialRemainingSeconds = napoleonTrial.initialRemainingSeconds;
        const thresholdSeconds = napoleonTrial.thresholdSeconds;
        const normalPrefix = napoleonTrial.normalPrefix;
        const normalSuffix = napoleonTrial.normalSuffix;
        const offerPrefix = napoleonTrial.offerPrefix;
        const offerSuffix = napoleonTrial.offerSuffix;
        const normalCtaUrl = napoleonTrial.normalCtaUrl;
        const normalCtaText = napoleonTrial.normalCtaText;
        const offerCtaUrl = napoleonTrial.offerCtaUrl;
        const offerCtaText = napoleonTrial.offerCtaText;

        const $noticeContent = $notice.find('.art-notice-content p');
        const $ctaButton = $notice.find('.art-install-button');

        // Ensure the notice is visible without animation
        $notice.show(); // Use jQuery's show() for instant display

        function updateCountdown() {
            const currentTime = Math.floor(Date.now() / 1000); // Current time in seconds
            const remainingSeconds = expirationTimestamp - currentTime;

            console.log('napoleonTrial.expirationTimestamp:', expirationTimestamp);
            console.log('Current Time:', currentTime);
            console.log('Remaining Seconds:', remainingSeconds);

            if (remainingSeconds <= 0) {
                $countdownElement.text(expiredText);
                $notice.removeClass('is-dismissible').slideUp(); // Hide and remove dismiss functionality
                clearInterval(countdownInterval);
                return;
            }

            const totalHours = Math.floor(remainingSeconds / 3600);
            const minutes = Math.floor((remainingSeconds % 3600) / 60);
            const seconds = remainingSeconds % 60;

            let countdownParts = [];
            if (totalHours > 0) {
                countdownParts.push(totalHours + (totalHours === 1 ? ' hour' : ' hours'));
            }
            if (minutes > 0) {
                countdownParts.push(minutes + (minutes === 1 ? ' minute' : ' minutes'));
            }
            if (seconds > 0 || countdownParts.length === 0) { // Always show seconds if no other units, or if seconds are remaining
                countdownParts.push(seconds + (seconds === 1 ? ' second' : ' seconds'));
            }

            let countdownDisplay = countdownParts.join(', ');
            if (countdownParts.length > 1) {
                const lastPart = countdownParts.pop();
                countdownDisplay = countdownParts.join(', ') + ' and ' + lastPart;
            }

            // Determine which text and CTA to use
            let currentPrefix = normalPrefix;
            let currentSuffix = normalSuffix;
            let currentCtaUrl = normalCtaUrl;
            let currentCtaText = normalCtaText;

            if (remainingSeconds < thresholdSeconds) {
                currentPrefix = offerPrefix;
                currentSuffix = offerSuffix;
                currentCtaUrl = offerCtaUrl;
                currentCtaText = offerCtaText;
            }

            // Update the countdown element's text and the CTA button
            $countdownElement.text(countdownDisplay); // Directly update the countdown text
            $ctaButton.attr('href', currentCtaUrl).text(currentCtaText);
        }

        // Initial call to display countdown immediately
        updateCountdown();

        // Update countdown every second
        const countdownInterval = setInterval(updateCountdown, 1000);

        // Handle dismiss button click
        $dismissButton.on('click', function() {
            // Send AJAX request to dismiss the notice
            $.post(ajaxurl, {
                action: 'napoleon_dismiss_trial_notice',
                dismissed: 'true',
                nonce: dismissNonce
            }).done(function(response) {
                if (response.success) {
                    // Notice will be hidden by WordPress's default dismiss behavior
                    clearInterval(countdownInterval); // Stop the countdown
                } else {
                    console.error('Failed to dismiss trial notice:', response.data.message);
                }
            }).fail(function(jqXHR, textStatus, errorThrown) {
                console.error('AJAX error dismissing trial notice:', textStatus, errorThrown);
            });
        });
    });
})(jQuery);
