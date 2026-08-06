# Administrator Manual

1. Open `Settings → Sabri Welcome Intro`.
2. Keep frequency at 30 days unless a longer approved interval is required; values below 30 are rejected.
3. Keep eligible routes narrowly allowlisted. The default is `/` only. `*` requires explicit cross-file review.
4. Do not remove login, registration, account, support, booking, clinical, emergency or transaction suppressions without approval.
5. Use the signed preview buttons for default, reduced motion, skipped, disabled and failure states. Preview never changes public state.
6. Review System Check before release. `degraded` blocks release.
7. Keep analytics disabled unless aggregate counting is approved. It never stores identity/network/device data.
8. Use Restore Safe Defaults only after reading the confirmation; the command is nonce-protected and audited.
9. Use File 20/24 safe mode or `SWI_DISABLE` for emergency containment.
10. Complete every item in `STAGING-ACCEPTANCE.md` before merge/live deployment.
