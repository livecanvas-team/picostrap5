/// CONSTANTS
const bootstrapColors = ['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'light', 'dark'];

//SUPPORT FUNCTIONS
function smartCaseReplace(match, replacement) {
    if (match === match.toLowerCase()) return replacement.toLowerCase();
    if (match === match.toUpperCase()) return replacement.toUpperCase();
    if (match[0] === match[0].toUpperCase()) return replacement.charAt(0).toUpperCase() + replacement.slice(1).toLowerCase();
    return replacement;
}

function removeAfterLastSlash(url) {
    const lastSlashIndex = url.lastIndexOf('/');
    if (lastSlashIndex === -1) {
        return url;  // No slash found, return the original string
    }
    return url.substring(0, lastSlashIndex + 1);
}

//HANDLE USER ACTIONS  
(function ($) {
  
    //WHEN USER CLICKS LINK TO OPEN STYLE GUIDE
    $("body").on("click", ".style-guide-link", function (e) {

        e.preventDefault();

        const thePreviewDocument = document.querySelector('#customize-preview iframe').contentWindow.document;

        //first time, store the body in originalPageBody
        if (typeof window.originalPageBody === 'undefined') {
            window.originalPageBody = thePreviewDocument.querySelector('body').innerHTML;
        }
 
        //determine URL to load
        let theStyleGuideURL = document.querySelector('#style-guide-js').getAttribute("src");
        theStyleGuideURL = removeAfterLastSlash(theStyleGuideURL) + 'style-guide-body.html';

        //determine section to show
        const theID = $(this).attr("href");

        //load the url
        $.get(theStyleGuideURL, function (response) {

            //insert response in the preview body
            thePreviewDocument.querySelector('body').innerHTML = response;
            
            //build the dynamic parts: color guide 
            const masterContent = thePreviewDocument.getElementById('master-colors-primary').outerHTML;
            bootstrapColors.reverse().forEach(color => {
                if (color === 'primary') return;
                const newContent = masterContent.replace(/primary|PRIMARY|Primary/g, match => smartCaseReplace(match, color));
                const tempElement = document.createElement('div');
                tempElement.innerHTML = newContent;
                const newDiv = tempElement.firstElementChild;
                thePreviewDocument.getElementById('master-colors-primary').insertAdjacentElement('afterend', newDiv);
            });

            //define handler for shades pair button
            thePreviewDocument.getElementById("random_shades_pair").addEventListener('click', function () {
                let values = [25, 50, 100, 200, 300, 400, 600, 700, 800, 900];
                let elements = thePreviewDocument.querySelectorAll('.random_bg_text');
                let randomColor = bootstrapColors[Math.floor(Math.random() * bootstrapColors.length)];
                let randomValueIndex = Math.floor(Math.random() * values.length);
                let randomValueBg = values[randomValueIndex];
                let randomValueTextIndex = randomValueIndex + 3; // ensure at least 3 steps difference
                if (randomValueTextIndex >= values.length) {
                    randomValueTextIndex -= 4;
                }
                let randomValueText = values[randomValueTextIndex];

                elements.forEach(function (element) {
                    element.className = "random_bg_text"; // reset classes
                    element.classList.add("bg-" + randomColor + "-" + randomValueBg);
                    element.classList.add("text-" + randomColor + "-" + randomValueText);
                });

                let textElements = thePreviewDocument.querySelectorAll('.random_text');
                randomColor = bootstrapColors[Math.floor(Math.random() * bootstrapColors.length)];
                let randomValueIndex2 = randomValueTextIndex + 4; // ensure at least 4 steps difference from randomValueText
                if (randomValueIndex2 >= values.length) {
                    randomValueIndex2 -= 5;
                }
                let randomValue2 = values[randomValueIndex2];

                textElements.forEach(function (element) {
                    element.className = "random_text"; // reset classes
                    element.classList.add("text-" + randomColor + "-" + randomValue2);
                });
            });

            //scroll to chosen section
            thePreviewDocument.querySelector(theID).scrollIntoView({ behavior: "smooth", block: "start", inline: "nearest" });

        });
    });

    //WHEN USER RIGHT-CLICKS LINK TO OPEN STYLE GUIDE, RE-PUT ORIGINAL BODY
    $("body").on("contextmenu", ".style-guide-link", function (e) {

        e.preventDefault();
        console.log('remove style guide');

        const thePreviewDocument = document.querySelector('#customize-preview iframe').contentWindow.document;
 
        //if the style guide is already there, place the original body again  
        if (window.originalPageBody && thePreviewDocument.querySelector('body').innerHTML.includes('Style Guide')) {
            thePreviewDocument.querySelector('body').innerHTML = window.originalPageBody;
        }
 
    });

    //add here
})(jQuery);