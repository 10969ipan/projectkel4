document.addEventListener('DOMContentLoaded', function() {
    window.openItemsModal = function(id) {
        const modal = document.getElementById('itemsModal-' + id);
        if (modal) modal.classList.remove('hidden');
    };
    window.closeItemsModal = function(id) {
        const modal = document.getElementById('itemsModal-' + id);
        if (modal) modal.classList.add('hidden');
    };
});
