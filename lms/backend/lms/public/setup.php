<?php

/**
 * Fallback setup entry when routes are not cached yet.
 * Prefer visiting /setup after document root points to public/.
 */
header('Location: /setup', true, 302);
exit;
