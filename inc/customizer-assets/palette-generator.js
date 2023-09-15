const palette_generator_html =`


    <div style="background-color: #f8f9fa;  top: 15px; right: 0; border: 1px solid #dee2e6;">
        <div style="background-color: #007bff; padding: 1rem 1.5rem; border-bottom: 1px solid #dee2e6;">
            <h2 style="font-size: 1.25rem; margin-bottom: 0; color: #fff;">Palette Generator <sup>AI</sup></h2>
        </div>
        <form id="color-palette-form" style="padding: 1rem; background-color: #fff;">
            <div style="margin-bottom: 1rem;">
                <label style="font-size: 0.875rem;" for="mode">Mode:</label>
                <select style="font-size: 0.875rem; width: 100%;" id="mode">
                    <option value="transformer">Transformer</option>
                    <option value="diffusion">Diffusion</option>
                </select>
            </div>
            <div style="margin-bottom: 1rem;">
                <label style="font-size: 0.875rem;" for="temperature">Temperature <sup style="font-size: 0.625rem;">(0 -
                        2.4):</sup></label>
                <input style="font-size: 0.875rem; width: 100%;" type="number" id="temperature" name="temperature"
                    step="0.1" min="0" max="2.4" value="1.2" required>
            </div>

            <div style="display: none; margin-bottom: 1rem;">
                <label style="font-size: 0.875rem;" for="num_colors">Number of Colors (2-12):</label>
                <span
                    style="font-size: 0.875rem; font-weight: bold; border: 1px solid #dee2e6; padding: 0.25rem 0.5rem; border-radius: 0.25rem;"
                    id="slider_color_value">10</span>
                <input type="range" style="width: 100%;" id="num_colors" name="num_colors" min="2" max="12" value="10"
                    required>
            </div>

            <div style="margin-bottom: 1rem; border-bottom: 1px solid #dee2e6; padding-bottom: 1rem;">
                <label style="font-size: 0.875rem;" for="adjacency_matrix">Choose Contrast:</label>
                <select style="font-size: 0.875rem; width: 100%;" type="select" id="adjacency_matrix"
                    name="adjacency_matrix" required aria-label="Default select example">
                    
                    <option value="
                        0,	90,	75,	25,	25,	15,	15,	15,	15,	15,
                        90,	0,	75,	0,	50,	15,	15,	15,	15,	15,
                        75,	0,	0,	95,	75,	15,	15,	15,	15,	15,
                        50,	80,	90,	0,	75,	15,	15,	15,	15,	15,
                        25,	75,	60,	50,	0,	15,	15,	15,	15,	15,
                        15,	15,	15,	15,	15,	0,	15,	15,	15,	15,
                        15,	15,	15,	15,	15,	15,	0,	0,	15,	15,
                        15,	15,	15,	15,	15,	15,	15,	0,	15,	15,
                        15,	15,	15,	15,	15,	15,	15,	15,	0,	15,
                        15,	15,	15,	15,	15,	15,	15,	15,	15,	0">High Contrast</option>
                        <option selected value="
                        0,	75,	75,	25,	25,	15,	15,	15,	15,	15,
                        75,	0,	75,	0,	50,	15,	15,	15,	15,	15,
                        75,	0,	0,	95,	75,	15,	15,	15,	15,	15,
                        50,	80,	90,	0,	75,	15,	15,	15,	15,	15,
                        25,	75,	60,	50,	0,	15,	15,	15,	15,	15,
                        15,	15,	15,	15,	15,	0,	15,	15,	15,	15,
                        15,	15,	15,	15,	15,	15,	0,	0,	15,	15,
                        15,	15,	15,	15,	15,	15,	15,	0,	15,	15,
                        15,	15,	15,	15,	15,	15,	15,	15,	0,	15,
                        15,	15,	15,	15,	15,	15,	15,	15,	15,	0">Medium Contrast</option>
                        <option value="
                        0,	50,	75,	25,	25,	15,	15,	15,	15,	15,
                        50,	0,	75,	0,	50,	15,	15,	15,	15,	15,
                        75,	0,	0,	95,	75,	15,	15,	15,	15,	15,
                        50,	80,	90,	0,	75,	15,	15,	15,	15,	15,
                        25,	75,	60,	50,	0,	15,	15,	15,	15,	15,
                        15,	15,	15,	15,	15,	0,	15,	15,	15,	15,
                        15,	15,	15,	15,	15,	15,	0,	0,	15,	15,
                        15,	15,	15,	15,	15,	15,	15,	0,	15,	15,
                        15,	15,	15,	15,	15,	15,	15,	15,	0,	15,
                        15,	15,	15,	15,	15,	15,	15,	15,	15,	0">Normal</option>
                        <option value="
                        0,	30,	75,	25,	25,	15,	15,	15,	15,	15,
                        30,	0,	75,	0,	50,	15,	15,	15,	15,	15,
                        75,	0,	0,	95,	75,	15,	15,	15,	15,	15,
                        50,	80,	90,	0,	75,	15,	15,	15,	15,	15,
                        25,	75,	60,	50,	0,	15,	15,	15,	15,	15,
                        15,	15,	15,	15,	15,	0,	15,	15,	15,	15,
                        15,	15,	15,	15,	15,	15,	0,	0,	15,	15,
                        15,	15,	15,	15,	15,	15,	15,	0,	15,	15,
                        15,	15,	15,	15,	15,	15,	15,	15,	0,	15,
                        15,	15,	15,	15,	15,	15,	15,	15,	15,	0">Low Contrast</option>
                        
                </select>
            </div>

            <div style="margin-bottom: 1rem;">
                <label style="font-size: 0.875rem;" for="locked_colors">Brand Color:</label>
                <input type="color" style="font-size: 0.875rem; width: 100%;" id="locked_colors" name="locked_colors"
                    value="#563d7c" title="Choose your Brand Colors">
            </div>

            <button
                style="font-size: 0.875rem; background-color: #007bff; color: #fff; border: none; padding: 0.5rem 1rem; cursor: pointer;"
                type="button" id="generate-button">Generate Colors</button>
        </form>
        <div class="result" style="margin-top: 1rem; padding: 0 1rem;">
            <textarea style="display: none; width: 100%; border: 1px solid #dee2e6; border-radius: 0.25rem;"
                id="result-palette" rows="1" readonly></textarea>
        </div>

    </div>

    <div class="result-palettes" style="  background-color: #fff;">
    </div>

`;


(function ($) {
    $(document).ready(function () {

        //ADD THE COLOR PALETTE GENERATOR HTML STRUCTURE
        $("li#accordion-section-themes").after(palette_generator_html);

        //DEFINE BEHAVIOURS
        const generateButton = document.getElementById('generate-button');
        const resultPalette = document.getElementById('result-palette');

        generateButton.addEventListener('click', function () {
            // Get input values
            const mode = document.getElementById('mode').value;
            const numColors = document.getElementById('num_colors').value;
            const temperature = document.getElementById('temperature').value;
            const adjacency = document.getElementById('adjacency_matrix').value;
            const lockedColors = document.getElementById('locked_colors').value.split(',');

            // Construct the JSON data object
            const jsonData = {
                "mode": mode,
                "num_colors": numColors,
                "temperature": temperature,
                "num_results": 10,
                "adjacency": adjacency.split(','),
                "palette": lockedColors,
            };

            // Make the AJAX request
            // Crea una nuova richiesta XMLHttpRequest
            const xhr = new XMLHttpRequest();

            // Configura la richiesta
            xhr.open("POST", "https://api.huemint.com/color", true);
            xhr.setRequestHeader("Content-Type", "application/json;charset=UTF-8");


            // Definisci la funzione di gestione della risposta
            xhr.onload = function () {
                if (xhr.status >= 200 && xhr.status < 300) {
                    const response = JSON.parse(xhr.responseText);
                    console.log(response); // Log the entire API response

                    // Check if response.results is defined and is an array
                    if (response && Array.isArray(response.results)) {
                        // Get the container element where palettes will be displayed
                        const paletteContainer = document.querySelector(".result-palettes");

                        // Loop through each result and create HTML elements for palettes
                        response.results.forEach((result, index) => {
                            // Create a new div for this palette
                            const paletteDiv = document.createElement("div");
                            paletteDiv.style.backgroundColor = "#f8f9fa";
                            paletteDiv.style.border = "1px solid #dee2e6";
                            paletteDiv.style.padding = "1rem";
                            paletteDiv.style.marginBottom = "1rem";
                            paletteDiv.style.display = "grid";
                            paletteDiv.style.gridGap = "0.5rem";
                            paletteDiv.style.gridTemplateColumns = "1fr 1fr 1fr 1fr 1fr";

                            // Loop through the colors in the palette and create color boxes
                            result.palette.forEach((color, colorIndex) => {
                                // Create a div for the color box
                                const colorBoxDiv = document.createElement("div");
                                colorBoxDiv.style.display = "flex";
                                colorBoxDiv.style.alignItems = "center";
                                colorBoxDiv.style.gap = "0.2rem";

                                // Create a div for displaying the color
                                const colorDiv = document.createElement("div");
                                colorDiv.style.width = "24px";
                                colorDiv.style.height = "12px";
                                colorDiv.style.backgroundColor = color;
                                colorDiv.style.border = "1px solid #dee2e6";
                                colorDiv.style.borderRadius = "0.25rem";

                                // Create a div for displaying the color code
                                /*
                                const colorCodeDiv = document.createElement("div");
                                colorCodeDiv.style.fontSize = "0.875rem";
                                colorCodeDiv.style.paddingLeft = "0.5rem";
                                colorCodeDiv.style.paddingRight = "0.5rem";
                                colorCodeDiv.textContent = color;
                                */

                                // Append color and color code divs to the color box div
                                colorBoxDiv.appendChild(colorDiv);
                                //colorBoxDiv.appendChild(colorCodeDiv);

                                // Append the color box div to the palette div
                                paletteDiv.appendChild(colorBoxDiv);
                            });

                            // Append the palette div to the container
                            paletteContainer.appendChild(paletteDiv);
                        });
                    } else {
                        // Handle the case where response.results is undefined or not an array
                        resultPalette.value = "Error: Invalid API response format.";
                    }
                } else {
                    // Handle the case where the AJAX request fails
                    resultPalette.value = "Error: AJAX request failed with status " + xhr.status;
                }
            };

            // Definisci la funzione di gestione degli errori
            xhr.onerror = function () {
                resultPalette.value = "Error: AJAX request failed.";
            };

            // Invia la richiesta con i dati JSON
            xhr.send(JSON.stringify(jsonData));
        });




    }); //end doc ready

})(jQuery);



document.addEventListener('DOMContentLoaded', function () { 
    
});