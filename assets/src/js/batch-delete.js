/** LightAdminBundle/assets/scripts/batch-delete.js */
/**
 * Function to handle the batch delete process
 */
export async function handleBatchDelete(event) {
    event.preventDefault(); // Prevent default form submission or link navigation

    // Get the delete button that triggered the event to extract data attributes
    const deleteButton = event.currentTarget;
    const url = deleteButton.dataset.url; // Get URL from data-url attribute
    const csrfToken = deleteButton.dataset.csrfToken; // Get CSRF token from data-csrf-token

    // Check if URL and CSRF token are available
    if (!url || !csrfToken) {
        console.error('Batch Delete: Missing URL or CSRF Token data attributes on the button.');
        alert('Deletion cannot proceed: Missing configuration data.');
        return;
    }

    let els = document.querySelectorAll('.batch-delete-checkbox'); // Use a more specific class for checkboxes
    let carrier = [];

    if (els) {
        Array.from(els).forEach(el => {
            if (el.checked) {
                carrier.push(el.value);
            }
        });
    }

    if (carrier.length === 0) {
        alert('Please select at least one item to delete.');
        return;
    }

    if (!confirm('Are you sure you want to delete the selected items?')) {
        return; // User cancelled the operation
    }

    // Send the carrier array to the server
    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                // Potentially add the CSRF token to headers if your backend expects it there
                'X-CSRF-Token': csrfToken,
            },
            body: JSON.stringify({
                items: carrier,
                _token: csrfToken, // Include the CSRF token in the body as well
            }),
        });

        if (response.ok) {
            console.log('Batch delete successful:', response);
            window.location.reload(); // Refresh the page after successful deletion
        } else {
            const errorData = await response.json();
            console.error('Batch delete failed:', errorData);
            alert(`Deletion failed: ${errorData.message || 'Unknown error'}`);
        }
    } catch (error) {
        console.error('Network or client-side error during batch delete:', error);
        alert('A network error occurred. Please try again.');
    }
}