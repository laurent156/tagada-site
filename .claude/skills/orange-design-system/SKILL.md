---
name: orange-design-system
description: >
  Design system reverse-engineered from the Webflow template orange-template.webflow.io
  (dark ink / warm beige / orange-accent studio-portfolio aesthetic). Use whenever the
  user wants a new site or page built in this look, references "the orange template",
  "le design system qu'on a fait", or asks to reuse the Tagada site's style on a new
  project. Covers color/type/radius/easing tokens, the curtain image-reveal, the
  scroll-triggered fade-in, the accordion "fill" hover, the hide-on-scroll navbar, and
  the grayscale-on-hover logo strip — plus the headless-browser gotchas that bit us
  building it (IntersectionObserver never fires here; poll with getBoundingClientRect
  instead).
---

## What this is

A design system reverse-engineered from `orange-template.webflow.io` by reading its
Webflow IX2 interaction JSON and rebuilding the CSS/JS by hand. First used for the
Tagada SRL site (Brussels signage/large-format fabrication studio). Everything below
is implementation-proven — pulled from a real working page, not reconstructed from memory.

Read `references/tokens.css` for the full `:root` token block and `references/components.md`
for copy-pasteable CSS/JS for each component pattern.

## Build approach

- **Single self-contained HTML file per page.** Fonts and images embedded as base64
  data URIs (`data:font/ttf;base64,...`, `data:image/png;base64,...`). No external
  requests, no build tooling required — opens directly in a browser or deploys as-is
  to any static host (GitHub Pages, Netlify, Vercel).
- If a project has a template file that gets assembled into multiple output pages
  (e.g. a shared `<style>`/nav/footer injected into several legal pages), **make the
  template the single source of truth and extract the shared blocks from it at build
  time** — never maintain a separate hand-edited snapshot of "the CSS" or "the footer"
  alongside the template. A build script that overwrites the template's own edited
  blocks with a stale cached copy is a real bug we hit and had to fix: any direct edit
  to the template silently vanished from the assembled output because the stale
  snapshot clobbered it on every rebuild.

## Core tokens (see references/tokens.css for the full block)

```css
--bg:#EEEBE9; --surface:#FFFFFF; --gray:#E3E0DE; --ink:#160E08;
--accent:#FF7A00; --accent-deep:#C35E00;
--on-dark:#F4F1ED; --dark-surface:#221A12;
--ease: cubic-bezier(.645,.045,.355,1);
--radius-pill:800px; --radius-lg:24px; --radius-md:12px;
--font-display:'Archivo','Arial Narrow',sans-serif;
--font-body:'Inter Tight',Arial,sans-serif;
```

## Signature components

- **Curtain image reveal** — an orange panel lifts off a photo/frame on scroll-in (`.curtain .cur-fill`, `height:100%` → `0%` on `.is-revealed`).
- **Scroll fade-in** — `.reveal-io`, opacity+translateY, `.is-visible` on trigger.
- **Accordion "fill"** — FAQ items get an orange fill sliding up from the bottom on hover, and a second independent fill on open (`.fill.hover-fill` / `.fill.open-fill`, both `height:0%→100%`).
- **Navbar hide-on-scroll** — translateY(-100%) on scroll down, reveals on scroll up.
- **Logo strip** — client/brand logos as `<img>`, grayscale + 50% opacity by default, full color on hover (`.logo-chip`).

Full CSS + JS for each is in `references/components.md`.

## Critical gotcha: IntersectionObserver does not fire in this tool's headless browser

Confirmed by direct testing (a fresh observer on an already-visible element never
triggered). **Do not use IntersectionObserver for any reveal/scroll-trigger logic in
pages you'll test with this environment's browser tool.** Use `setInterval` polling
with `getBoundingClientRect()` instead (see the `sweep()` function in
`references/components.md`) — it costs nothing noticeable at a 150ms interval and
works identically in real browsers.

A related trap: an element that is *both* `.reveal-io` and `.curtain` (fade-in AND
its own curtain lift) needs **both** classes flagged independently in the same pass.
A ternary that does `classList.add(isCurtain ? 'is-revealed' : 'is-visible')` will
silently skip one of the two and leave the element stuck invisible.

## Other gotchas worth remembering

- CSS specificity: a generic parent-scoped rule (e.g. `.footer-col a{display:block}`)
  can silently beat a more specific-looking utility class (`.social-link{display:flex}`)
  if the parent rule has equal or higher specificity — scope the override to
  `.footer-col a.social-link` rather than fighting it with `!important`.
- Sizing an icon/logo with only `height:Npx; width:auto` can produce a fractional
  computed width that visibly distorts edges at small sizes — pin both dimensions
  (or at minimum add `object-fit:contain`) when the source aspect ratio must be exact.
- Bash heredocs (`git commit -m "$(cat <<'EOF' ...)"`) can fail with
  "unexpected EOF" on certain content — write the message to a temp file and use
  `git commit -F <file>` instead.
