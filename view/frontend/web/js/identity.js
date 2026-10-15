/**
 * CommerceGears Arasta Live Chat (Luma): loads the Arasta widget and keeps its identity attributes up to date.
 *
 * The loader tag is <script id="platform-widget-loader" data-store-id="…">. The signed token of the logged-in
 * customer (data-customer-token) and the guest's masked cart id (data-cart-id) come from the private
 * `platform-identity` customer-data section, so full-page-cached HTML never contains them. Every change is announced
 * with the `platform:identity` window event. An expired token is replaced with a fresh one.
 *
 * With CommerceGears Cookie Consent on the page (window.SwConsent) and a consent category set, the loader is added only
 * once the visitor grants that category; without it the loader is added at once.
 */
define(['Magento_Customer/js/customer-data'], function (customerData) {
    'use strict';

    /**
     * Moves the Arasta launcher (inside the widget's open shadow root) up on phones by the configured offset.
     * A stylesheet with !important wins over the launcher's inline position.
     */
    function placeLauncher(offset) {
        var css = '@media (max-width: 767px){button[aria-expanded]{bottom:' + (20 + offset) + 'px !important}'
            + 'div[part="panel"]{bottom:' + (92 + offset) + 'px !important;'
            + 'height:min(640px,calc(100vh - ' + (124 + offset) + 'px)) !important}}',
            observer;

        function scan() {
            var host = document.querySelector('[data-platform-widget]'),
                style;

            if (!host || !host.shadowRoot) {
                return false;
            }
            if (!host.shadowRoot.querySelector('style[data-cg-offset]')) {
                style = document.createElement('style');
                style.setAttribute('data-cg-offset', '');
                style.textContent = css;
                host.shadowRoot.appendChild(style);
            }
            return true;
        }

        if (!offset || scan()) {
            return;
        }
        observer = new MutationObserver(function () {
            if (scan()) {
                observer.disconnect();
            }
        });
        observer.observe(document.body, {childList: true});
    }

    var SECTION = 'platform-identity',
        SCRIPT_ID = 'platform-widget-loader',
        MARGIN_MS = 30000;

    return function (config) {
        var identity, script, timer = null, retried = false;

        if (!config.loaderUrl || !config.storeId || document.getElementById(SCRIPT_ID)) {
            return;
        }
        identity = customerData.get(SECTION);
        script = document.createElement('script');
        script.src = config.loaderUrl;
        script.async = true;
        script.id = SCRIPT_ID;
        script.setAttribute('data-store-id', config.storeId);
        if (config.zIndex) {
            script.setAttribute('data-z-index', String(config.zIndex));
        }

        function refresh() {
            customerData.reload([SECTION], false);
        }

        function apply(data) {
            var token = (data && data.token) || null,
                expiresAt = data && data.expiresAt ? data.expiresAt * 1000 : 0;

            if (timer) {
                clearTimeout(timer);
                timer = null;
            }
            if (token && expiresAt && expiresAt - MARGIN_MS <= Date.now()) {
                if (!retried) {
                    // Stale token from the browser cache: ask for a new one (once; the server clock wins after that).
                    retried = true;
                    token = null;
                    refresh();
                }
            } else if (token && expiresAt) {
                retried = false;
                timer = setTimeout(refresh, Math.max(expiresAt - MARGIN_MS - Date.now(), 1000));
            }
            if (token) {
                script.setAttribute('data-customer-token', token);
            } else {
                script.removeAttribute('data-customer-token');
            }
            if (data && data.cartId) {
                script.setAttribute('data-cart-id', data.cartId);
            } else {
                script.removeAttribute('data-cart-id');
            }
            window.dispatchEvent(new CustomEvent('platform:identity', {detail: {token: token}}));
        }

        function load() {
            placeLauncher(Number(config.phoneOffset) || 0);
            document.body.appendChild(script);
        }

        apply(identity());
        identity.subscribe(apply);
        if (config.consentCategory && window.SwConsent && typeof window.SwConsent.whenGranted === 'function') {
            window.SwConsent.whenGranted(config.consentCategory, load);
        } else {
            load();
        }
    };
});
