# Accessibility and Performance Evidence

## Implemented accessibility controls

- semantic `dialog` with label, description and one polite announcement;
- immediate visible Close, Continue and Skip controls with inline icons and text/accessible names;
- minimum 44×44 CSS-pixel targets;
- Escape support, bounded focus containment, background `inert`/`aria-hidden`, cleanup and focus restoration;
- hidden-by-default/no-JavaScript fail-open;
- `prefers-reduced-motion` static/short route and explicit preview;
- visible focus, forced-colors borders, logical properties, RTL-aware motion origin;
- responsive mobile action stacking and no audio.

## Source budgets

- no remote runtime dependency;
- no font download;
- one local stylesheet, one deferred local script and one optimized SVG;
- no database query loop, pagination or background queue;
- public eligibility is request-time constant work over bounded route arrays (maximum 100 each);
- client state reads are bounded and synchronous; optional network writes are keepalive/failure-tolerant.

## Staging objectives

- no measurable layout shift from the hidden overlay;
- underlying DOM available immediately;
- no forced automatic close by default; when a Founder-approved nonzero duration is configured it must match the approved visual specification and remain within the 30,000 ms safety bound; reduced path remains 250–1,500 ms when auto-close is enabled;
- representative public page Core Web Vitals remain in the “good” range;
- no page-level horizontal scrollbar at 320 CSS px or 400% zoom;
- keyboard, NVDA/JAWS/VoiceOver, RTL and reduced-motion manual acceptance.
