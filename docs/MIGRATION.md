# Migration

File 13 migration is intentionally small, non-destructive and suppressive.

1. Inventory any old theme-injected splash/intro, option keys, cookies/localStorage keys, and route hooks.
2. Upgrade to 1.0.1; the schema migration forces File 13 activation and analytics state to disabled and clears its historical cleanup schedule.
3. Sync the File 01 manifest so File 13 is recorded as compatibility/migration scope. Existing active records are downgraded to `degraded`, never preserved as active.
4. Confirm File 24 evaluates `spcrc/file13_contract_state` as `compatible` only while no legacy public hook is registered.
5. Allow old File 13 browser keys to expire harmlessly; do not import them into another owner without a separately reviewed migration.
6. Implement and validate the active welcome experience in File 20 (invocation/frequency) and File 25 (presentation) before any production claim.
7. Use File 13 preview only to inspect historical copy during migration; it stores no preference or analytics state.

No companion table, profile/content data, File 20 state or File 25 state is mutated.
