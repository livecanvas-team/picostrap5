(function ($) {

    const palette_generator_html = `
        <div id="color-palette-generator" hidden style="background-color: #f8f9fa; border: 1px solid #dee2e6;margin-bottom:8px">
            <div style="background-color: #f0f0f1; padding: 1rem ; border-bottom: 1px solid #dee2e6;">
            
                <h2 style="font-size: 1.25rem;margin-bottom: 0;color: #000;display: inline;">Palette Generator <sup style="background: #0071eb;font-size: 0.5rem;padding: 1px 5px;color: #f0f0f1;border-radius: 2px;">AI</sup> 
                <a target="_blank" style="color:#bbc0c4;float:right" class="customize-help-toggle dashicons dashicons-editor-help" href="https://huemint.com/" title="Huemint Color Palette Generator"></a>
                </h2>
                <p style="font-size: 10px; line-height: 1.5; margin: 0.4em 0;">This tool will generate 6-color palettes, for body background, text body, light, dark, primary, secondary.</p>
            </div>

            <form id="color-palette-form" style="padding: 1rem; background-color: #fff;">
                <div style="margin-bottom: 1rem;">
                    <label style="font-size: 0.875rem;display:block;margin-bottom:2px" for="mode">Mode:</label>
                    <select style="font-size: 0.875rem; width: 100%;" id="mode">
                        <option value="transformer">Transformer</option>
                        <option value="diffusion">Diffusion</option>
                    </select>
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="font-size: 0.875rem;" for="temperature">Temperature <sup style="font-size: 0.625rem;">(0 - 2.4):</sup></label>
                    
                    <output id="output_temperature" style="display:inline; float:right; font-size: 0.875rem; border: 1px solid #f0f0f1; padding: 0.2rem 0.5rem; border-radius: 0.25rem;">1.2</output>
                    <input oninput="output_temperature.value = temperature.value" type="range" id="temperature" name="temperature" step="0.1" min="0" max="2.4" value="1.2" style="width: 100%;" required>
                </div>

                <div style="display: none; margin-bottom: 1rem;">
                    <label style="font-size: 0.875rem;" for="num_colors">Number of Colors (2-12):</label>
                    <span
                        style="font-size: 0.875rem; font-weight: bold; border: 1px solid #dee2e6; padding: 0.25rem 0.5rem; border-radius: 0.25rem;"
                        id="slider_color_value">6</span>
                    <input type="range" style="width: 100%;" id="num_colors" name="num_colors" min="2" max="12" value="6"
                        required>
                </div>

                <div style="margin-bottom: 1rem; border-bottom: 1px solid #dee2e6; padding-bottom: 1rem;">
                    <label style="font-size: 0.875rem;display:block;margin-bottom:2px" for="adjacency_matrix">Choose Contrast:</label>
                    <select style="font-size: 0.875rem; width: 100%;" type="select" id="adjacency_matrix"
                        name="adjacency_matrix" required aria-label="Matrix">
                        <option value="
                            0, 100, 20, 70, 50, 35,
                            100, 0, 90, 0, 0, 0,
                            20, 90, 0, 100, 0, 0,
                            70, 0, 100, 0, 65, 90,
                            50, 0, 0, 65, 0, 75,
                            35, 0, 0, 90, 75, 0
                        ">High Contrast</option>
                        <option selected value="
                            0,	90,	10,	60,	40,	25,
                            90,	0,	80,	0,	0,	0,
                            10,	80,	0,	90,	0,	0,
                            60,	0,	90,	0,	55,	80,
                            40,	0,	0,	55,	0,	65,
                            25,	0,	0,	80,	65,	0
                            ">Balanced Contrast</option>
                        <option value="
                            0, 50, 0, 20, 20, 5,
                            50, 0, 40, 0, 0, 0,
                            0, 40, 0, 50, 0, 0,
                            20, 0, 50, 0, 15, 40,
                            20, 0, 0, 15, 0, 25,
                            5, 0, 0, 40, 25, 0
                        ">Low Contrast</option>
                        <!-- 
                        <option value="
                            0, 0, 0, 0, 0, 0,
                            0, 0, 0, 0, 0, 0,
                            0, 0, 0, 0, 0, 0,
                            0, 0, 0, 0, 0, 0,
                            0, 0, 0, 0, 0, 0,
                            0, 0, 0, 0, 0, 0
                        ">Unconnected Colors</option>
                        -->
                    </select>
                </div>

                <div style="margin-bottom: 1rem; border-bottom: 1px solid #dee2e6; padding-bottom: 1rem;">
                    <label style="font-size: 0.875rem;display:block;margin-bottom:2px" for="color_type_select">Lock a Color: <span style="font-size: 9px;font-style: italic;">(optional)</span></label>
                    <select style="font-size: 0.875rem; width: 100%;" type="select" id="color_type_select"
                        name="color_type_select" required aria-label="Default select example">
                        <option value="None">None</option>
                        <option value="0">body-bg</option>
                        <option value="1">body-color</option>
                        <option value="2">light</option>
                        <option value="3">dark</option>
                        <option value="4">primary</option>
                        <option value="5">secondary</option>
                    </select>
                </div>

                <div style="margin-bottom: 1rem; display: none" id="color_selection">
                    <label style="font-size: 0.875rem;display:block;margin-bottom:2px" for="locked_colors">Choose Color:</label>
                    <input type="color" style="font-size: 0.875rem; width: 100%;" id="locked_colors" name="locked_colors"
                        value="#563d7c" title="Choose your colors">
                </div>

                <button
                    style="font-size: 0.875rem; background-color: #007bff; color: #fff; border: none; padding: 0.5rem 1rem; cursor: pointer;"
                    type="button" id="generate-button">Generate Palettes</button>
            </form>
            <div class="result" style="margin-top: 1rem; padding: 0 1rem;">
                <textarea style="display: none; width: 100%; border: 1px solid #dee2e6; border-radius: 0.25rem;"
                    id="result-palette" rows="1" readonly></textarea>
            </div>
            <div class="result-palettes" style="background-color: #fff;">
            </div>
        </div>
    
    `;


    //SUPPORT FUNCTION
    function setColorWidget(theSuffix = "body-bg", theValue = "#ffcc99") {
        document.querySelector(`[id^='customize-control-SCSSvar_${theSuffix}'] input`).value = theValue;
        document.querySelector(`[id^='customize-control-SCSSvar_${theSuffix}'] input`).dispatchEvent(new Event("change"));
    }

    
    function pico_add_to_customizer(section_name, html) {
        $("#sub-accordion-" + section_name + " li:first ").after('<div class="pico-extra-customizer-actions-wrap"> ' + html + ' </div>');
    }

    //////ON DOCUMENT READY
    $(document).ready(function () {

        //ADD THE COLOR PALETTE GENERATOR HTML STRUCTURE
        $("#sub-accordion-section-colors li:first ").after(palette_generator_html);
        //$("li#accordion-section-themes").after(palette_generator_html); //for developing, and quicker testing
        

        //ADD ACTION BUTTONS
        pico_add_to_customizer("section-colors", "<a class='button style-guide-link' href='#colors'         >" + theStyleGuideIcon + "Style Guide</a> <a class='button toggle-palette-generator' href='#'><svg style='vertical-align: middle; height:11px; width: 11px; margin-right: 5px; margin-top: -1px; ' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 128 128' fill='none'> <g clip-path='url(#clip0_1517_33)'> <path d='M128 128L128 85L85.3333 85V128H128Z' fill='#212121'/> <path d='M128 43L128 0L85.3333 0V43L128 43Z' fill='#525252'/> <path d='M128 85.3333L128 42.6666L85 42.6666V85.3333H128Z' fill='#363636'/> <path d='M85.3333 85.3333L85.3333 42.6666L42.3333 42.6666L42.3333 85.3333H85.3333Z' fill='#6F6F6F'/> <path d='M42.6667 43L42.6667 0L8.01086e-05 0L8.01086e-05 43L42.6667 43Z' fill='#B2B1B1'/> <path d='M42.6667 128L42.6667 85L7.62939e-05 85L7.62939e-05 128H42.6667Z' fill='#4A4A4A'/> <path d='M42.6667 85.3333L42.6667 42.6666L7.62939e-05 42.6666L7.62939e-05 85.3333H42.6667Z' fill='#9E9E9E'/> </g> <defs> <clipPath id='clip0_1517_33'> <rect width='128' height='128' fill='white'/> </clipPath> </defs> </svg> AI Palette Generator</a> ");
        pico_add_to_customizer("section-typography", "<a class='button style-guide-link' href='#typography'     >" + theStyleGuideIcon + "Style Guide</a> <a class='button' href='#' id='cs-show-combi'><span style='font-family: serif; font-size: 11px'>Aa </span>Font Combinations...</a>");
        pico_add_to_customizer("section-components", "<a class='button style-guide-link' href='#sample-sections'>" + theStyleGuideIcon + "Style Guide</a> ");
        pico_add_to_customizer("section-buttons", "<a class='button style-guide-link' href='#buttons'  >      " + theStyleGuideIcon + "Style Guide</a> ");
        //pico_add_to_customizer("section-buttons-forms", "");
        //pico_add_to_customizer("section-nav", "");

        //DEFINE BEHAVIOURS
        const resultPalette = document.getElementById("result-palette");

        //USER CLICKS TOGGLE: REVEAL / HIDE PALETTE GENERATOR
        document.querySelector(".toggle-palette-generator").addEventListener("click", function () {
            this.classList.toggle('active'); //toggle class on button
            document.querySelector("#color-palette-generator").toggleAttribute("hidden"); //show/hide the palette generator
        });

        // USER CHANGES COLOR SELECT: SHOW / HIDE COLOR PICKER
        document.getElementById('color_type_select').addEventListener('change', function () {
            const colorSelector = document.getElementById('color_selection');
            if (this.value == 'None') colorSelector.style.display = 'none'; else colorSelector.style.display = 'block';
        });

        //USER CLICKS GENERATE BUTTON: GENERATE PALETTE
        document.getElementById("generate-button").addEventListener("click", function () {
            //disable the button
            this.disabled = true;

            // Get input values
            const mode = document.getElementById("mode").value;
            const numColors = document.getElementById("num_colors").value;
            const temperature = document.getElementById("temperature").value;
            const adjacency = document.getElementById("adjacency_matrix").value;

            const colorIndex = document.getElementById("color_type_select").value;
            let palette = ["-", "-", "-", "-", "-", "-", "-", "-", "-", "-"];

            // if the color index chose isn't none, then set the color in the right position
            if (colorIndex != "None") {
                palette[colorIndex] = document.getElementById("locked_colors").value;
            }

            // Construct the JSON data object
            const jsonData = {
                mode: mode,
                num_colors: numColors,
                temperature: temperature,
                num_results: 10,
                adjacency: adjacency.split(",").map(function (item, index, arr) {
                    if (index === 0 || index % 11 == 0) {
                        return parseInt(item);
                    } else {
                        return item.toString().trim();
                    }
                }),
                palette: palette,
            };

            // Show loader
            //document.querySelector(".result-palettes").innerHTML = '<div id="pg-feedback" style="position:relative;padding:12px 24px; font-family:monospace;overflow:hidden"> <div style="background: -webkit-gradient(linear, left center, right center, from(#f74843), color-stop(24%, #f74843), color-stop(25%, #ffa067), color-stop(49%, #ffa067), color-stop(50%, #81d565), color-stop(74%, #81d565), color-stop(74%, #3a82e4), color-stop(100%, #3a82e4)); width: 60px; height: 500px; opacity: 1; -webkit-transform: rotate(25deg); position: absolute; z-index: 1;right:0 " id="pg-feedback"></div> Working... </div>';
            document.querySelector(".result-palettes").innerHTML =
                '<div id="pg-feedback"><div id="pg-feedback-bars"></div> Working<span class="pg-feedback-dot">.</span><span class="pg-feedback-dot">.</span><span class="pg-feedback-dot">.</span></div>';

            // Make the AJAX request
            const xhr = new XMLHttpRequest();

            xhr.open("POST", "https://api.huemint.com/color", true);
            xhr.setRequestHeader("Content-Type", "application/json;charset=UTF-8");

            // When we get a response
            xhr.onload = function () {
                document.querySelector(".result-palettes").innerHTML = "";
                if (xhr.status >= 200 && xhr.status < 300) {
                    const response = JSON.parse(xhr.responseText);
                    console.log(response); // Log the entire API response

                    // Check if response.results is defined and is an array
                    if (response && Array.isArray(response.results)) {
                        //write message into results div: Choose Palette
                        document.querySelector(".result-palettes").innerHTML =
                            '<h2 style="font-size: 1.25rem; margin: 0 !important; padding: 12px;">Choose Palette</h2>';

                        // Loop through each result and create HTML elements for palettes
                        response.results.forEach((result, index) => {
                            // Create a new div for this palette
                            const paletteDiv = document.createElement("div");
                            paletteDiv.style.backgroundColor = "#f8f9fa";
                            paletteDiv.style.border = "1px solid #dee2e6";
                            paletteDiv.style.padding = "1rem";
                            paletteDiv.style.marginBottom = "1rem";
                            paletteDiv.style.display = "flex"; 
                            paletteDiv.style.flexWrap = "wrap";
                            paletteDiv.style.gap = "0.5rem";
                            paletteDiv.style.justifyContent = "space-between"; 


                            // Loop through the colors in the palette and create color boxes
                            result.palette.forEach((color, colorIndex) => {
                                // Create a div for the color box
                                const colorBoxDiv = document.createElement("div");
                                colorBoxDiv.style.display = "flex";
                                colorBoxDiv.style.alignItems = "center";
                                colorBoxDiv.style.gap = "0.2rem";
                                colorBoxDiv.setAttribute("data-color", color);
                                colorBoxDiv.setAttribute("data-index", colorIndex);

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
                            document.querySelector(".result-palettes").appendChild(paletteDiv);

                            //scroll to it
                            document.querySelector(".result-palettes").scrollIntoView({ behavior: "smooth", block: "start", inline: "nearest", });
                            document.getElementById("generate-button").disabled = false;
                        });
                    } else {
                        // Handle the case where response.results is undefined or not an array
                        resultPalette.value = "Error: Invalid API response format.";
                    }
                } else {
                    // Handle the case where the AJAX request fails
                    resultPalette.value =
                        "Error: AJAX request failed with status " + xhr.status;
                }
            };

            // Error Handling
            xhr.onerror = function () {
                resultPalette.value = "Error: AJAX request failed.";
            };

            // Send request with JSON data
            xhr.send(JSON.stringify(jsonData));
        });

        //ON CLICK OF A PALETTE, APPLY IT
        $("body").on("click", ".result-palettes > DIV", function (e) {
            e.preventDefault();

            setColorWidget("body-bg", $(this).find("> DIV:eq(0)").attr("data-color"));
            setColorWidget("body-color", $(this).find("> DIV:eq(1)").attr("data-color"));
            setColorWidget("light", $(this).find("> DIV:eq(2)").attr("data-color"));
            setColorWidget("dark", $(this).find("> DIV:eq(3)").attr("data-color"));
            setColorWidget("primary", $(this).find("> DIV:eq(4)").attr("data-color"));
            setColorWidget("secondary", $(this).find("> DIV:eq(5)").attr("data-color"));

            /*setColorWidget("success", $(this).find("> DIV:eq(6)").attr("data-color"));
            setColorWidget("danger", $(this).find("> DIV:eq(7)").attr("data-color"));
            setColorWidget("warning", $(this).find("> DIV:eq(8)").attr("data-color"));
            setColorWidget("info", $(this).find("> DIV:eq(9)").attr("data-color"));
            */
        }); // end onClick
    }); //end doc ready
})(jQuery);
