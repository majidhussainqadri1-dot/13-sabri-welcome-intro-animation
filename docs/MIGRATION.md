# Migration

File 13 migration is intentionally small, non-destructive and suppressive.

1. Inventory any old theme-injected splash/intro, option keys, cookies/localStorage keys, and route hooks.
2. Upgrade to 1.0.1; the schema migration forces File 13 activation and analytics state to disabled. The retention-only daily cleanup schedule is preserved/re-established for legacy aggregates older than 90 days, without re-enabling analytics collection. Verify the scheduled event and deletion on the exact deployed environment.
3. Sync the File 01 manifest so File 13 is recorded as compatibility/migration scope. Existing active records are downgraded to `degraded`, never preserved as active.
4. Confirm File 24 evaluates `spcrc/file13_contract_state` as `compatible` only when no legacy public hook is registered, schema 1.0.1 is persisted, File 01 registry is synced, and no `swi_intro_audit_gap` marker remains.
5. Allow old File 13 browser keys to expire harmlessly; do not import them into another owner without a separately reviewed migration.
6. Implement and validate the active welcome experience in File 20 (invocation/frequency) and File 25 (presentation) before any production claim.
7. Use File 13 preview only to inspect historical copy during migration; it stores no preference or analytics state.

No companion table, profile/content data, File 20 state or File 25 state is mutated. Before approving a permanent ownership transfer, verify the dated Founder change-control record, affected-file migration, rollback and acceptance. The original plan names package folder `13-sabri-welcome-intro`, whereas this candidate builds `sabri-welcome-intro-13`; resolve that package identity through explicit change control before deployment.

8. Verify the exact WordPress `rewrite_rules` persisted mapping and request-local File 13 preview registration after activation; if absent or foreign-owned, health must be `degraded` and File 24 assurance `blocked`. Do not flush a foreign route or claim live preview availability without an authenticated live re-test.
