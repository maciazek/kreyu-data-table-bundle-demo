import { Controller } from '@hotwired/stimulus';
import { Tooltip } from 'bootstrap';

export default class extends Controller {
    static targets = ['tooltip'];

    /**
     * Handles theme switch
     */
    initialize() {
        // get user's theme
        let theme = localStorage.getItem('theme');

        // if theme is undefined, set to 'auto'
        if (!theme) {
            theme = 'auto';
            localStorage.setItem('theme', theme);
        }

        // mark chosen theme's button as active
        this.#updateThemeSwitcher(theme);

        // on OS theme change
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (event) => {
            // get user's theme
            let theme = localStorage.getItem('theme');

            // if theme is undefined, set to 'auto'
            if (!theme) {
                theme = 'auto';
                localStorage.setItem('theme', theme);
            }

            // if theme is set to 'auto', get OS theme
            if (theme == 'auto') {
                theme = event.matches ? "dark" : "light";
            }

            // apply theme
            document.body.setAttribute('data-bs-theme', theme);
        });
    }

    /**
     * Initialize tooltip on target element
     *
     * @param {*} element - Target element
     */
    tooltipTargetConnected(element) {
        new Tooltip(element);
    }

    /**
     * Sets theme by passed name
     *
     * @param {*} event - needed to get param
     */
    switchTheme(event) {
        // get chosen theme
        let theme = event.params.name;

        // keep chosen theme in local storage
        localStorage.setItem('theme', theme);

        // mark chosen theme's button as active
        this.#updateThemeSwitcher(theme);

        // if theme is set to 'auto', get OS theme
        if (theme == 'auto' && window.matchMedia) {
            theme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        }

        // apply theme
        document.body.setAttribute('data-bs-theme', theme);
    }

    /**
     * Marks chosen theme's button as active
     *
     * @param {string} theme - chosen theme
     */
    #updateThemeSwitcher(theme) {
        // clear activity in whole switcher
        document.querySelectorAll("[data-bootstrap-name-param]").forEach((button) => {
            button.classList.remove('active');
            button.setAttribute('aria-pressed', 'false');
        });

        // mark chosen theme's button as active
        const button = document.querySelector("[data-bootstrap-name-param='" + theme + "']");
        button?.classList.add('active');
        button?.setAttribute('aria-pressed', 'true');
    }
}
