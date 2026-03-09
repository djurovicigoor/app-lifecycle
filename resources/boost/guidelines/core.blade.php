## djurovicigoor/app-lifecycle

App lifecycle state detection plugin for NativePHP Mobile — detects foreground/background transitions on Android and iOS.

### Installation

```bash
composer require djurovicigoor/app-lifecycle
```

### PHP Usage (Livewire/Blade)

Use the `AppLifecycle` facade:

@verbatim
<code-snippet name="Using AppLifecycle Facade" lang="php">
use Djurovicigoor\AppLifecycle\Facades\AppLifecycle;

// Execute the plugin functionality
$result = AppLifecycle::execute(['option1' => 'value']);

// Get the current status
$status = AppLifecycle::getStatus();
</code-snippet>
@endverbatim

### Available Methods

- `AppLifecycle::execute()`: Execute the plugin functionality
- `AppLifecycle::getStatus()`: Get the current status

### Events

- `AppLifecycleCompleted`: Listen with `#[OnNative(AppLifecycleCompleted::class)]`

@verbatim
<code-snippet name="Listening for AppLifecycle Events" lang="php">
use Native\Mobile\Attributes\OnNative;
use Djurovicigoor\AppLifecycle\Events\AppLifecycleCompleted;

#[OnNative(AppLifecycleCompleted::class)]
public function handleAppLifecycleCompleted($result, $id = null)
{
    // Handle the event
}
</code-snippet>
@endverbatim

### JavaScript Usage (Vue/React/Inertia)

@verbatim
<code-snippet name="Using AppLifecycle in JavaScript" lang="javascript">
import { appLifecycle } from '@djurovicigoor/app-lifecycle';

// Execute the plugin functionality
const result = await appLifecycle.execute({ option1: 'value' });

// Get the current status
const status = await appLifecycle.getStatus();
</code-snippet>
@endverbatim