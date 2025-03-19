jQuery(document).ready(function($) {

    // 1. Identify which fields to target. We use a simple approach:
    //    - All textareas
    //    - All text inputs (if you only want to do this in Elementor,
    //      you can filter by a specific container or class).
    const textFields = $('textarea, input[type="text"], input.elementor-control-title');

    // 2. For each field, add an AI button
    textFields.each(function() {
        const $field = $(this);

        // Skip if we've already added a button
        if ($field.next('.swcg-ai-btn').length > 0) {
            return;
        }

        // Create the AI button
        const $btn = $('<button class="swcg-ai-btn" type="button" style="margin-left:5px;">AI</button>');
        $field.after($btn);

        // When clicked, open a small prompt UI or do something more advanced
        $btn.on('click', function(e) {
            e.preventDefault();

            // Let user type or refine a request for content
            const userPrompt = window.prompt('Enter your content prompt for AI:', '');
            if (!userPrompt) {
                return;
            }

            // Optionally, show a loading state
            $btn.text('Loading...').prop('disabled', true);

            // Send AJAX request to our plugin
            $.ajax({
                url: SWCG_Data.ajaxUrl,
                method: 'POST',
                data: {
                    action: 'swcg_generate_content',
                    security: SWCG_Data.nonce,
                    prompt: userPrompt
                },
                success: function(response) {
                    $btn.text('AI').prop('disabled', false);

                    if (response.success) {
                        // Insert generated content into the field
                        const newText = response.data.content || '';
                        
                        // Depending on your preference, you can:
                        // (A) Replace the entire field
                        $field.val(newText);

                        // (B) Append it:
                        // $field.val($field.val() + "\\n" + newText);
                    } else {
                        alert('Error: ' + (response.data.message || 'Unknown error'));
                    }
                },
                error: function(err) {
                    $btn.text('AI').prop('disabled', false);
                    alert('AJAX error. Check console for details.');
                    console.error(err);
                }
            });
        });
    });
});
