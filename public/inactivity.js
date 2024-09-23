let inactivityTimeout;
let modal;
let isModalShown = false; // Track if modal is already shown

function resetInactivityTimeout() {
  clearTimeout(inactivityTimeout);

  // Set the timeout for 10 minute (600000 milliseconds)
  inactivityTimeout = setTimeout(showFullScreenPopup, 600000);
}

function showFullScreenPopup() {
    if (isModalShown) {
      return; // Exit if modal is already shown
    }
  
    // Create a modal element
    modal = document.createElement('div');
    modal.classList.add('dashboardModal'); // Updated class name
  
    // Add the modal content
    modal.innerHTML = `
    <style>body:has(#bigFrame) {
      overflow: hidden;
  }</style>
      <div class="modal-content p-0 mx-2">
        <span class="close" style="font-size: 32px;">&times;</span>
        <iframe id='bigFrame' src="${url}" style="min-height: 90vh;"></iframe>
      </div>
    `;
  
    // Append the modal to the body
    document.body.appendChild(modal);
  
    // Handle the close button click event
    const closeButton = modal.querySelector('.close');
    closeButton.addEventListener('click', closeFullScreenPopup);
  
    // Set isModalShown to true
    isModalShown = true;
  
    // Clear the inactivity timeout (to prevent subsequent modals)
    clearTimeout(inactivityTimeout);
  }
  

function closeFullScreenPopup() {
  // Remove the modal from the body
  document.body.removeChild(modal);

  // Set isModalShown to false
  isModalShown = false;

  // Reset the inactivity timeout
  resetInactivityTimeout();
}

// Reset the inactivity timeout whenever there is any user activity
window.addEventListener('mousemove', resetInactivityTimeout);
window.addEventListener('keypress', resetInactivityTimeout);

// Start the inactivity timeout initially
resetInactivityTimeout();


