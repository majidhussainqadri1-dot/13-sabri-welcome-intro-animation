# Migration

File 13 migration is intentionally small and non-destructive.

1. Inventory any old theme-injected splash/intro, option keys, cookies/localStorage keys, and route hooks.
2. Disable duplicate theme injection before File 13 public activation.
3. Activate File 13 with File 20 present; the official integration is the File 20 welcome invocation hook.
4. Old browser keys may expire harmlessly. Do not import identifiers or tracking cookies into File 13.
5. Confirm Sabri Green token output and that no legacy orange-primary styling remains active.
6. Verify authenticated/task/clinical/emergency/recovery routes are suppressed by File 20 and rejected by File 13 if context is invalid.
7. Run preview, reduced-motion, JavaScript-disabled, storage-denied and slow-network checks.
8. Keep the feature flag/kill switch available throughout the observation window.

No companion table or profile/content data is mutated.
