/** Entry Point: Decide whether to load the custom cookie banner or not **/
document.addEventListener('DOMContentLoaded', function() {
    const preferences = new GetGeoLocation().getCookiePreferences();

    // If preferences are found, return
    if (preferences) {
        return;
    }

    // Check if a cookie consent banner or consent manager is already present
    if (!isCookieConsentPresent()) {

        // Prompt the cookie banner
        promptCookieBanner();
        // cookieSettingsPage();

        // Add event listeners to the buttons
        btnEventListeners();
    }

    return;
});

/**
 * Detects if a cookie consent banner or consent manager is already present
 * Checks for:
 * - Known CMP global objects
 * - Known CMP DOM elements
 * - IAB TCF API
 * - Heuristic detection of elements with "cookie" in id/class
 */
const isCookieConsentPresent = () => {
    // Check if any of the known cookie consent globals are present
    const knownGlobals = [
        'Cookiebot',
        'Optanon',
        'truste',
        '_iub',
        '__cmp',
        'didomi',
        'klaro',
        '__tcfapi'
    ];

    for (const global of knownGlobals) {
        if (typeof window[global] !== 'undefined') {
            return true;
        }
    }

    // If no known cookie consent globals are present, observe for late loaded banners
    observeForLateLoadedBanners();

    return false;
}

/**
 * Observe for late loaded banners
 */
const observeForLateLoadedBanners = () => {

    // Check if the cookie consent banner is present
    const observer = new MutationObserver((mutations) => {
        for (const mutation of mutations) {
            for (const node of mutation.addedNodes) {
                if (node.nodeType === 1) { // ELEMENT_NODE

                    // Check if the node matches the cookie consent banner selector
                    const el = node;

                    // If the node matches the cookies consent banner selector, prompt the cookie banner
                    if (el.matches && el.matches('[id*="cookie"], [class*="cookie"]')) {

                        observer.disconnect();

                        // Return true to indicate that the cookie consent banner was detected
                        return true;
                    }
                }
            }
        }
    });

    // Observe the body for late loaded cookie consent banners
    observer.observe(document.body, { childList: true, subtree: true });
}

/**
 * Prompt the cookie banner
 */
const promptCookieBanner = () => {
    const cookieBanner = document.createElement('div');

    cookieBanner.classList.add('cookie-banner');
    cookieBanner.innerHTML = `
    
        <div class="cookie-banner-content-container">
            <h1 id="cookie-banner-title"">We use cookies to optimize your experience</h1>
            
            <p id="cookie-banner-description">You can choose which cookies to allow. By clicking "Accept all", you consent to all cookies. By clicking "Manage preferences", you can adjust your choices.</p>
        
            <button id="manage-preferences" class="cookie-banner-btn">Manage preferences</button>
            <button id="accept-cookies" class="cookie-banner-btn">Accept all</button>
        </div>


    `;

    document.body.appendChild(cookieBanner);
}

/**
 * Handle the "all" checkbox change event
 * @param {Event} e - The change event
 * @param {NodeList} otherCheckboxes - The other checkboxes to update
 */
const handleAllCheckboxChange = (e, otherCheckboxes) => {
    const isChecked = e.target.checked;
    otherCheckboxes.forEach(checkbox => {
        checkbox.checked = isChecked;
    });
};

/**
 * Handle individual checkbox change event
 * @param {NodeList} otherCheckboxes - All checkboxes except "all"
 * @param {HTMLInputElement} allCheckbox - The "all" checkbox
 */
const handleIndividualCheckboxChange = (otherCheckboxes, allCheckbox) => {
    const allChecked = Array.from(otherCheckboxes).every(cb => cb.checked);
    allCheckbox.checked = allChecked;
};

/**
 * Initialize checkbox behavior
 * @param {HTMLElement} container - The container element containing the checkboxes
 */
const initializeCheckboxBehavior = (container) => {
    const allCheckbox = container.querySelector('[data-is-all="true"]');
    const otherCheckboxes = container.querySelectorAll('.preference-checkbox:not([data-is-all="true"])');

    // Handle "all" checkbox change
    allCheckbox.addEventListener('change', (e) => {
        handleAllCheckboxChange(e, otherCheckboxes);
    });

    // Handle other checkboxes change
    otherCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', () => {
            handleIndividualCheckboxChange(otherCheckboxes, allCheckbox);
        });
    });
};

/**
 * Cookie Settings Page
 */
const cookieSettingsPage = () => {
    let formFields = '';

    // Hide the cookie banner if it exists
    const existingBanner = document.querySelector('.cookie-banner');
    if (existingBanner) {
        existingBanner.style.display = 'none';
    }

    // Create and append backdrop
    const backdrop = document.createElement('div');
    backdrop.classList.add('cookie-settings-backdrop');
    document.body.appendChild(backdrop);

    const cookieSettingsPage = document.createElement('div');
    cookieSettingsPage.classList.add('cookie-settings-page');
    
    // Get the cookie preferences
    const cookiePreferences = new GetGeoLocation().getCookiePreferences();

    // Generate form fields from managePreference array
    formFields = managePreference.map(pref => {
        // Check if this preference exists in cookie data
        const isChecked = cookiePreferences ? cookiePreferences[pref.name] === 'on' : pref.checked;
        
        return `
            <li>
                <input type="checkbox" 
                    id="${pref.name}" 
                    name="${pref.name}" 
                    ${isChecked ? 'checked' : ''} 
                    ${pref.disabled ? 'disabled' : ''}
                    class="preference-checkbox"
                    data-is-all="${pref.name === 'all'}"
                >
                <label for="${pref.name}">${pref.label}</label>
            </li>
        `;
    }).join('');

    // Template for the cookie settings page
    cookieSettingsPage.innerHTML = `
        <h1>Cookie Settings</h1>

        <form id="cookie-settings-form">
            <ul class="cookie-settings-section">
                <li class="cookie-settings-section-title" id="analytics-section">
                    <span>Analytics</span>
                    <button type="button" class="accordion-toggle">+</button>
                </li>
                <div class="cookie-settings-content">
                    ${formFields}
                </div>
            </ul>

            <!-- Buttons -->
            <div style="margin-top: 20px;">
                <button type="submit" id="save-cookie-settings" class="settings-btn">Save Settings</button>
                <button type="button" id="back-to-banner" class="settings-btn">Back to Cookie Banner</button>
            </div>
        </form>
    `;

    // Append the cookie settings page to the body
    document.body.appendChild(cookieSettingsPage);

    // Initialize checkbox behavior
    initializeCheckboxBehavior(cookieSettingsPage);

    // Initialize form submission handling
    saveCookieSettings();

    // Initialize accordion functionality
    accordionToggle();

    // Add event listener for the back button
    document.getElementById('back-to-banner').addEventListener('click', () => {
        // Remove the settings page and backdrop
        cookieSettingsPage.remove();
        backdrop.remove();

        // Show the banner again
        if (existingBanner) {
            existingBanner.style.display = 'block';
        }
    });
}

/**
 * Button Event Listeners
 */
const btnEventListeners = () => {
    // Get the buttons, excluding the accordion toggle button
    const btns = document.querySelectorAll('.cookie-banner-btn:not(.accordion-toggle)');

    // Add event listeners to the buttons
    btns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();

            // Get the button's id
            const btnId = btn.id;

            // If the button id is "manage-preferences", show the cookie settings page
            if (btnId === 'manage-preferences') {
                cookieSettingsPage();
            }

            // If the button id is "accept-cookies", accept all cookies
            if (btnId === 'accept-cookies') {
                acceptAllCookies();
            }

            // If the button id is "back-to-banner", show the cookie banner
            if (btnId === 'back-to-banner') {
                promptCookieBanner();
            }
            
            // If the button id is "save-cookie-settings", save the cookie settings
            if (btnId === 'save-cookie-settings') {
                saveCookieSettings();
            }
        })
    });
}

/**
 * Initialize analytics based on cookie preferences
 * @param {Object} preferences - The cookie preferences object
 */
const initializeAnalytics = (preferences) => {
    // Only initialize if preferences are valid
    if (!preferences || typeof preferences !== 'object') {
        return;
    }

    // Initialize geolocation tracking if enabled
    if (preferences.ip_address === 'on') {
        new GetGeoLocation();
    }
}

/**
 * Accept all cookies
 */
const acceptAllCookies = () => {
    const banner = document.querySelector('.cookie-banner');
    const settings = document.querySelector('.cookie-settings-page');
    const backdrop = document.querySelector('.cookie-settings-backdrop');

    // Create a preferences object with all cookies enabled
    const allPreferences = {};
    managePreference.forEach(pref => {
        if (!pref.disabled) {
            allPreferences[pref.name] = 'on';
        }
    });

    // Set the cookie preferences
    setCookiePreferences(allPreferences);

    // Initialize analytics and tracking based on preferences
    initializeAnalytics(allPreferences);
    
    // Remove banner, settings page, and backdrop if they exist
    if (banner) banner.remove();
    if (settings) settings.remove();
    if (backdrop) backdrop.remove();
}

/**
 * Save Cookie Settings
 */
const saveCookieSettings = () => {
    // Get the form
    const form = document.getElementById('cookie-settings-form');
    const banner = document.querySelector('.cookie-banner');
    const settings = document.querySelector('.cookie-settings-page');
    const backdrop = document.querySelector('.cookie-settings-backdrop');
    
    // Add submit event listener to the form
    form.addEventListener('submit', (e) => {
        e.preventDefault();
        
        // Get the form data
        const formData = new FormData(form);
        
        // Convert FormData to a regular object
        const data = {}

        formData.forEach((value, key) => {
            data[key] = value;
        });
        
        // Set the cookie preferences
        setCookiePreferences(data);

        // Initialize analytics and tracking based on preferences
        initializeAnalytics(data);

        // Remove banner, settings page, and backdrop if they exist
        if (banner) banner.remove();
        if (settings) settings.remove();
        if (backdrop) backdrop.remove();
    });
}

/**
 * Set the cookie preferences
 * @param {Object} data - The data to set the cookie preferences
 */
const setCookiePreferences = (data) => {
    // Check if the data is object
    if (typeof data !== 'object') {
        return;
    }
    
    // Set expiry date to 1 year from now
    const expiryDate = new Date();
    expiryDate.setFullYear(expiryDate.getFullYear() + 1);

    // Set the cookie preferences
    document.cookie = `wpva-visitor-analytics-cookie-preferences=${encodeURIComponent(JSON.stringify(data))}; path=/; expires=${expiryDate.toUTCString()}; SameSite=Strict`;
}

/**
 * Accordion Toggle
 */
const accordionToggle = () => {
    const accordionTitles = document.querySelectorAll('.cookie-settings-section-title');
    
    accordionTitles.forEach(title => {
        title.addEventListener('click', () => {
            const content = title.nextElementSibling;
            const toggleButton = title.querySelector('.accordion-toggle');
            
            // Toggle the content visibility
            if (content.style.display === "block") {
                content.style.display = "none";
                toggleButton.textContent = '+';
            } else {
                content.style.display = "block";
                toggleButton.textContent = '-';
            }
        });
    });
} 