// lazyload.config.js
const lazyLoadOptions = {
    elements_selector: ".lazy",    // CSS selector for lazy-loaded elements
    threshold: 300,                // Threshold in pixels for loading
    data_src: "src",               // Attribute for the actual image source
    data_srcset: "srcset",         // Attribute for responsive image set source
    callback_load: (element) => {  // Optional callback when an element is loaded
        console.log("Image loaded", element);
        ScrollReveal().reveal(element, {
            opacity: 0,
            distance: '50px',
            duration: 800,
            easing: 'ease-out',
            origin: 'bottom'
        });
    },
    callback_error: (element) => { // Optional callback for error handling
        console.error("Image failed to load", element);
    },
    use_native: true               // Use browser native lazy loading if available
};