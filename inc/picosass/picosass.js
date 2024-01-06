import * as sass from 'https://jspm.dev/sass';

export class Picosass {
  constructor(selector) {
    this.selector = selector || '#the-scss';
    this.init();
  }

  // Initialize the PicoSass environment
  init() {
    this.addFeedbackElement();
    window.addEventListener('DOMContentLoaded', () => {
      if (!document.querySelector(this.selector).classList.contains('prevent-autocompile')) {
        this.compile();
      }
    });
  }

  replaceLast = (str, pattern, replacement) => {
    const match =
      typeof pattern === 'string'
        ? pattern
        : (str.match(new RegExp(pattern.source, 'g')) || []).slice(-1)[0];
    if (!match) return str;
    const last = str.lastIndexOf(match);
    return last !== -1
      ? `${str.slice(0, last)}${replacement}${str.slice(last + match.length)}`
      : str;
  }

  // Add feedback element to the DOM
  addFeedbackElement = () => {
    const feedbackHtml = `
            <div id='picosass-output-feedback'></div> 
            <style> 
              #picosass-output-feedback { position: fixed; top:5px; z-index: 9999; width:80%;font-size:36px; background:#212337; color:lime; font-family:courier; border:8px solid red; padding:0 15px 10px 15px; display:block;   word-wrap: break-word;   }
              #picosass-output-feedback span{display:block; font-size:20px; z-index:0}
              #picosass-output-feedback:empty {display:none}
            </style>
        `;
    document.querySelector('html').insertAdjacentHTML('afterbegin', feedbackHtml);
  }

  compile = async (sassParams = {}) => {
    try {
      this.showFeedback('Compiling SCSS...');
      const theCode = document.querySelector(this.selector).innerHTML;
      if (!theCode.trim()) {
        console.log('Empty SCSS source, aborting');
        return;
      }

      const compiled = await this.runScssCompiler(theCode, sassParams);
      this.injectStyle(compiled.css);
      this.showCompiledFeedback(compiled.css);
    } catch (error) {
      this.showFeedback(`SCSS error: ${error}`, true);
    }
  }

  runScssCompiler = async (code, sassParams) => {
    const defaultParams = {
      style: 'compressed',
      importers: [{ canonicalize: this.canonicalize, load: this.load }],
      charset: false,
      ...sassParams
    };

    return await sass.compileStringAsync(code, defaultParams);
  }

  injectStyle = (css) => {
    const head = document.getElementsByTagName('head')[0];
    const styleTag = document.createElement('style');
    styleTag.innerHTML = css;
    head.appendChild(styleTag);
  }

  showFeedback = (message, isError = false) => {
    const feedbackElement = document.querySelector('#picosass-output-feedback');
    feedbackElement.innerHTML = message;
    if (isError) {
      // Additional error handling
    }
  }

  showCompiledFeedback = (css) => {
    const sizeInfo = `Approx. CSS bundle size: ${this.measureStringSizeInKB(css)} KB (${this.measureEstimatedGzippedSizeInKB(css)} KB gzipped)`;
    this.showFeedback(`SCSS compiled successfully. ${sizeInfo}`);
  }

  // Replace your support functions here (measureStringSizeInKB, measureEstimatedGzippedSizeInKB, basicGzip, canonicalize, load)
  measureStringSizeInKB =  (str) => {
    const encoder = new TextEncoder('utf-8');
    const bytes = encoder.encode(str);
    return Math.floor(bytes.length / 1024);
  }

  measureEstimatedGzippedSizeInKB = (str) => {
    const encoder = new TextEncoder('utf-8');
    const bytes = encoder.encode(str);
    const compressedBytes = this.basicGzip(bytes);
    return Math.floor((compressedBytes.length / 1024) * 0.074);
  }

  basicGzip = (inputBytes) => {
    let compressedBytes = [];
    let currentByte = inputBytes[0];
    let count = 1;

    for (let i = 1; i < inputBytes.length; i++) {
      if (inputBytes[i] === currentByte && count < 255) {
        count++;
      } else {
        compressedBytes.push(count, currentByte);
        currentByte = inputBytes[i];
        count = 1;
      }
    }
    compressedBytes.push(count, currentByte);
    return new Uint8Array(compressedBytes);
  }

  canonicalize = (url) => {
    if (!url.endsWith("/main") && !url.endsWith("/bootstrap")) {
      url = this.replaceLast(url, '/', '/_');
    }
    const base = document.querySelector(this.selector).getAttribute("baseurl") ?? window.location.toString();
    return new URL(url + '.scss', base);
  }

  

  load = async (canonicalUrl) => {
    const options = (((new URL(document.location)).searchParams).get("sass_nocache")) ? { cache: "no-cache" } : {}
    let response = await fetch(canonicalUrl, options);

    if (!response.ok && document.querySelector(this.selector).hasAttribute("fallback_baseurl")) {
      const canonicalUrlFallback = canonicalUrl.href.replace(
        document.querySelector(this.selector).getAttribute("baseurl"),
        document.querySelector(this.selector).getAttribute("fallback_baseurl")
      );
      response = await fetch(canonicalUrlFallback, options);
    }

    if (!response.ok) {
      throw new Error(`Failed to fetch ${canonicalUrl}: ${response.status} (${response.statusText})`);
    }

    const contents = await response.text()
    return {
      contents,
      syntax: canonicalUrl.pathname.endsWith('.sass') ? 'indented' : 'scss'
    }
  }
}

// legacy support create instance
const picosassInstance = new Picosass();
window.picosassInstance = picosassInstance;

// // Usage
// window.PicoSass = PicoSass;

window.addEventListener("DOMContentLoaded", (event) => {

  //run  the compiler, unless a special class is added to the body
  if (!document.querySelector(theScssSelector).classList.contains("prevent-autocompile")) {
    const pico = new PicoSass()
    pico.compile();
  }

});