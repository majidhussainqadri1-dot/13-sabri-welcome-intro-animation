# Privacy

File 13 version 1.0.1 has no public intro runtime and performs no public analytics or browser persistence.

Historical browser keys are no longer read or written by File 13. File 20 is the current frequency owner and must govern any active session/cookie/localStorage behavior under its own privacy contract.

Legacy aggregate options may remain for audit/rollback evidence, but no public endpoint records new File 13 events. A retention-only daily cleanup hook remains scheduled while the plugin is active and deletes aggregate options older than 90 days; it never re-enables collection. Deactivation/uninstall clears the scheduled hook. WordPress cron execution and actual database deletion require separate operational verification.

The plugin does not export File 00/File 24 rights by pretending to own them. Platform-wide export/erasure/legal-hold policy remains with the canonical owners.
