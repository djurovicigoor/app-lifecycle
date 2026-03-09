<?php

namespace Djurovicigoor\AppLifecycle\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Fired when the app returns to the foreground (after being backgrounded).
 *
 * Dispatched by the Android/iOS native layer via NativeActionCoordinator.
 * The first launch resume is intentionally suppressed — this only fires
 * on genuine foreground returns.
 */
class AppForegrounded
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly int $timestamp
    ) {}
}
