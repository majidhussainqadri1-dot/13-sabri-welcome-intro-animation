# Automated QA Report — File 13 Version 1.1.0

Future Welcome Experience Superset / eighty-round closure baseline: 10 August 2026.

## Historical evidence boundary

All earlier `0.1.1`, `0.2.0` and `1.0.0` successful CI runs, package sizes, assertion counts and SHA-256 values are historical after the 1.1.0 Future source changes. They may be retained for provenance but must not be presented as proof for the current PR head.

## 1.1.0 verification scope

The permanent workflow verifies the exact current candidate through independently visible gates:

- PHP syntax across plugin/test PHP files;
- current PHP contract suite;
- historical post-plan and monotonic-governance regressions re-run against current source;
- dedicated Future Superset 18 PHP regressions;
- JavaScript syntax;
- core JavaScript runtime behavior;
- historical forty-round JavaScript runtime/static suites re-run against current source;
- dedicated Future Superset JavaScript runtime suite;
- security/privacy/architecture static contracts;
- deterministic sequential eighty-round review gate;
- translation catalogue freshness;
- reproducible `1.1.0` installable ZIP;
- ZIP integrity and exact generated SHA-256 verification;
- absence of obsolete temporary payload fragments and whitespace errors.

The Future regressions explicitly cover all 18 approved enhancements while also preserving the 30-day recurrence, fail-open, no-forced-auto-close, exact-green-primary, no-sound and canonical-ownership rules.

## Exact-head evidence rule

This file deliberately does **not** freeze a mutable branch to a historical package checksum or assertion total. The authoritative repository QA evidence is always:

1. the exact current PR-head SHA;
2. its fresh successful `File 13 Complete Integrity` run;
3. the exact artifact produced by that run;
4. the generated `release/SHA256SUMS` and `MANIFEST-1.1.0.json` from that exact run.

Any source/document/test/workflow change reopens this gate and requires a fresh run.

## Evidence boundary

A green exact-head workflow can establish repository **Coded / Packaged / Automated-QA Green** for the verified scope only. It cannot establish real WordPress/theme/File 00/20/24/25 integration, Hostinger/LiteSpeed behavior, browser/assistive-technology acceptance, backup restoration, Founder visual acceptance, production deployment or operational monitoring. Those remain separate staging/live gates.
