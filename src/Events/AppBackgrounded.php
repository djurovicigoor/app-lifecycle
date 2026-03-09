<?php

namespace Djurovicigoor\AppLifecycle\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Fired when the app moves to the background (user switches away or locks screen).
 *
 * Dispatched by the Android/iOS native layer via NativeActionCoordinator.
 */
class AppBackgrounded
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly int $timestamp
    ) {}
}
