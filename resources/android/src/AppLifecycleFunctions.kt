package com.djurovicigoor.plugins.app_lifecycle

import android.util.Log
import androidx.fragment.app.FragmentActivity
import com.nativephp.mobile.bridge.BridgeFunction
import com.nativephp.mobile.bridge.BridgeResponse
import com.nativephp.mobile.lifecycle.NativePHPLifecycle
import com.nativephp.mobile.utils.NativeActionCoordinator
import org.json.JSONObject

/**
 * AppLifecycleFunctions
 *
 * Subscribes to NativePHPLifecycle events to detect true foreground/background
 * transitions and dispatches PHP events accordingly.
 *
 * Because NativePHP's single activity declares:
 *   android:configChanges="uiMode|colorMode|orientation|screenSize"
 * …onPause / onResume are NOT fired during screen rotation, so no extra
 * guard for rotation is needed.
 *
 * Events dispatched to PHP:
 *   - Djurovicigoor\AppLifecycle\Events\AppBooted        (once, on initial app launch)
 *   - Djurovicigoor\AppLifecycle\Events\AppForegrounded  (onResume after a pause)
 *   - Djurovicigoor\AppLifecycle\Events\AppBackgrounded  (onPause)
 */
object AppLifecycleFunctions {

    /**
     * Initialize — registered as a bridge_function so it is instantiated
     * (and its init{} block runs) during app startup when
     * registerPluginBridgeFunctions() is called by the NativePHP core.
     */
    class Initialize(private val activity: FragmentActivity) : BridgeFunction {

        // True once the activity has paused at least once, so that the first
        // onResume at app launch does not fire a spurious AppForegrounded event.
        private var wasBackgrounded = false

        init {
            // ── App booted (initial launch) ──────────────────────────────────
            val bootPayload = JSONObject().apply {
                put("timestamp", System.currentTimeMillis())
            }.toString()

            Log.d("AppLifecycle", "App booted")

            NativeActionCoordinator.dispatchEvent(
                activity,
                "Djurovicigoor\\AppLifecycle\\Events\\AppBooted",
                bootPayload
            )

            // ── Going to background ──────────────────────────────────────────
            NativePHPLifecycle.on(NativePHPLifecycle.Events.ON_PAUSE) { _ ->
                wasBackgrounded = true

                val payload = JSONObject().apply {
                    put("timestamp", System.currentTimeMillis())
                }.toString()

                Log.d("AppLifecycle", "App backgrounded")

                NativeActionCoordinator.dispatchEvent(
                    activity,
                    "Djurovicigoor\\AppLifecycle\\Events\\AppBackgrounded",
                    payload
                )
            }

            // ── Returning to foreground ──────────────────────────────────────
            NativePHPLifecycle.on(NativePHPLifecycle.Events.ON_RESUME) { _ ->
                if (!wasBackgrounded) return@on   // skip the very first onResume at launch
                wasBackgrounded = false

                val payload = JSONObject().apply {
                    put("timestamp", System.currentTimeMillis())
                }.toString()

                Log.d("AppLifecycle", "App foregrounded")

                NativeActionCoordinator.dispatchEvent(
                    activity,
                    "Djurovicigoor\\AppLifecycle\\Events\\AppForegrounded",
                    payload
                )
            }

            Log.d("AppLifecycle.Initialize", "AppLifecycle monitoring registered")
        }

        override fun execute(parameters: Map<String, Any>): Map<String, Any> {
            return BridgeResponse.success(mapOf("status" to "AppLifecycle listener registered"))
        }
    }
}