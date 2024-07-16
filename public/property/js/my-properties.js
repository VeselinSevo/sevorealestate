import {formatPrice, capitalizeFirstLetter} from "../../../util/formatting-functions.js"

async function getProperties() {
    try {
        const response = await fetch(`services/property/get-my-properties.php?id=${userId}`);
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        const properties = await response.json();
        renderProperties(properties);
    } catch (error) {
        console.error('Error fetching data:', error);
    }
}

const noPropertiesMessage = document.querySelector('#no-properties-message')
function renderProperties(properties) {
    var container = document.getElementById('properties-container');
    var html = ''

    if(properties.length > 0) {
        noPropertiesMessage.classList.add('hidden')
    }
    properties.forEach(function(property) {
        console.log(property)
        console.log(property)
        let formattedPrice = formatPrice(property.price, selectedCurrency);
        html +=  `
    <div class="bg-white rounded-md shadow-md  hover:bg-indigo-50 cursor-pointer p-4 gap-3 flex flex-col items-center h-full">
        <a href="property.php?id=${property.id}" class="h-full">
        <div class="img-container relative h-60 md:h-40 w-full md:w-full">
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
        
        </a>
           <div class="flex justify-between mt-4 md:mt-6 gap-3">
                <a href="edit-property.php?id=${property.id}">
                    <button id="editButton" type="submit" class="flex w-20 justify-center rounded-md bg-indigo-700 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Edit</button>
                </a>
                <form action="services/property/delete-property.php" method="post">
                    <input name="property_id" class="hidden" value="${property.id}">
                    <button type="submit" class="flex w-20 justify-center rounded-md bg-red-500 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-red-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Delete</button>
                </form>
           </div>
        </div>
    </div>




`;
    });
    container.innerHTML = html;
}


getProperties()

document.addEventListener("DOMContentLoaded", function () {
    var displayFourBtn = document.getElementById('displayFourBtn');
    var displayFiveBtn = document.getElementById('displayFiveBtn');
    var propertiesContainer = document.getElementById('properties-container');


    displayFourBtn.addEventListener('click', function() {
        propertiesContainer.classList.remove('md:grid-cols-5', 'lg:grid-cols-5');
        propertiesContainer.classList.add('md:grid-cols-4', 'lg:grid-cols-4');
        displayFiveBtn.classList.remove('text-indigo-600')
        displayFourBtn.classList.add('text-indigo-600')


        var imageContainers = document.querySelectorAll('.img-container')
        console.log(imageContainers)
        imageContainers.forEach(imageContainer => {
            console.log(123)
            imageContainer.classList.remove('md:h-40')
            imageContainer.classList.add('md:h-65')

        })

    });

    displayFiveBtn.addEventListener('click', function() {
        propertiesContainer.classList.remove('md:grid-cols-4', 'lg:grid-cols-4');
        propertiesContainer.classList.add('md:grid-cols-5', 'lg:grid-cols-5');
        displayFourBtn.classList.remove('text-indigo-600')
        displayFiveBtn.classList.add('text-indigo-600')

        var imageContainers = document.querySelectorAll('.img-container')

        imageContainers.forEach(imageContainer => {
            console.log(123)
            imageContainer.classList.remove('md:h-65')
            imageContainer.classList.add('md:h-40')

        })
    });


});







