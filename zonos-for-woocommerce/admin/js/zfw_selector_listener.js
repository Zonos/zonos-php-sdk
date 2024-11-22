document.addEventListener("DOMContentLoaded", function () {
    const items = document.querySelectorAll('.wrap select');
    if (items) {
        for (let i = 0; i < items.length; i++) {
            const customInput = document.querySelector(`.wrap input#${items[i].id}`);

            items[i].addEventListener('change', function () {
                if (items[i].value === 'custom' && customInput.classList.contains('hidden')) {
                    items[i].name = `${items[i].id}-ignore`;
                    customInput.disabled = false;
                    customInput.classList.remove('hidden');
                } else if (!customInput.classList.contains('hidden')) {
                    items[i].name = items[i].id;
                    customInput.disabled = true;
                    customInput.value = '';
                    customInput.classList.add('hidden');
                }
            });
        }
    }
});