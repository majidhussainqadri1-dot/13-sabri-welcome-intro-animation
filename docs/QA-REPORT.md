# Automated QA Report — File 13 Version 1.0.0

Plan-reconciliation baseline: 2026-08-10 PKT

## Historical evidence boundary

The 7 August candidate at `57f2f015b977a7d5c15e125146f0d7bc4343f6d3` had green GitHub Actions and preserved forty-round evidence. That evidence is historical after the 10 August plan-reconciliation source changes and must not be presented as proof for a later head.

## 10 August plan-reconciliation findings

Fresh comparison against the governing central plan and File 13 plan found and corrected four repository-level defects:

1. historical forced eight-second duration still active;
2. `#087A3E` near-match used instead of exact Sabri Green `#087A4E`;
3. default claim did not use the governing healing identity;
4. integration filters could broaden local kill/privacy/eligibility denials.

The CI workflow was also refreshed to immutable SHAs of current Node24-compatible official `actions/checkout` and `actions/upload-artifact` releases after the runner surfaced Node20 deprecation warnings.

## Exact-head CI gate

Repository QA is valid only when the exact current PR head has a successful GitHub Actions run. The workflow must show:

- PHP contract tests: **62 PASS, 0 FAIL**;
- post-plan PHP regressions: **15 PASS, 0 FAIL**;
- Node runtime/static tests: **28 PASS, 0 FAIL**;
- static security/privacy/architecture contracts: **19 PASS, 0 FAIL**;
- PHP syntax and declared PHP 7.4 compatibility: PASS;
- JavaScript syntax: PASS;
- deterministic package build: PASS;
- ZIP CRC/integrity: PASS;
- source/evidence checksum ledger: PASS;
- package entries: **21**;
- package bytes: **88,910**;
- package SHA-256: `407aac00a10249cc942a10646b8d263584093d2108abc0a3ba2ae185cfe60615`.

The exact PR head and its check-run are the authoritative QA evidence; this document intentionally does not freeze a mutable branch to a historical “current” commit.

## Evidence boundary

These automated checks prove repository-verifiable behavior only. They do not prove active WordPress/theme/File 00/20/24/25 integration, Hostinger cache behavior, real browser/assistive-technology behavior, backup restoration, Founder acceptance, live deployment or operational monitoring.
