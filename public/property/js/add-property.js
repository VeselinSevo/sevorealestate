import { capitalizeFirstLetter } from "../../../util/formatting-functions.js";

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
    console.log(countryId)
    try {
        const response = await fetch(`services/property/get-cities.php?country_id=${countryId}`);
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        const cities = await response.json();
        console.log(cities)
        renderCitiesDdl(cities);
    } catch (error) {
        console.error('Error fetching data:', error);
    }
}

async function getTypes() {
    try {
        const response = await fetch(`services/property/get-types.php`);
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        const cities = await response.json();
        renderTypeDdl(cities);
    } catch (error) {
        console.error('Error fetching data:', error);
    }
}

function renderCountriesDdl(countries) {
    var countryDdl = document.getElementById('countryDdl');
    countryDdl.innerHTML = ``;
    countries.forEach((country, index) => {
        const selected = index === 0 ? 'selected' : '';
        countryDdl.innerHTML += `
            <option value="${country.id}" ${selected}>${country.name}</option>
        `;
    });
}

function renderCitiesDdl(cities) {
    const cityDdl = document.getElementById('cityDdl');
    cityDdl.innerHTML = ` `;

    console.log(cities)
    if(cities == []) {
        return
    }
    cities.forEach((city, index) => {
        cityDdl.innerHTML += `
            <option value="${city.id}">${city.name}</option>
        `;
    });
}

function renderTypeDdl(types) {
    const typeDdl = document.getElementById('typeDdl');
    typeDdl.innerHTML = ``
    types.forEach(type => {
        console.log(type.id)
        typeDdl.innerHTML += `
                                <option value=${type.id}>${capitalizeFirstLetter(type.name)}</option>
                             `
    })
}

getCountries()
getTypes()

document.addEventListener("DOMContentLoaded", function () {

    const countrySelect = document.getElementById('countryDdl');
    const citySelect = document.getElementById('cityDdl');

    // Disable city input initially
    citySelect.disabled = true;

    // Enable city input when country is selected
    countrySelect.addEventListener('click', function () {

        let countryId = countrySelect.value
        if (countrySelect.value !== '') {
            citySelect.disabled = false;
        } else {
            citySelect.disabled = true;
        }
        console.log(countryId + "HSHAHSHSA")
        getCities(countryId)
    });



    const mainImageInput = document.querySelector('#main-image-input');
    const additionalImagesInput = document.querySelector('#additional-images-input');
    const dropContainers = document.querySelectorAll('.drop-container');
    const mainImageDropContainer = document.querySelector('#main-image-drop-container');
    const additionalImagesDropContainer = document.querySelector('#additional-images-drop-container');
    // dragover and dragenter events need to have 'preventDefault' called
    // in order for the 'drop' event to register.
    // See: https://developer.mozilla.org/en-US/docs/Web/Guide/HTML/Drag_operations#droptargets
    dropContainers.forEach(container => {
        container.ondragenter = function(e) {
            e.preventDefault();
            e.stopPropagation();
            this.classList.add("bg-gradient-to-r","from-indigo-200", "via-indigo-50", "to-indigo-200");
            this.classList.add("text-white");
        };
    })

    dropContainers.forEach(container => {
        container.ondragleave = function(e) {
            e.preventDefault();
            e.stopPropagation();
            this.classList.remove("bg-gradient-to-r","from-indigo-300", "via-indigo-100", "to-indigo-300");
            this.classList.remove("text-white");
        };
    })

    dropContainers.forEach(container => {
        container.ondragover = function(e) {
            e.preventDefault();
            e.stopPropagation();
        };
    })


    mainImageDropContainer.ondrop = function(e) {
        e.stopPropagation();
        e.preventDefault();
        console.log('Dropped in main image')
        mainImageInput.files = e.dataTransfer.files;

        const dT = new DataTransfer();
        dT.items.add(e.dataTransfer.files[0]);
        console.log(e.dataTransfer.files[0])
        mainImageInput.files = dT.files;
        updateImageDisplay(mainImageInput, mainImagePreview, mainImageDropContainer);
    };

    additionalImagesDropContainer.ondrop = function(e) {
        e.stopPropagation();
        e.preventDefault();
        console.log('Dropped in additional images')
        additionalImagesInput.files = e.dataTransfer.files;

        const dT = new DataTransfer();


        for(const file of e.dataTransfer.files) {
            console.log(file)
            dT.items.add(file)
        }

        console.log(dT.files)
        additionalImagesInput.files = dT.files;
        updateImageDisplay(additionalImagesInput, additionalImagesPreview, additionalImagesDropContainer)
    };


    const mainImagePreview = document.querySelector("#main-image-preview")
    const additionalImagesPreview = document.querySelector("#additional-images-preview")



    mainImageInput.addEventListener('change', () => updateImageDisplay(mainImageInput, mainImagePreview, mainImageDropContainer))
    additionalImagesInput.addEventListener('change', () => updateImageDisplay(additionalImagesInput, additionalImagesPreview, additionalImagesDropContainer))

    function updateImageDisplay(input, preview, dropContainer) {
        dropContainer.classList.add("bg-gradient-to-r","from-indigo-200", "via-indigo-50", "to-indigo-200");
        while (preview.firstChild) {
            preview.removeChild(preview.firstChild);
        }

        const curFiles = input.files;
        if (curFiles.length === 0) {
            const para = document.createElement("p");
            para.textContent = "No files currently selected for upload";
            preview.appendChild(para);
        } else {
            const list = document.createElement("ol");
            list.classList.add("flex", "flex-col", "w-full");
            preview.appendChild(list);

            for (const file of curFiles) {
                const listItem = document.createElement("li");
                listItem.classList.add("flex", "gap-5", "bg-gray-100", "p-5", "rounded-md")
                const para = document.createElement("p");
                if (validFileType(file)) {
                    para.textContent = `File name ${file.name}, file size ${returnFileSize(
                        file.size,
                    )}.`;
                    const image = document.createElement("img");
                    image.classList.add("h-24")
                    image.src = URL.createObjectURL(file);
                    image.alt = image.title = file.name;

                    listItem.appendChild(image);
                    listItem.appendChild(para);
                } else {
                    para.textContent = `File name ${file.name}: Not a valid file type. Update your selection.`;
                    listItem.appendChild(para);
                }

                list.appendChild(listItem);
            }
        }
    }

    const fileTypes = [
        "image/apng",
        "image/bmp",
        "image/gif",
        "image/jpeg",
        "image/pjpeg",
        "image/png",
        "image/svg+xml",
        "image/tiff",
        "image/webp",
        "image/x-icon",
    ];

    function validFileType(file) {
        return fileTypes.includes(file.type);
    }

    function returnFileSize(number) {
        if (number < 1024) {
            return `${number} bytes`;
        } else if (number >= 1024 && number < 1048576) {
            return `${(number / 1024).toFixed(1)} KB`;
        } else if (number >= 1048576) {
            return `${(number / 1048576).toFixed(1)} MB`;
        }
    }




    const steps = document.querySelectorAll('.step');
    const stepIndicator = document.getElementById('step-indicator');
    const nextButton = document.getElementById('next-step');
    const prevButton = document.getElementById('prev-step');
    const submitButton = document.getElementById('submit-property');

    let currentStep = 0;


    const stepIndicatorBoxes = document.querySelectorAll('.step-indicator-box');

    function updateStepIndicator() {
        stepIndicatorBoxes.forEach((box, index) => {
            if (index < currentStep) {
                box.classList.add('bg-indigo-600');
            } else {
                box.classList.remove('bg-indigo-600');
            }
        });
    }

    function showStep(stepIndex) {
        updateStepIndicator();
        steps.forEach((step, index) => {
            if (index === stepIndex) {
                step.classList.remove('hidden');
            } else {
                step.classList.add('hidden');
            }
        });

        if(stepIndex == 0) {
            prevButton.classList.add("hidden")
        } else {
            prevButton.classList.remove("hidden")
        }


        if(stepIndex == steps.length - 1)  {
            submitButton.classList.remove('hidden')
            nextButton.classList.add('hidden')
        } else {
            submitButton.classList.add('hidden')
            nextButton.classList.remove('hidden')
        }

        updateStepIndicator();
    }

    function goToNextStep() {
        if (currentStep < steps.length - 1) {
            currentStep++;
            showStep(currentStep);

        } else if (currentStep === steps.length - 1) {

            // document.getElementById('add-property-form').submit(); // Submit the form on the last step
        }
    }

    function goToPrevStep() {
        if (currentStep > 0) {
            currentStep--;
            showStep(currentStep);
        }
    }

    nextButton.addEventListener('click', goToNextStep);

    if (prevButton) { // Check if the previous button exists
        prevButton.addEventListener('click', goToPrevStep);
    }

    // Initial setup
    showStep(currentStep);


    const numberInputs = document.querySelectorAll('.number-input');

    // Add input event listener for each number input field
    numberInputs.forEach(input => {
        validateNumericInput(input);
    });

    showStep(currentStep);
});

function validateNumericInput(input) {
    console.log(123)
    input.addEventListener('input', function(event) {
        const value = event.target.value;
        const valid = /^\d*\.?\d*$/.test(value);
        if (!valid) {
            event.target.value = value.slice(0, -1); // Remove the last character if it's not a digit or dot
        }
    });
}
