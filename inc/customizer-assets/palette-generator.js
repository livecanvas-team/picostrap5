const palette_generator_html = `

<style>
    .result-palettes > DIV:hover {
        cursor: pointer;
    }
    
    #pg-feedback {
        background-color: #1b1b1b;
        color: white;
        position: relative;
        padding: 12px 24px;
        font-family: monospace;
        overflow: hidden;
    }
    #pg-feedback-bars {
        background: -webkit-gradient(
        linear,
        left center,
        right center,
        from(#f74843),
        color-stop(24%, #f74843),
        color-stop(25%, #ffa067),
        color-stop(49%, #ffa067),
        color-stop(50%, #81d565),
        color-stop(74%, #81d565),
        color-stop(74%, #3a82e4),
        color-stop(100%, #3a82e4)
    );
        width: 32px;
        height: 128px;
        transform: rotate(25deg);
        position: absolute;
        z-index: 1;
        right: 12px;
        top: -24px;
    }

    .pg-feedback-dot {
        display: inline-block;
        opacity: 0;
        animation: dotAnimation 1s linear infinite;
      }
    
      .pg-feedback-dot:nth-child(1) {
        animation-delay: 0.2s;
      }
    
      .pg-feedback-dot:nth-child(2) {
        animation-delay: 0.4s;
      }
    
      .pg-feedback-dot:nth-child(3) {
        animation-delay: 0.6s;
      }
    
      @keyframes dotAnimation {
        0%, 100% {
          opacity: 0;
        }
        50% {
          opacity: 1;
        }
      }

    
</style>


    <div style="background-color: #f8f9fa;  top: 15px; right: 0; border: 1px solid #dee2e6;">
        <div style="background-color: #f0f0f1; padding: 1rem 1.5rem; border-bottom: 1px solid #dee2e6;">
            <a target="_blank" style="text-decoration:none" href="https://huemint.com/" title="Huemint Color Palette Generator">
                <img src="https://huemint.com/assets/img/logo-icon.svg" style="width: 12px; height:auto">
            </a>
            <h2 style="font-size: 1.25rem;margin-bottom: 0;color: #000;display: inline;">Palette Generator <sup style="background: #0071eb;font-size: 0.5rem;padding: 1px 5px;color: #f0f0f1;border-radius: 2px;">AI</sup></h2>
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
                    id="slider_color_value">10</span>
                <input type="range" style="width: 100%;" id="num_colors" name="num_colors" min="2" max="12" value="10"
                    required>
            </div>

            <div style="margin-bottom: 1rem; border-bottom: 1px solid #dee2e6; padding-bottom: 1rem;">
                <label style="font-size: 0.875rem;display:block;margin-bottom:2px" for="adjacency_matrix">Choose Contrast:</label>
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
                <label style="font-size: 0.875rem;display:block;margin-bottom:2px" for="locked_colors">Brand Color:</label>
                <input type="color" style="font-size: 0.875rem; width: 100%;" id="locked_colors" name="locked_colors"
                    value="#563d7c" title="Choose your Brand Colors">
            </div>

            <button
                style="font-size: 0.875rem; background-color: #007bff; color: #fff; border: none; padding: 0.5rem 1rem; cursor: pointer;"
                type="button" id="generate-button">Generate Palette</button>
        </form>
        <div class="result" style="margin-top: 1rem; padding: 0 1rem;">
            <textarea style="display: none; width: 100%; border: 1px solid #dee2e6; border-radius: 0.25rem;"
                id="result-palette" rows="1" readonly></textarea>
        </div>

    </div>

    <div class="result-palettes" style="background-color: #fff;">
    </div>

`;

(function ($) {
  $(document).ready(function () {
    //ADD THE COLOR PALETTE GENERATOR HTML STRUCTURE
    $("li#accordion-section-themes").after(palette_generator_html);

    //DEFINE BEHAVIOURS
    const generateButton = document.getElementById("generate-button");
    const resultPalette = document.getElementById("result-palette");

    generateButton.addEventListener("click", function () {
      // Get input values
      const mode = document.getElementById("mode").value;
      const numColors = document.getElementById("num_colors").value;
      const temperature = document.getElementById("temperature").value;
      const adjacency = document.getElementById("adjacency_matrix").value;
      const lockedColors = document
        .getElementById("locked_colors")
        .value.split(",");

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
        palette: [lockedColors[0], "-", "-", "-", "-", "-", "-", "-", "-", "-"],
      };

      // Empty the results container and give feedback
      //document.querySelector(".result-palettes").innerHTML = '<div id="pg-feedback" style="position:relative;padding:12px 24px; font-family:monospace;overflow:hidden"> <div style="background: -webkit-gradient(linear, left center, right center, from(#f74843), color-stop(24%, #f74843), color-stop(25%, #ffa067), color-stop(49%, #ffa067), color-stop(50%, #81d565), color-stop(74%, #81d565), color-stop(74%, #3a82e4), color-stop(100%, #3a82e4)); width: 60px; height: 500px; opacity: 1; -webkit-transform: rotate(25deg); position: absolute; z-index: 1;right:0 " id="pg-feedback"></div> Working... </div>';
      document.querySelector(".result-palettes").innerHTML =
        '<div id="pg-feedback"><div id="pg-feedback-bars"></div> Working<span class="pg-feedback-dot">.</span><span class="pg-feedback-dot">.</span><span class="pg-feedback-dot">.</span></div>';

      // Make the AJAX request
      const xhr = new XMLHttpRequest();

      xhr.open("POST", "https://api.huemint.com/color", true);
      xhr.setRequestHeader("Content-Type", "application/json;charset=UTF-8");

      // Definisci la funzione di gestione della risposta
      xhr.onload = function () {
        document.querySelector(".result-palettes").innerHTML = "";
        if (xhr.status >= 200 && xhr.status < 300) {
          const response = JSON.parse(xhr.responseText);
          console.log(response); // Log the entire API response

          // Check if response.results is defined and is an array
          if (response && Array.isArray(response.results)) {
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
              document
                .querySelector(".result-palettes")
                .appendChild(paletteDiv);
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

    //ON CLICK OF A PALETTE
    $("body").on("click", ".result-palettes > DIV", function (e) {
      e.preventDefault();
      console.log("chosen palette");

      function setColorWidget(theSuffix = "body-bg", theValue = "#ffcc99") {
        document.querySelector(
          `[id^='customize-control-SCSSvar_${theSuffix}'] input`
        ).value = theValue;
        document
          .querySelector(`[id^='customize-control-SCSSvar_${theSuffix}'] input`)
          .dispatchEvent(new Event("change"));
      }

      setColorWidget(
        "body-color",
        $(this).find("> DIV:eq(0)").attr("data-color")
      );
      setColorWidget("body-bg", $(this).find("> DIV:eq(1)").attr("data-color"));
      setColorWidget("light", $(this).find("> DIV:eq(2)").attr("data-color"));
      setColorWidget("dark", $(this).find("> DIV:eq(3)").attr("data-color"));
      setColorWidget("primary", $(this).find("> DIV:eq(4)").attr("data-color"));
      setColorWidget(
        "secondary",
        $(this).find("> DIV:eq(5)").attr("data-color")
      );
      setColorWidget("success", $(this).find("> DIV:eq(6)").attr("data-color"));
      setColorWidget("danger", $(this).find("> DIV:eq(7)").attr("data-color"));
      setColorWidget("warning", $(this).find("> DIV:eq(8)").attr("data-color"));
      setColorWidget("info", $(this).find("> DIV:eq(9)").attr("data-color"));

      updateScssPreviewDebounced();
      //ps_update_fonts_import_code_snippet();
    }); // end onClick
  }); //end doc ready
})(jQuery);
