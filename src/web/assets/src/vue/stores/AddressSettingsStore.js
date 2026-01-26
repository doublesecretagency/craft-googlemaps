import { reactive, computed } from 'vue';
import { defineStore } from 'pinia';

// Define the Pinia store that backs the Address field settings UI
export const useAddressSettingsStore = defineStore('AddressSettings', () => {

    // Settings object for the live preview
    const settings = reactive({
        previewConfig: null,
    });

    // Form model representing the Address field settings
    const form = reactive({
        subfields: [],
        showMapOnLoad: 'coords',     // 'open' | 'closed' | 'coords'
        onSearch: 'open',            // 'open' | 'close' | 'noChange'
        visibilityToggle: 'both',    // 'text' | 'icon' | 'both' | 'hidden'
        coordinatesMode: 'editable', // 'editable' | 'readonly' | 'hidden'
        requireCoordinates: false,
    });

    // Computed preview configuration derived from the form model
    const previewConfig = computed(() => {
        return {
            subfieldConfig: form.subfields.map(
                ({ key, label, width, show, auto, req }) => ({
                    handle: key,
                    label,
                    width,
                    enabled: !!show,
                    autocomplete: !!auto,
                    required: !!req,
                })
            ),
            visibilityToggle: form.visibilityToggle,
            coordinatesMode: form.coordinatesMode,
            mapOnSearch: form.onSearch,
            showMap: form.showMapOnLoad !== 'closed',
            requireCoordinates: !!form.requireCoordinates,
        };
    });

    /**
     * Read the current settings DOM and sync it into the reactive form model.
     */
    function setFromDom(root) {

        // Helper to get a dropdown value
        const getDropdownValue = (name) => {
            // Get the specified element
            const el = root.querySelector(`[name$="[${name}]"]`);
            // If no element, bail
            if (!el) {
                return undefined;
            }
            // Return the value
            return el.value;
        };

        // Helper to get a lightswitch value
        const getLightswitchValue = (settingName) => {
            // Get the specified hidden input
            const hidden = root.querySelector(`input[type="hidden"][name$="[${settingName}]"]`);
            // If no hidden input, bail
            if (!hidden) {
                return null;
            }
            // Get the associated lightswitch button
            const btn = hidden
                .closest('.lightswitch-outer-container')
                ?.querySelector('.lightswitch');
            // If no button, bail
            if (!btn) {
                return null;
            }
            // Return whether the lightswitch is on
            return btn.getAttribute('aria-checked') === 'true';
        };

        // Helper to read a text field value
        const readTextField = (row, index, suffix) => {
            // Get the specified element
            const el = row.querySelector(
                `textarea[name$="[${index}][${suffix}]"],` +
                `input[type="text"][name$="[${index}][${suffix}]"]`
            );
            // If no element, bail
            if (!el) {
                return '';
            }
            // Return the value
            return el.value ?? '';
        };

        // Helper to read a number field value
        const readNumberField = (row, index, suffix) => {
            // Get the specified element
            const el = row.querySelector(
                `input[type="number"][name$="[${index}][${suffix}]"]`
            );
            // If no element, bail
            if (!el) {
                return 0;
            }
            // Get the value
            const value = el.value;
            // If value is empty, return 0
            if (value === undefined || value === null || value === '') {
                return 0;
            }
            // Return the numeric value
            return Number(value);
        };

        // Helper to read a boolean field value
        const readBooleanField = (row, index, suffix) => {
            // Get the specified element
            let el = row.querySelector(
                `input[type="checkbox"][name*="[subfieldConfig][${index}][${suffix}]"]`
            );
            // If element found, return its checked state
            if (el) {
                return !!el.checked;
            }
            // Fallback: check for a hidden input
            el = row.querySelector(
                `input[type="hidden"][name*="[subfieldConfig][${index}][${suffix}]"]`
            );
            // If there is a hidden input, interpret its value
            if (el) {
                const val = (el.value || '').toString().toLowerCase();
                return val === '1' || val === 'true' || val === 'on';
            }
            // If the suffix is 'enabled', infer from row class
            if (suffix === 'enabled') {
                const isDisabled = row.classList.contains('disabled');
                return !isDisabled;
            }
            // Default to false
            return false;
        };

        // Get all subfield rows
        const rows = root.querySelectorAll('[data-gm-subfield-row]');

        // Initialize result array
        const result = [];

        // Loop through each subfield row
        rows.forEach((row) => {

            // Get the subfield key
            const key = row.getAttribute('data-key');

            // If no key, bail
            if (!key) {
                return;
            }

            // Get the subfield index
            const index = row.getAttribute('data-index');

            // If no index, bail
            if (!index) {
                return;
            }

            // Read all relevant fields from the row
            const label = readTextField(row, index, 'label');
            const width = readNumberField(row, index, 'width') || 100;
            const show = readBooleanField(row, index, 'enabled');
            const auto = readBooleanField(row, index, 'autocomplete');
            const req  = readBooleanField(row, index, 'required');

            // Append the normalized subfield configuration to the result array
            result.push({
                key,
                label,
                width,
                show,
                auto,
                req,
            });
        });

        // Persist the normalized subfield configurations into the reactive form model
        form.subfields = result;

        // Get dropdown value for whether to show the map on start
        const rawStart = getDropdownValue('mapOnStart');

        // If a value was found
        if (rawStart) {
            // Push the raw value into the form model
            form.mapOnStart = rawStart;
            // Push whether to show the map on load into the form model
            form.showMapOnLoad =
                rawStart === 'default'
                    ? 'coords'
                    : rawStart === 'open'
                        ? 'open'
                        : 'closed'; // 'close'
        }

        // Get dropdown value for whether to show the map on search
        const mapOnSearch = getDropdownValue('mapOnSearch');

        // If a value was found
        if (mapOnSearch) {
            // Push whether to show the map on search into the form model
            form.mapOnSearch = mapOnSearch;
            // Push whether to show the map on search into the form model
            form.onSearch = mapOnSearch; // keep existing alias
        }

        // Get dropdown value for the visibility toggle setting
        const visibilityToggle = getDropdownValue('visibilityToggle');

        // If a value was found
        if (visibilityToggle) {
            // Push the value into the form model
            form.visibilityToggle = visibilityToggle;
        }

        // Get dropdown value for the coordinates mode setting
        const coordinatesMode = getDropdownValue('coordinatesMode');

        // If a value was found
        if (coordinatesMode) {
            // Push the value into the form model
            form.coordinatesMode = coordinatesMode;
        }

        // Push lightswitch value for the require coordinates setting
        form.requireCoordinates = getLightswitchValue('requireCoordinates');

        // If settings object is defined
        if (typeof settings !== 'undefined') {
            // Push the updated preview configuration into the settings object
            settings.previewConfig = {
                subfieldConfig: result.map((sf) => ({
                    key: sf.key,
                    handle: sf.key,
                    label: sf.label,
                    width: sf.width,
                    enabled: sf.show,
                    required: sf.req,
                    autocomplete: sf.auto,
                })),
                mapOnStart:         form.mapOnStart ?? null,
                mapOnSearch:        form.mapOnSearch ?? null,
                visibilityToggle:   form.visibilityToggle ?? null,
                coordinatesMode:    form.coordinatesMode ?? null,
                requireCoordinates: !!form.requireCoordinates,
            };
        }

    }

    /**
     * Apply enabled/disabled styling to a single subfield row.
     */
    function applyRowUi(row) {

        // Get the subfield row index
        const index = row.getAttribute('data-index');

        // If no index, bail
        if (!index) {
            return;
        }

        // Get the enabled checkbox
        const enabledEl = row.querySelector(
            `input[type="checkbox"][name*="[subfieldConfig][${index}][enabled]"]`
        );

        // Determine whether the subfield is enabled
        const isEnabled = enabledEl ? !!enabledEl.checked : true;

        // Toggle disabled class on the row
        row.classList.toggle('disabled', !isEnabled);
    }

    /**
     * Bind listeners so any UI change re-syncs state and updates the preview.
     */
    function bindDomListeners(root) {

        // Apply initial UI state to all subfield rows
        root.querySelectorAll('[data-gm-subfield-row]').forEach(applyRowUi);

        // Loop through all subfield inputs/selects/textareas
        root.querySelectorAll(
            '[data-gm-subfield-row] input, ' +
            '[data-gm-subfield-row] select, ' +
            '[data-gm-subfield-row] textarea'
        ).forEach((el) => {

            // Define the change handler
            const onChange = () => {
                // Get the closest subfield row
                const row = el.closest('[data-gm-subfield-row]');
                // If row found
                if (row) {
                    // Apply UI changes
                    applyRowUi(row);
                }
                // Sync the store from the DOM
                setFromDom(root);
            };

            // Attach the event listeners
            el.addEventListener('input', onChange);
            el.addEventListener('change', onChange);
        });

        // Attach a listener for subfield reordering
        root.addEventListener('gm:subfields:reordered', () => {
            setFromDom(root);
        });

        // Loop through all settings selects and checkboxes
        root
            .querySelectorAll('[data-gm-settings-select], [data-gm-settings-checkbox]')
            .forEach((el) => {
                // Attach a listener so the store stays in sync with user interaction
                el.addEventListener('change', () => setFromDom(root));
            });

        // Define the setting names to bind
        const settingNames = [
            'mapOnStart',
            'mapOnSearch',
            'visibilityToggle',
            'coordinatesMode',
        ];

        // Loop through each setting name
        settingNames.forEach((name) => {
            // Loop through each matching element
            root
                .querySelectorAll(`[name$="[${name}]"]`)
                .forEach((el) => {
                    // If already bound, skip
                    if (el.__gmSettingsBound) {
                        return;
                    }
                    // Attach a listener so the store stays in sync with user interaction
                    el.addEventListener('change', () => setFromDom(root));
                    el.__gmSettingsBound = true;
                });
        });

        // Get the require coordinates hidden input
        const hidden = root.querySelector('input[type="hidden"][name$="[requireCoordinates]"]');

        // Get the associated lightswitch button
        const btn = hidden?.closest('.lightswitch-outer-container')?.querySelector('.lightswitch');

        // If button found and not already bound
        if (btn && !btn.__gmSettingsBound) {

            // Attach a listener so the store stays in sync with user interaction
            btn.addEventListener('click', () => {
                // // Get whether the lightswitch is on
                // const isOn = btn.getAttribute('aria-checked') === 'true';
                setFromDom(root);
            });

            // Mark this button as bound so we don’t double-bind
            btn.__gmSettingsBound = true;
        }

        // Get the subfields table
        const table = root.querySelector('table.gm-subfields');

        // If table found and Garnish DragSort is available
        if (table && window.Garnish && window.Garnish.DragSort && window.$) {

            // If not already initialized
            if (!table.__gmSubfieldDragSort) {

                // Wrap the table in a jQuery object
                const $table = window.$(table);

                // Reindex the subfield rows after a reorder
                const reindexRows = () => {

                    // Get all subfield rows
                    const rows = table.querySelectorAll('tbody [data-gm-subfield-row]');

                    // Loop through each row and update its data-index and input names
                    rows.forEach((row, newIndex) => {

                        // Get the new and old indices
                        const newIdx = String(newIndex);
                        const oldIdx = row.getAttribute('data-index');

                        // If indices are the same, skip
                        if (oldIdx === newIdx) {
                            return;
                        }

                        // Update the row's data-index attribute
                        row.setAttribute('data-index', newIdx);

                        // Get all named inputs/textareas/selects within the row
                        row.querySelectorAll('input[name], textarea[name], select[name]')

                            // Loop through each input and update its name attribute
                            .forEach((inp) => {

                                // Get the old name
                                const oldName = inp.getAttribute('name');

                                // If no old name, skip
                                if (!oldName) {
                                    return;
                                }

                                // Generate the updated name by replacing the old index with the new index
                                const updated = oldName.replace(
                                    /\[subfieldConfig]\[\d+]/,
                                    `[subfieldConfig][${newIdx}]`
                                );

                                // If the name has changed, update the input's name attribute
                                if (updated !== oldName) {
                                    inp.setAttribute('name', updated);
                                }
                            });

                    });

                    // Dispatch a custom event indicating that subfields have been reordered
                    root.dispatchEvent(
                        new CustomEvent('gm:subfields:reordered', { bubbles: true })
                    );
                };

                // Enable Garnish DragSort on the subfields table
                table.__gmSubfieldDragSort = new window.Garnish.DragSort(
                    $table.find('tbody tr'),
                    {
                        handle: '.move',
                        axis: 'y',
                        collapseDraggees: true,
                        onSortChange: reindexRows,
                    }
                );
            }
        }
    }

    // Expose the store's state and actions
    return {
        form,
        previewConfig,
        setFromDom,
        bindDomListeners
    };
});
