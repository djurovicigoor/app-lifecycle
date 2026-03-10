/**
 * app-lifecycle — JavaScript client
 *
 * Listens for AppBooted / AppForegrounded / AppBackgrounded events dispatched by the
 * native layer so Vue / React / vanilla JS can react without any extra
 * bridge calls.
 *
 * NativePHP dispatches all native events as a single CustomEvent on the
 * document:
 *
 *   document.dispatchEvent(new CustomEvent('native-event', {
 *     detail: { event: 'Djurovicigoor\\AppLifecycle\\Events\\AppForegrounded', payload: {...} }
 *   }))
 *
 * Usage (Vue 3):
 *
 *   import { onAppForegrounded, onAppBackgrounded } from '@djurovicigoor/app-lifecycle';
 *   import { onMounted, onUnmounted } from 'vue';
 *
 *   onMounted(() => {
 *     const stopFg = onAppForegrounded(({ timestamp }) => console.log('active!', timestamp));
 *     const stopBg = onAppBackgrounded(({ timestamp }) => console.log('background', timestamp));
 *     onUnmounted(() => { stopFg(); stopBg(); });
 *   });
 *
 * Usage (vanilla JS):
 *
 *   import { onAppForegrounded } from '@djurovicigoor/app-lifecycle';
 *   onAppForegrounded(({ timestamp }) => syncData());
 */

// ── Event class names ─────────────────────────────────────────────────────────
// These must exactly match the PHP class FQCNs (with single backslashes).

export const Events = {
    AppLifecycle: {
        AppBooted: 'Djurovicigoor\\AppLifecycle\\Events\\AppBooted',
        AppForegrounded: 'Djurovicigoor\\AppLifecycle\\Events\\AppForegrounded',
        AppBackgrounded: 'Djurovicigoor\\AppLifecycle\\Events\\AppBackgrounded',
    },
};

// ── Internal helper ───────────────────────────────────────────────────────────

/**
 * Subscribe to a NativePHP native event by its PHP class name.
 *
 * NativePHP v3 dispatches all native events as a single 'native-event'
 * CustomEvent on the document, with { event, payload } in the detail.
 *
 * Returns an unsubscribe function.
 *
 * @param {string} eventName - PHP FQCN of the event (single backslashes)
 * @param {(payload: Record<string, any>) => void} handler
 * @returns {() => void}
 */
function subscribe(eventName, handler) {
    const listener = (e) => {
        if (e.detail && e.detail.event === eventName) {
            handler(e.detail.payload ?? {});
        }
    };

    document.addEventListener('native-event', listener);
    return () => document.removeEventListener('native-event', listener);
}

// ── Public API ────────────────────────────────────────────────────────────────

/**
 * Register a callback that fires once when the app cold-starts (boots).
 * Does NOT fire on background→foreground transitions — use onAppForegrounded for those.
 *
 * @param {(payload: { timestamp: number }) => void} handler
 * @returns {() => void} unsubscribe function
 */
export function onAppBooted(handler) {
    return subscribe(Events.AppLifecycle.AppBooted, handler);
}

/**
 * Register a callback that fires when the app returns to the foreground.
 * Does NOT fire on initial app launch — only after a real background→foreground transition.
 *
 * @param {(payload: { timestamp: number }) => void} handler
 * @returns {() => void} unsubscribe function
 */
export function onAppForegrounded(handler) {
    return subscribe(Events.AppLifecycle.AppForegrounded, handler);
}

/**
 * Register a callback that fires when the app moves to the background.
 *
 * @param {(payload: { timestamp: number }) => void} handler
 * @returns {() => void} unsubscribe function
 */
export function onAppBackgrounded(handler) {
    return subscribe(Events.AppLifecycle.AppBackgrounded, handler);
}