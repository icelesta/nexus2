document.addEventListener('DOMContentLoaded', () => {

    console.log('NEXUS PREVIEW LOADED');

    const card = document.getElementById('nexus-preview-card');

    if (!card) {
        console.warn('Preview Card Not Found');
        return;
    }

    /*
    |--------------------------------------------------------------------------
    | Preview Elements
    |--------------------------------------------------------------------------
    */

    const previewImage = document.getElementById('preview-image');
    const previewCode = document.getElementById('preview-code');
    const previewName = document.getElementById('preview-name');
    const previewCategory = document.getElementById('preview-category');
    const previewBrand = document.getElementById('preview-brand');

    /*
    |--------------------------------------------------------------------------
    | Thumbnail List
    |--------------------------------------------------------------------------
    */

    const items = document.querySelectorAll('.nexus-item-thumbnail');

    console.log('Thumbnail Found :', items.length);

    items.forEach(item => {

        console.log('Item Dataset :', item.dataset);

        item.addEventListener('mouseenter', () => {

            console.log('Hover :', item.dataset.itemCode);

            item.classList.add('is-hover');

            if (previewImage) {
                previewImage.src =
                    item.dataset.itemImage ||
                    '/images/no-image.png';
            }

            if (previewCode) {
                previewCode.textContent =
                    item.dataset.itemCode || '-';
            }

            if (previewName) {
                previewName.textContent =
                    item.dataset.itemName || '-';
            }

            if (previewCategory) {
                previewCategory.textContent =
                    'Category : ' +
                    (item.dataset.itemCategory || '-');
            }

            if (previewBrand) {
                previewBrand.textContent =
                    'Brand : ' +
                    (item.dataset.itemBrand || '-');
            }

            card.classList.add('show');

        });

        item.addEventListener('mouseleave', () => {

            item.classList.remove('is-hover');

            card.classList.remove('show');

        });

    });

    /*
    |--------------------------------------------------------------------------
    | Follow Cursor
    |--------------------------------------------------------------------------
    */

    document.addEventListener('mousemove', (e) => {

        if (!card.classList.contains('show')) {
            return;
        }

        let left = e.clientX + 24;
        let top = e.clientY + 20;

        const cardWidth = card.offsetWidth;
        const cardHeight = card.offsetHeight;

        if (left + cardWidth > window.innerWidth) {
            left = e.clientX - cardWidth - 24;
        }

        if (top + cardHeight > window.innerHeight) {
            top = e.clientY - cardHeight - 24;
        }

        card.style.left = `${left}px`;
        card.style.top = `${top}px`;

    });

});