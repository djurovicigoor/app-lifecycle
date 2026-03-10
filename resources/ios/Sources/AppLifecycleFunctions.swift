import Foundation
import UIKit

// MARK: - AppLifecycle Plugin Init

/// Called once during app startup by NativePHP's generated plugin registration code.
///
/// Registers NotificationCenter observers so that real foreground/background
/// transitions dispatch the corresponding PHP events.
///
/// Notification choice rationale:
///   - didBecomeActiveNotification      fires when the app is fully active; a one-shot
///     guard ensures AppBooted is sent only on the very first activation (cold start).
///   - willEnterForegroundNotification  fires only when returning FROM the background,
///     NOT on initial app launch — avoids a spurious AppForegrounded on first open.
///   - didEnterBackgroundNotification   fires when the app fully enters the background.
@_cdecl("NativePHPAppLifecycleInit")
public func NativePHPAppLifecycleInit() {
    let center = NotificationCenter.default

    // ── App booted (initial launch) ──────────────────────────────────────────
    // didBecomeActive fires after the bridge is ready, unlike calling send? directly
    // here at init time. The hasBooted guard ensures it fires only on cold start.
    var hasBooted = false
    center.addObserver(
        forName: UIApplication.didBecomeActiveNotification,
        object: nil,
        queue: .main
    ) { _ in
        guard !hasBooted else { return }
        hasBooted = true
        LaravelBridge.shared.send?(
            "Djurovicigoor\\AppLifecycle\\Events\\AppBooted",
            ["timestamp": Int(Date().timeIntervalSince1970 * 1000)]
        )
    }

    // ── Returning to foreground ──────────────────────────────────────────────
    center.addObserver(
        forName: UIApplication.willEnterForegroundNotification,
        object: nil,
        queue: .main
    ) { _ in
        LaravelBridge.shared.send?(
            "Djurovicigoor\\AppLifecycle\\Events\\AppForegrounded",
            ["timestamp": Int(Date().timeIntervalSince1970 * 1000)]
        )
    }

    // ── Going to background ──────────────────────────────────────────────────
    center.addObserver(
        forName: UIApplication.didEnterBackgroundNotification,
        object: nil,
        queue: .main
    ) { _ in
        LaravelBridge.shared.send?(
            "Djurovicigoor\\AppLifecycle\\Events\\AppBackgrounded",
            ["timestamp": Int(Date().timeIntervalSince1970 * 1000)]
        )
    }

    print("AppLifecycle plugin initialized")
}