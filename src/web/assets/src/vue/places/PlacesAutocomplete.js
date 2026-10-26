/**
 * Places Autocomplete, powered by Places API (New).
 *
 * Attaches a suggestion menu to an existing input. Google's legacy Places API
 * is unavailable to Cloud projects created after March 1st, 2025, but the new
 * API must be enabled on older projects. If the new API is not enabled, every
 * input on the page falls back to the legacy Places Autocomplete.
 */

// Page-wide provider: null (not yet known), 'new', or 'legacy'
let _provider = null;

// Every attached menu on the page
const _instances = new Set();

// Whether the legacy fallback has already been reported
let _reported = false;

// Whether the menu styles have been injected
let _styled = false;

// Unique ID counter for menus
let _uid = 0;

// Place fields needed to fill the Address subfields
const PLACE_FIELDS = ['addressComponents', 'formattedAddress', 'location', 'displayName', 'id'];

// Delay between keystrokes and suggestion requests (in milliseconds)
const DEBOUNCE = 150;

// ========================================================================= //

/**
 * Get the page-wide provider.
 */
export function getProvider() {
    return _provider;
}

/**
 * Whether a request failed because Places API (New) is unavailable to the API key.
 */
export function isPermissionError(error) {

    // gRPC code 7 is PERMISSION_DENIED (API disabled or restricted)
    if ('RpcError' === error?.name && 7 === error?.code) {
        return true;
    }

    // Otherwise, check the message
    return /has not been used|is disabled|not authorized/i.test(String(error?.message || ''));
}

/**
 * Convert a Places API (New) place into the legacy `PlaceResult` shape.
 *
 * Everything downstream (subfields, `raw`, coordinates) reads the legacy shape,
 * so a place looks the same whichever API produced it.
 */
export function toLegacyPlace(place) {

    // If no place, bail
    if (!place) {
        return null;
    }

    // Return the legacy shape
    return {
        address_components: (place.addressComponents || []).map(c => ({
            long_name: c.longText,
            short_name: c.shortText,
            types: c.types || [],
        })),
        formatted_address: place.formattedAddress ?? null,
        geometry: {location: place.location ?? null},
        html_attributions: [],
        name: place.displayName ?? null,
        place_id: place.id ?? null,
    };
}

/**
 * Whether any suggestion menu on the page is open.
 */
export function isAnyMenuOpen() {
    for (const instance of _instances) {
        if (instance.isOpen()) {
            return true;
        }
    }
    return false;
}

/**
 * Attach a suggestion menu to an input.
 *
 * Returns null if the page is already using the legacy API, so the
 * caller can wire up the legacy Autocomplete instead.
 */
export function attachAutocomplete(input, {onPlace, onFallback}) {

    // Get the Places library
    const places = window.google?.maps?.places;

    // If already using legacy, or the new API is missing from Maps JS, bail
    if ('legacy' === _provider || !places?.AutocompleteSuggestion) {
        return null;
    }

    // Inject the menu styles
    _injectStyles();

    // Create the menu
    const id = `gm-places-menu-${++_uid}`;
    const menu = document.createElement('div');
    menu.className = 'gm-places-menu';
    menu.style.display = 'none';

    // Create the list of suggestions
    const list = document.createElement('ul');
    list.id = id;
    list.setAttribute('role', 'listbox');
    list.setAttribute('aria-label', 'Address suggestions');
    menu.appendChild(list);

    // Add the Google Maps attribution
    const attribution = document.createElement('div');
    attribution.className = 'gm-places-attribution';
    attribution.setAttribute('translate', 'no');
    attribution.textContent = 'Google Maps';
    menu.appendChild(attribution);

    // Add the menu to the page
    document.body.appendChild(menu);

    // Make the input a combobox
    input.setAttribute('role', 'combobox');
    input.setAttribute('aria-autocomplete', 'list');
    input.setAttribute('aria-expanded', 'false');
    input.setAttribute('aria-controls', id);
    input.setAttribute('autocomplete', 'off');

    // Current suggestions and highlighted index
    let suggestions = [];
    let active = -1;

    // Session token, debounce timer, and latest request number
    let token = null;
    let timer = null;
    let request = 0;

    // Whether the menu is open
    const isOpen = () => ('none' !== menu.style.display);

    // Position the menu under the input
    const position = () => {
        const rect = input.getBoundingClientRect();
        menu.style.top = `${rect.bottom + window.scrollY}px`;
        menu.style.left = `${rect.left + window.scrollX}px`;
        menu.style.width = `${rect.width}px`;
    };

    // Close the menu
    const close = () => {
        menu.style.display = 'none';
        input.setAttribute('aria-expanded', 'false');
        input.removeAttribute('aria-activedescendant');
        active = -1;
    };

    // Highlight a suggestion
    const highlight = (index) => {
        active = index;
        list.querySelectorAll('[role="option"]').forEach((li, i) => {
            li.setAttribute('aria-selected', (i === index) ? 'true' : 'false');
        });
        if (index < 0) {
            input.removeAttribute('aria-activedescendant');
        } else {
            input.setAttribute('aria-activedescendant', `${id}-${index}`);
        }
    };

    // Render the suggestions
    const render = () => {

        // Clear the list
        list.textContent = '';

        // If no suggestions, close the menu
        if (!suggestions.length) {
            close();
            return;
        }

        // Add each suggestion
        suggestions.forEach((s, i) => {
            const prediction = s.placePrediction;
            const li = document.createElement('li');
            li.id = `${id}-${i}`;
            li.setAttribute('role', 'option');
            li.setAttribute('aria-selected', 'false');
            const main = document.createElement('span');
            main.className = 'gm-places-main';
            main.textContent = prediction.mainText?.text ?? prediction.text?.text ?? '';
            const secondary = document.createElement('span');
            secondary.className = 'gm-places-secondary';
            secondary.textContent = prediction.secondaryText?.text ?? '';
            li.append(main, ' ', secondary);
            // Keep focus in the input when choosing with the mouse
            li.addEventListener('mousedown', (e) => {
                e.preventDefault();
                select(i);
            });
            li.addEventListener('mousemove', () => highlight(i));
            list.appendChild(li);
        });

        // Open the menu
        active = -1;
        position();
        menu.style.display = '';
        input.setAttribute('aria-expanded', 'true');
    };

    // Fall back to the legacy API, page-wide
    const fallBack = () => {
        _provider = 'legacy';
        _reportLegacy();
        for (const instance of [..._instances]) {
            instance.fallBack();
        }
    };

    // Fetch suggestions for the current input value
    const fetchSuggestions = async () => {

        // Get the search text
        const text = input.value.trim();

        // If nothing to search, close the menu
        if (!text) {
            suggestions = [];
            render();
            return;
        }

        // Start a new session if needed
        token = token || new places.AutocompleteSessionToken();

        // Number this request, so stale responses are ignored
        const current = ++request;

        try {
            // Request suggestions
            const response = await places.AutocompleteSuggestion.fetchAutocompleteSuggestions({
                input: text,
                sessionToken: token,
            });
            // If a newer request was made, ignore this one
            if (current !== request || !_instances.has(instance)) {
                return;
            }
            // The new API works
            _provider = 'new';
            // Keep only place predictions
            suggestions = (response.suggestions || []).filter(s => s.placePrediction);
            render();
        } catch (error) {
            // If the new API is unavailable, fall back to legacy
            if (isPermissionError(error)) {
                fallBack();
                return;
            }
            // Otherwise, log it and close the menu
            console.warn('[GM] Unable to fetch address suggestions.', error);
            suggestions = [];
            render();
        }
    };

    // Select a suggestion
    const select = async (index) => {

        // Get the suggestion
        const prediction = suggestions[index]?.placePrediction;

        // If no suggestion, bail
        if (!prediction) {
            return;
        }

        // Close the menu
        close();

        try {
            // Get the place details (ends the billing session)
            const place = prediction.toPlace();
            await place.fetchFields({fields: PLACE_FIELDS});
            // Start a new session next time
            token = null;
            // Hand the place over in the legacy shape
            onPlace(toLegacyPlace(place));
        } catch (error) {
            console.warn('[GM] Unable to get the selected address.', error);
        }
    };

    // Debounce typing
    const onInput = () => {
        clearTimeout(timer);
        timer = setTimeout(fetchSuggestions, DEBOUNCE);
    };

    // Keyboard navigation
    const onKeydown = (e) => {

        // If the event is part of an IME composition, ignore it
        if (e.isComposing) {
            return;
        }

        // If the menu is closed, ignore it
        if (!isOpen()) {
            return;
        }

        // Handle navigation keys
        switch (e.key) {
            case 'ArrowDown':
                e.preventDefault();
                highlight((active + 1) % suggestions.length);
                break;
            case 'ArrowUp':
                e.preventDefault();
                highlight((active <= 0 ? suggestions.length : active) - 1);
                break;
            case 'Enter':
                // Never submit the form while the menu is open
                e.preventDefault();
                if (active < 0) {
                    close();
                } else {
                    select(active);
                }
                break;
            case 'Tab':
                if (active >= 0) {
                    select(active);
                } else {
                    close();
                }
                break;
            case 'Escape':
                e.preventDefault();
                close();
                break;
        }
    };

    // Close when focus leaves the input
    const onBlur = () => close();

    // Keep the menu under the input when the page moves
    const onMove = () => {
        if (isOpen()) {
            position();
        }
    };

    // Attach the listeners
    input.addEventListener('input', onInput);
    input.addEventListener('keydown', onKeydown, {capture: true});
    input.addEventListener('blur', onBlur);
    window.addEventListener('resize', onMove);
    window.addEventListener('scroll', onMove, true);

    // Remove everything from the page
    const destroy = () => {
        clearTimeout(timer);
        input.removeEventListener('input', onInput);
        input.removeEventListener('keydown', onKeydown, {capture: true});
        input.removeEventListener('blur', onBlur);
        window.removeEventListener('resize', onMove);
        window.removeEventListener('scroll', onMove, true);
        ['role', 'aria-autocomplete', 'aria-expanded', 'aria-controls', 'aria-activedescendant']
            .forEach(a => input.removeAttribute(a));
        menu.remove();
        _instances.delete(instance);
    };

    // Register this instance
    const instance = {
        isOpen,
        destroy,
        fallBack: () => {
            destroy();
            onFallback();
        },
    };
    _instances.add(instance);

    // Return the controls
    return {isOpen, destroy};
}

// ========================================================================= //

/**
 * Report the legacy fallback once per page.
 */
function _reportLegacy() {

    // If already reported, bail
    if (_reported) {
        return;
    }
    _reported = true;

    // Tell the developer
    console.warn('[GM] Places API (New) is not enabled for the browser key. Address autocompletion is using the legacy Places API, which is deprecated. Please enable "Places API (New)" in your Google Cloud project.');

    // Log a deprecation warning in the control panel
    window.Craft?.sendActionRequest?.('POST', 'google-maps/deprecation/legacy-places')
        .catch(() => {});
}

/**
 * Inject the menu styles once.
 */
function _injectStyles() {

    // If already injected, bail
    if (_styled) {
        return;
    }
    _styled = true;

    // Add the styles
    const style = document.createElement('style');
    style.textContent = `
.gm-places-menu {
    position: absolute;
    z-index: 1000;
    box-sizing: border-box;
    background: #fff;
    border-top: 1px solid #d9d9d9;
    border-radius: 0 0 2px 2px;
    box-shadow: 0 2px 6px rgba(0,0,0,.3);
    font-family: Arial, sans-serif;
    overflow: hidden;
}
.gm-places-menu ul {
    list-style: none;
    margin: 0;
    padding: 0;
}
.gm-places-menu li {
    padding: 4px 8px;
    border-top: 1px solid #e6e6e6;
    font-size: 13px;
    line-height: 26px;
    color: #515151;
    cursor: default;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.gm-places-menu li:first-child {
    border-top: 0;
}
.gm-places-menu li[aria-selected="true"] {
    background: #ebf2fe;
}
.gm-places-main {
    color: #000;
}
.gm-places-secondary {
    color: #5e5e5e;
}
.gm-places-attribution {
    padding: 4px 10px 5px;
    border-top: 1px solid #e6e6e6;
    text-align: right;
    font-family: Roboto, sans-serif;
    font-size: 12px;
    font-weight: 400;
    color: #5e5e5e;
}
`;
    document.head.appendChild(style);
}
