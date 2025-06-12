// Function untuk toggle jabatan list
function toggleJabatanList(elementId) {
    const element = document.getElementById(elementId);
    if (element) {
        element.classList.toggle('hidden');
        
        // Change icon from add to remove and vice versa
        const button = event.currentTarget;
        const icon = button.querySelector('.material-icons-outlined');
        
        if (icon) {
            if (element.classList.contains('hidden')) {
                icon.textContent = 'add';
            } else {
                icon.textContent = 'remove';
            }
        }
    }
}
