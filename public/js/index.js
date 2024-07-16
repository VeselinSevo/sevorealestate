import { formatPrice, capitalizeFirstLetter } from "../../util/formatting-functions.js";

async function getProperties() {
    const urlParams = new URLSearchParams(window.location.search);
    const search = urlParams.get('search') || '';

    try {
        const response = await fetch(`services/property/get-properties.php?search=${encodeURIComponent(search)}`);
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        const properties = await response.json();
        renderProperties(properties);
    } catch (error) {
        console.error('Error fetching data:', error);
    }
}

async function getCountries() {
    try {
        const response = await fetch('services/property/get-countries.php');
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        const countries = await response.json();
        renderCountriesDdl(countries);
    } catch (error) {
        console.error('Error fetching data:', error);
    }
}

async function getCities(countryId) {
    try {
        const response = await fetch(`services/property/get-cities.php?country_id=${countryId}`);
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        const cities = await response.json();
        renderCitiesDdl(cities);
    } catch (error) {
        console.error('Error fetching data:', error);
    }
}

async function getTypes() {
    try {
        const response = await fetch('services/property/get-types.php');
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        const types = await response.json();
        renderTypeDdl(types);
    } catch (error) {
        console.error('Error fetching data:', error);
    }
}

function renderProperties(properties) {
    const container = document.getElementById('properties-container');
    let html = '';
    properties.forEach(property => {
        let formattedPrice = property.price;
        if (selectedCurrency) {
            formattedPrice = formatPrice(property.price, selectedCurrency);
        }

        const imageSrcString = `https://flagcdn.com/w320/${property.country_short_name}.png`;

        html += `
            <a href="property.php?id=${property.id}" class="h-full">
                <div class="bg-white rounded-md shadow-md hover:bg-indigo-50 cursor-pointer p-4 gap-3 flex flex-col items-center h-full">
                    <div class="relative img-container h-60 md:h-40 w-full md:w-full">
                        <img src="store/property/main-images/${property.image_name}" alt="Property Image" class="w-full h-full object-cover rounded-md">
                        <div class="absolute text-xs font-medium text-indigo-600 bg-white bottom-2 left-2 p-1 rounded-md">
                            ${parseInt(property.size)} m<sup>2</sup>
                        </div>
                    </div>
                    <div class="self-start w-full">
                        <h1>${capitalizeFirstLetter(property.type_name)}</h1>
                        <h3 class="text-base md:text-lg font-semibold mb-2">${property.name}</h3>
                        <p class="text-sm md:text-sm text-gray-700 mb-2 line-clamp-3 overflow-ellipsis h-10">${property.description}</p>
                        <div class="flex justify-between mt-2">
                            <span class="text-sm md:text-sm text-gray-600 mr-2"><i class="fas fa-map-marker-alt"></i> ${property.city_name}</span>
                            <p class="text-base md:text-base text-indigo-600 font-semibold">${formattedPrice}</p>
                        </div>
                    </div>
                </div>
            </a>
        `;
    });

    container.innerHTML = html;
}

function renderCountriesDdl(countries) {
    const countryDdl = document.getElementById('countryFilter');
    countryDdl.innerHTML = `<option value="">All</option>`;
    countries.forEach(country => {
        countryDdl.innerHTML += `<option value=${country.id}>${country.name}</option>`;
    });
}

function renderCitiesDdl(cities) {
    const cityDdl = document.getElementById('cityFilter');
    cityDdl.innerHTML = `<option value="">All</option>`;
    cities.forEach(city => {
        cityDdl.innerHTML += `<option value=${city.id}>${city.name}</option>`;
    });
}

function renderTypeDdl(types) {
    const typeDdl = document.getElementById('typeFilter');
    typeDdl.innerHTML = `<option value="">All</option>`;
    types.forEach(type => {
        typeDdl.innerHTML += `<option value=${type.id}>${capitalizeFirstLetter(type.name)}</option>`;
    });
}

document.getElementById('searchForm').addEventListener('submit', function(event) {
    event.preventDefault();
    const searchInput = document.getElementById('search').value;
    const url = new URL(window.location);
    url.searchParams.set('search', searchInput);
    window.history.pushState({}, '', url);
    getProperties();
});

document.addEventListener("DOMContentLoaded", function () {
    var toggleFiltersButton = document.getElementById('toggleFiltersBtn');
    var filterSection = document.getElementById('filtersSection');
    var displayThreeBtn = document.getElementById('displayThreeBtn');
    var displayFourBtn = document.getElementById('displayFourBtn');
    var propertiesContainer = document.getElementById('properties-container');

    toggleFiltersButton.addEventListener('click', function() {
        filterSection.classList.toggle('hidden');
        toggleFiltersButton.classList.toggle('text-indigo-600');
    });

    displayThreeBtn.addEventListener('click', function() {
        var imageContainers = document.querySelectorAll('.img-container');

        imageContainers.forEach(imageContainer => {
            imageContainer.classList.remove('md:h-40');
            imageContainer.classList.add('md:h-65');
        });

        propertiesContainer.classList.remove('md:grid-cols-4', 'lg:grid-cols-4');
        propertiesContainer.classList.add('md:grid-cols-3', 'lg:grid-cols-3');
        displayFourBtn.classList.remove('text-indigo-600');
        displayThreeBtn.classList.add('text-indigo-600');
    });

    displayFourBtn.addEventListener('click', function() {
        var imageContainers = document.querySelectorAll('.img-container');

        imageContainers.forEach(imageContainer => {
            imageContainer.classList.remove('md:h-65');
            imageContainer.classList.add('md:h-40');
        });

        propertiesContainer.classList.remove('md:grid-cols-3', 'lg:grid-cols-3');
        propertiesContainer.classList.add('md:grid-cols-4', 'lg:grid-cols-4');
        displayThreeBtn.classList.remove('text-indigo-600');
        displayFourBtn.classList.add('text-indigo-600');
    });

    const countrySelect = document.getElementById('countryFilter');
    const citySelect = document.getElementById('cityFilter');

    citySelect.disabled = true;

    countrySelect.addEventListener('click', function() {
        let countryId = countrySelect.value;
        if (countryId !== '') {
            citySelect.disabled = false;
        } else {
            citySelect.disabled = true;
        }
        getCities(countryId);
    });
});

const minPriceInput = document.getElementById('minPrice');
const maxPriceInput = document.getElementById('maxPrice');
const minSizeInput = document.getElementById('minSize');
const maxSizeInput = document.getElementById('maxSize');
const errorMsg = document.getElementById('errorMsg');

minPriceInput.addEventListener('change', restrictNegativeNumberAndMinMax);
maxPriceInput.addEventListener('change', restrictNegativeNumberAndMinMax);
minSizeInput.addEventListener('change', restrictNegativeNumberAndMinMax);
maxSizeInput.addEventListener('change', restrictNegativeNumberAndMinMax);

function restrictNegativeNumberAndMinMax(event) {
    const input = event.target;
    const value = parseFloat(input.value);
    const minPrice = parseFloat(minPriceInput.value);
    const maxPrice = parseFloat(maxPriceInput.value);
    const minSize = parseFloat(minSizeInput.value);
    const maxSize = parseFloat(maxSizeInput.value);

    if (value < 0) {
        showErrorMsg('You cannot enter a negative number.');
        clearInput(input);
    } else {
        hideErrorMsg();
        adjustMinMaxValues(input, value, minPrice, maxPrice, minSize, maxSize);
    }
}

function adjustMinMaxValues(input, value, minPrice, maxPrice, minSize, maxSize) {
    if (input.id === 'minPrice') {
        adjustMinPriceValue(value, maxPrice);
    } else if (input.id === 'maxPrice') {
        adjustMaxPriceValue(value, minPrice);
    } else if (input.id === 'minSize') {
        adjustMinSizeValue(value, maxSize);
    } else if (input.id === 'maxSize') {
        adjustMaxSizeValue(value, minSize);
    }
}

function adjustMinPriceValue(value, max) {
    if (value > max) {
        showErrorMsg('Min value cannot exceed the max value.');
        clearInput(minPriceInput);
    } else {
        minPriceInput.value = value;
    }
}

function adjustMaxPriceValue(value, min) {
    if (value < min) {
        showErrorMsg('Max value cannot be less than the min value.');
        clearInput(maxPriceInput);
    } else {
        maxPriceInput.value = value;
    }
}

function adjustMinSizeValue(value, max) {
    if (value > max) {
        showErrorMsg('Min value cannot exceed the max value.');
        clearInput(minSizeInput);
    } else {
        minSizeInput.value = value;
    }
}

function adjustMaxSizeValue(value, min) {
    if (value < min) {
        showErrorMsg('Max value cannot be less than the min value.');
        clearInput(maxSizeInput);
    } else {
        maxSizeInput.value = value;
    }
}

function showErrorMsg(msg) {
    errorMsg.innerText = msg;
    errorMsg.classList.remove('hidden');
}

function hideErrorMsg() {
    errorMsg.classList.add('hidden');
}

function clearInput(input) {
    input.value = '';
}

// Initialize properties and dropdowns on page load
getProperties();
getCountries();
getTypes();
