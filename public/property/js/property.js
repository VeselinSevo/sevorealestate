var swiper = new Swiper('.swiper-container', {
    // Optional parameters
    direction: 'horizontal',
    loop: true,
    slidesPerView: 2,
    spaceBetween: 20,
    // navigation: {
    //     nextEl: '.swiper-button-next',
    //     prevEl: '.swiper-button-prev',
    // },
    breakpoints: {
        // when window width is >= 768px
        768: {
            slidesPerView: 2,
            spaceBetween: 20
        },
        // when window width is >= 1024px
        1024: {
            slidesPerView: 4,
            spaceBetween: 20
        }
    }
});

async function fetchImages(propertyId) {
    try {
        const response = await fetch(`services/property/get-images.php?id=${propertyId}`);
        if (!response.ok) {
            throw new Error(`Error fetching images: ${response.statusText}`);
        }
        const imageNames = await response.json();
        console.log(imageNames)
        if (imageNames.error) {
            throw new Error(`Server error: ${imageNames.error}`);
        }
        return imageNames;
    } catch (error) {
        console.error(error);
        return [];
    }
}

async function displayImages(propertyId) {
    const images = await fetchImages(propertyId);
    console.log(images)
    const swiperWrapper = document.querySelector('.swiper-wrapper');
    if(!images) {
        swiperWrapper.innerHTML = '<p>No images available for this property.</p>';
        return;
    }
    if (images.length === 0) {
        swiperWrapper.innerHTML = '<p>No images available for this property.</p>';
        return;
    }
    swiperWrapper.innerHTML = images.map(image => `
        <div class="swiper-slide h-full">
            <img class="rounded-md h-full" src="store/property/images/${image}" />
        </div>
    `).join('');
}



