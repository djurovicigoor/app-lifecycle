<?php

namespace Djurovicigoor\AppLifecycle\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Fired once when the app first launches (cold start / boot).
 *
 * Dispatched by the Android/iOS native layer via NativeActionCoordinator.
 * Unlike AppForegrounded, this fires on the very first resume at launch.
 */
class AppBooted
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly int $timestamp
    ) {}
}
