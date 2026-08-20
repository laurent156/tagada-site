# Component patterns — orange-template design system

All snippets are pulled verbatim from a real working implementation (the Tagada SRL
site). Assumes `tokens.css` variables are in scope.

## Scroll fade-in (`.reveal-io`)

```css
/* slideInBottom language, driven by polling (see JS below) */
.reveal-io{ opacity:0; transform:translateY(38px); transition:opacity .9s cubic-bezier(.25,1,.5,1), transform .9s cubic-bezier(.25,1,.5,1); }
.reveal-io.is-visible{ opacity:1; transform:translateY(0); }
@media (prefers-reduced-motion: reduce){ .reveal-io{ opacity:1 !important; transform:none !important; transition:none !important; } }
```

## Image curtain reveal (`.curtain`)

An orange panel covers the image/frame and lifts away on scroll-in.

```css
.curtain{ position:relative; overflow:hidden; }
.curtain .cur-fill{ position:absolute; left:0; right:0; bottom:0; top:auto; height:100%; background:var(--accent); transition:height 1.1s var(--ease); z-index:2; }
.curtain.is-revealed .cur-fill{ height:0%; }
@media (prefers-reduced-motion: reduce){ .curtain .cur-fill{ display:none; } }
```

```html
<div class="frame curtain reveal-io">
  <img src="..." alt="...">
  <div class="cur-fill"></div>
</div>
```

Note: an element can legitimately be **both** `.reveal-io` and `.curtain` at once
(fades in AND lifts its own curtain). Flag both classes independently in the trigger
logic — see the `sweep()` gotcha below.

## Scroll trigger — polling, NOT IntersectionObserver

IntersectionObserver does not fire in this tool's headless browser (confirmed: a
fresh observer on an already-visible element never triggered). Use this instead —
it also just works in real browsers:

```js
(function(){
  var targets = Array.prototype.slice.call(document.querySelectorAll('.reveal-io, .curtain'));
  var timer = setInterval(sweep, 150);
  function sweep(){
    if (!targets.length){ clearInterval(timer); return; }
    var vh = window.innerHeight;
    targets = targets.filter(function(el){
      var r = el.getBoundingClientRect();
      if (r.top < vh - 60 && r.bottom > 0){
        // flag each class independently — a ternary that picks only one
        // will silently skip the other and leave the element stuck invisible
        if (el.classList.contains('reveal-io')) el.classList.add('is-visible');
        if (el.classList.contains('curtain')) el.classList.add('is-revealed');
        return false;
      }
      return true;
    });
  }
  sweep();
})();
```

## Navbar hide-on-scroll

```css
.navbar{ transition:transform .5s var(--ease), border-color .3s; }
.navbar.nav-hidden{ transform:translateY(-100%); }
```

```js
(function(){
  var nav = document.querySelector('.navbar');
  var lastY = window.scrollY;
  setInterval(function(){
    var y = window.scrollY;
    if (y > lastY && y > 80) nav.classList.add('nav-hidden');
    else nav.classList.remove('nav-hidden');
    lastY = y;
  }, 150);
})();
```

## Accordion with hover + open "fill" (FAQ pattern)

Two independent orange fills: one that slides up on hover, one that stays filled
while the item is open.

```css
.accordion2{ position:relative; background:rgba(244,241,237,.06); border:1px solid var(--on-dark-15); border-radius:16px; overflow:hidden; cursor:pointer; }
.accordion2 .fill{ position:absolute; left:0; top:0; width:100%; height:0%; background:var(--accent); transition:height .7s var(--ease); }
.accordion2 .fill.hover-fill{ z-index:1; }
.accordion2 .fill.open-fill{ z-index:2; }
.accordion2:hover .fill.hover-fill{ height:100%; }
.accordion2.is-open .fill.open-fill{ height:100%; }
```

```html
<div class="accordion2 reveal-io">
  <div class="fill hover-fill"></div>
  <div class="fill open-fill"></div>
  <div class="head"><p>Question</p><span class="icon">+</span></div>
  <div class="body"><p>Answer</p></div>
</div>
```

## Logo strip (grayscale → color on hover)

```css
.logo-chip{ height:28px; width:auto; max-width:140px; object-fit:contain; display:block; filter:grayscale(1); opacity:.5; transition:filter .4s var(--ease), opacity .4s var(--ease); }
.logo-chip:hover{ filter:grayscale(0); opacity:1; }
```

```html
<div class="logos-row">
  <img class="logo-chip reveal-io" src="data:image/png;base64,..." alt="Brand">
</div>
```

## Button icon-swap on hover

```css
.icon-slide{ position:relative; width:16px; height:16px; overflow:hidden; display:inline-block; flex-shrink:0; }
.icon-slide svg{ position:absolute; top:0; left:0; width:16px; height:16px; transition:transform 1s var(--ease); }
.icon-slide .ic-a{ transform:translateX(0); }
.icon-slide .ic-b{ transform:translateX(-130%); }
.btn-primary:hover .icon-slide .ic-a{ transform:translateX(130%); }
.btn-primary:hover .icon-slide .ic-b{ transform:translateX(0); }
```

## CSS specificity note (footer/social links)

A parent-scoped rule can silently win over a more "specific-looking" utility class
if their specificity is equal or the parent rule comes later:

```css
/* BROKEN: .footer-col a (0,1,1) can beat .social-link (0,1,0) */
.footer-col a{ display:block; }
.social-link{ display:flex; align-items:center; gap:8px; }

/* FIX: scope the override explicitly instead of reaching for !important */
.footer-col a.social-link{ display:flex; align-items:center; gap:8px; }
```
