# Meena Bazar Online — স্টোরফ্রন্ট UI নির্দেশনা

## ১. প্রসঙ্গ ও লক্ষ্য

ডিজাইনের উদ্দেশ্য: পরিচ্ছন্ন, দ্রুত ব্যবহারযোগ্য এবং কিবোর্ডে সম্পূর্ণ পরিচালনাযোগ্য গ্রোসারি স্টোরফ্রন্টের জন্য একক টোকেন ও পুনর্ব্যবহারযোগ্য কম্পোনেন্ট ব্যবস্থা তৈরি করা।

এই নির্দেশনা Meena Bazar Online-এর দেওয়া ব্রিফ অনুসরণ করে বর্তমান Vue স্টোরফ্রন্টে প্রয়োগযোগ্য। রেফারেন্স: https://meenabazaronline.com/ । সাইটের পূর্ণ রেন্ডার করা UI যাচাই করা হয়নি; এটি পিক্সেল-নির্ভুল প্রতিলিপির দাবি নয়। বর্তমান প্রজেক্টের ব্র্যান্ড, পণ্যের তথ্য ও API সংযোগ অক্ষুণ্ণ রেখে নির্দেশনাগুলো প্রয়োগ করতে হবে (must)।

ব্রিফে দেওয়া ঘনত্ব: ৫৮৭ লিংক, ১৭৮ বাটন, ২ ইনপুট, ১ নেভিগেশন, ১ তালিকা। এগুলো প্রদত্ত ইনভেন্টরি, যাচাইকৃত DOM গণনা বা পুনরুৎপাদনের লক্ষ্য নয়। এই ঘনত্বে পুনরাবৃত্ত কম্পোনেন্ট, অর্থপূর্ণ ল্যান্ডমার্ক এবং সংক্ষিপ্ত Tab পথকে অগ্রাধিকার দেওয়া উচিত (should)।

নিয়মে **must** বাধ্যতামূলক; **should** সুপারিশ।

## ২. ডিজাইন টোকেন ও ভিত্তি

টোকেন প্রবাহ must হবে primitive → semantic → component। Raw রং কেবল primitive ঘোষণায় থাকবে (must); কম্পোনেন্ট CSS ও নির্দেশনায় semantic/component alias ব্যবহার করতে হবে (must)।

### Primitive মান

| টোকেন | মান |
|---|---|
| `p.black` / `p.white` | `#000000` / `#ffffff` |
| `p.ink` / `p.gray` | `#111827` / `#6b7280` |
| `p.green` / `p.greenHover` | `#84b93e` / `#79a837` |
| `p.border` / `p.borderMuted` | `#e5e7eb` / `#d1d5db` |
| `p.greenText` | `#365314` — অ্যাক্সেসিবল সবুজ লেখার সংযোজন |
| `p.error` | `#b91c1c` — ত্রুটির সংযোজন |
| `font.family.primary` | `-apple-system` |
| `font.family.stack` | `-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji"` |
| `font.size.xs / sm / md / lg` | `12px / 14px / 16px / 24px` |
| `font.size.base / font.weight.base` | `14px / 400` |
| `font.lineHeight.base / font.lineHeight.ratio` | `21px / 1.5` |
| `font.weight.emphasis` | `600` — মূল্য, শিরোনাম ও action-এর সংযোজন |
| `space.1 / 2 / 3 / 4` | `4px / 8px / 12px / 16px` |
| `radius.xs / sm` | `4px / 6px` |
| `shadow.1` | `0 1px 3px 0 rgb(0 0 0 / 10%), 0 1px 2px -1px rgb(0 0 0 / 10%)` |
| `shadow.2` | `0 4px 6px -1px rgb(0 0 0 / 10%), 0 2px 4px -2px rgb(0 0 0 / 10%)` |
| `motion.duration.instant / fast` | `150ms / 300ms` |
| `motion.duration.reduced` | `0ms` |
| `size.control / size.icon` | `44px / 24px` — ব্যবহারের সুবিধার জন্য সংযোজন |
| `border.width / focus.width / focus.offset` | `1px / 2px / 2px` |
| `breakpoint.sm / md / lg` | `640px / 768px / 1024px` — layout সংযোজন |

Shadow থেকে ফলাফলে প্রভাবহীন transparent layer বাদ দেওয়া হয়েছে। ব্রেকপয়েন্ট build-time token হিসেবে media query-তে বসাতে হবে (must); সাধারণ CSS custom property সরাসরি media query condition-এ ব্যবহার করা যাবে না (must)।

### Semantic mapping

| Semantic token | Primitive/alias | উদ্দেশ্য |
|---|---|---|
| `surface.page`, `surface.card`, `surface.input` | `p.white` | মূল স্টোরফ্রন্ট |
| `surface.inverse` | `p.black` | নির্বাচিত বিপরীত রঙের অঞ্চল |
| `text.primary` | `p.ink` | শিরোনাম ও মূল লেখা |
| `text.secondary` | `p.gray` | সাদা পৃষ্ঠে সহায়ক লেখা |
| `text.onInverse` | `p.white` | কালো পৃষ্ঠে লেখা |
| `text.brand`, `link.default` | `p.greenText` | সাদা পৃষ্ঠে ব্র্যান্ড লেখা ও লিংক |
| `action.primary`, `surface.selected` | `p.green` | action fill |
| `action.hover`, `action.active` | `p.greenHover` | action feedback |
| `text.onAction` | `p.ink` | সবুজ বাটনের লেখা |
| `border.decorative` | `p.border` | কার্ড বিভাজক |
| `border.muted` | `p.borderMuted` | অলংকারমূলক বিভাজক |
| `border.control` | `p.gray` | ইনপুট ও control boundary |
| `border.brand` | `p.greenHover` | অলংকারমূলক brand border |
| `focus.ring` / `focus.onInverse` | `p.ink` / `p.white` | স্পষ্ট focus |
| `status.error` | `p.error` | error text, icon ও border |
| `surface.disabled` / `text.disabled` | `p.border` / `p.ink` | নিষ্ক্রিয় control |
| `layout.gutter`, `card.padding`, `section.gap` | `space.4` | ধারাবাহিক layout |
| `control.gap`, `label.gap` | `space.2` | icon/label এবং label/field |
| `grid.gap` | `space.3`; md থেকে `space.4` | পণ্যের ব্যবধান |
| `section.spacing` | `calc(space.4 * 2)` | section separation |
| `type.body`, `type.label`, `type.meta`, `type.heading` | `sm`, `sm`, `xs`, `lg` | typography role |
| `button.bg`, `button.fg`, `button.radius` | `action.primary`, `text.onAction`, `radius.xs` | component alias |

ব্রিফের `color.surface.base` must migrate হবে `surface.inverse`-এ এবং `color.surface.muted` হবে `surface.page`। `color.text.inverse`-এর সবুজ মান must থাকবে `action.primary` হিসেবে; সাধারণ inverse text হবে `text.onInverse`। পুরোনো নাম ধরে পুরো পেজ কালো করা যাবে না (must)।

সাদা পৃষ্ঠে মূল সবুজ দিয়ে ছোট লেখা বা সবুজ বাটনে সাদা লেখা ব্যবহার করা যাবে না (must)। চূড়ান্ত rendered color pair must QA-তে contrast পরীক্ষায় উত্তীর্ণ হবে। Decorative border must একমাত্র control boundary হবে না।

### বাস্তবায়ন উদাহরণ

```css
:root {
  --p-ink: #111827;
  --p-green: #84b93e;
  --p-green-hover: #79a837;
  --text-primary: var(--p-ink);
  --text-on-action: var(--text-primary);
  --action-primary: var(--p-green);
  --action-hover: var(--p-green-hover);
  --focus-ring: var(--text-primary);
  --space-2: 8px;
  --space-4: 16px;
  --radius-xs: 4px;
  --size-control: 44px;
  --focus-width: 2px;
  --focus-offset: 2px;
  --button-bg: var(--action-primary);
  --button-fg: var(--text-on-action);
}
.store-button {
  background: var(--button-bg);
  color: var(--button-fg);
  min-block-size: var(--size-control);
  padding: var(--space-2) var(--space-4);
  border-radius: var(--radius-xs);
}
.store-button:enabled:hover { background: var(--action-hover); }
.store-button:focus-visible {
  outline: var(--focus-width) solid var(--focus-ring);
  outline-offset: var(--focus-offset);
}
```

এটি token wiring-এর উদাহরণ; সম্পূর্ণ component stylesheet নয়। সম্পূর্ণ বাস্তবায়নে নিচের সব state must যুক্ত হবে।

## ৩. কম্পোনেন্টের নিয়ম

### সবার জন্য state contract

প্রতিটি কম্পোনেন্ট must নিচের সাতটি state implement করবে অথবা non-interactive container-এর ক্ষেত্রে child control-এ delegate করার বিষয়টি নথিবদ্ধ করবে। Container must কেবল state দেখাতে গিয়ে নতুন Tab stop তৈরি করবে না।

| State | বাধ্যতামূলক আচরণ |
|---|---|
| Default | must semantic surface, text ও boundary ব্যবহার করবে |
| Hover | must interactive target-এ রং/underline feedback দেবে; hover-এ সীমাবদ্ধ তথ্য থাকবে না |
| Focus-visible | must দৃশ্যমান outline রাখবে; sticky header/overlay আড়াল করবে না |
| Active | must pressed/current state দেখাবে; toggle-এ `aria-pressed`, current link-এ `aria-current` থাকবে |
| Disabled | must activation আটকাবে; native `disabled` অথবা `aria-disabled` ও event guard থাকবে; প্রয়োজন হলে কারণ দৃশ্যমান থাকবে |
| Loading | must pending label ও `aria-busy` থাকবে; duplicate request আটকাবে এবং layout স্থির রাখবে |
| Error | must নির্দিষ্ট কারণ ও recovery action দেখাবে; শুধু রং দিয়ে বোঝাবে না; field error `aria-describedby` দিয়ে যুক্ত হবে |

Native button must Enter/Space-এ কাজ করবে; native link must Enter-এ খুলবে। Pointer ও touch must একই ফল দেবে। শুধু hover-এ menu/action প্রকাশ করা যাবে না (must)। Loading button should focus ধরে রেখে `aria-disabled` ও guard ব্যবহার করবে।

### কম্পোনেন্টভিত্তিক সাতটি state

নিচের প্রতিটি সারি must উপরোক্ত contract-এর সঙ্গে প্রয়োগ করতে হবে।

| কম্পোনেন্ট | Default | Hover | Focus-visible | Active | Disabled | Loading | Error |
|---|---|---|---|---|---|---|---|
| Header/navigation | logo, search, category, cart | link underline | প্রতিটি child-এ ring | current route | unavailable action + কারণ | count placeholder, nav সচল | count failure + retry |
| Button/link | primary/secondary/text | action.hover/underline | ring | action.active/pressed | guard + disabled token | “যোগ হচ্ছে…” | inline failure + retry |
| Search/input | label, field, submit | control boundary বজায় | input ring | typing/selected suggestion | বন্ধ + কারণ | “খোঁজা হচ্ছে…” | query বজায় + retry |
| Category/filter | label, count, checkbox | label feedback | native control ring | checked + icon | unavailable option | results busy | আগের filter বজায় + retry |
| Product card | image, name, unit, price, add | link underline/button feedback | পৃথক link/button ring | cart action feedback | “স্টকে নেই” | fixed image placeholder/action pending | image fallback/add retry |
| Banner/carousel | heading, image, CTA, controls | controls feedback | controls/CTA ring | current indicator | boundary control বন্ধ | স্থির aspect placeholder | fallback content; dead CTA বাদ |
| Product section/list | heading, grid, আরও দেখুন | child delegate | child delegate | pagination current | শেষ পৃষ্ঠায় next বন্ধ | grid placeholder | section-local retry |
| Cart/quantity | items, quantity, total | control feedback | control ring | pending quantity | min/max control বন্ধ | total busy, checkout guarded | আগের quantity ফেরত + কারণ |
| Dialog/drawer | title, content, close | child delegate | focus ভিতরে | trigger expanded | child delegate | content busy, close সচল | inline retry; close সচল |

### Anatomy, variant ও responsive আচরণ

| কম্পোনেন্ট | গঠন ও টোকেন | কিবোর্ড/pointer/touch এবং edge case |
|---|---|---|
| Header/navigation | must `header`, নামযুক্ত `nav`, logo link, search, cart; `layout.gutter`, `type.label` | must skip-link থেকে main-এ যাওয়া যাবে। Mobile-এ labelled menu button, `aria-expanded`; Escape-এ বন্ধ ও trigger-এ focus ফেরত। Desktop-এ সাধারণ link; application `menubar` নয়। দীর্ঘ category must wrap করবে। |
| Button/link | must text label, optional decorative icon; `button.*`, `control.gap`, `type.label`; primary, outlined secondary, text variant | must action-এ button, navigation-এ link; icon-only control-এ accessible name। Label must wrap করতে পারবে; narrow screen-এ full width should হবে। |
| Search/input | must visible label, input, submit, clear ও result status; `surface.input`, `border.control`, `label.gap`; search/text variant | must Enter submit, clear button-এ focus থেকে query clear; empty query-তে নির্দেশনা। Autocomplete থাকলে must combobox/listbox, Arrow keys, Enter select, Escape dismiss এবং active descendant থাকবে। না থাকলে সাধারণ search form should ব্যবহার হবে। |
| Category/filter | must labelled group, native checkbox/radio, reset; `space.2`, `type.label` | must Space toggle; mobile drawer ও desktop panel একই selection রাখবে। Long label wrap; zero-match-এ “ফিল্টার মুছুন”; zero-stock filter লুকানোর বদলে count should দেখাবে। |
| Product card | must image area, product link, unit, current price, optional old price, cart button; `card.padding`, `grid.gap`, `radius.sm`, `shadow.1` | must পুরো card-এর মধ্যে nested button/link থাকবে না। Title wrap; clamp থাকলে পূর্ণ নাম detail page ও accessible name-এ থাকবে। Price ভাঙবে না; টাকা+unit আলাদা semantic text। Missing image-এ fallback, missing price-এ “দাম পাওয়া যায়নি”; add বন্ধ। |
| Banner/carousel | must heading, image, CTA, previous/next, position; `section.gap`, `type.heading` | should autoplay বন্ধ থাকবে। চালু হলে must pause control, hover/focus pause এবং reduced-motion-এ autoplay বন্ধ। Swipe-এর পাশাপাশি buttons must থাকবে। শূন্য slide-এ section বাদ; এক slide-এ controls বাদ; modulo-by-zero must এড়াবে। |
| Product section/list | must heading, semantic `ul/li` grid, pagination/load more; `section.spacing`, `grid.gap` | must নতুন item যোগে focus স্থির থাকবে ও count politely ঘোষণা হবে। 320px-এ ১ column; sm-এ ২, md-এ ৪, lg-এ ৫; card content চাপলে fewer columns should হবে। Empty state-এ কারণ ও shopping recovery action must থাকবে। |
| Cart/quantity | must item name, unit price, numeric quantity, remove, subtotal, delivery ও total; `space.3`, `type.body` | must +/- button-এ পণ্যের নামসহ label; typing-এ integer/min/max validation। Pending request-এ duplicate mutation বন্ধ। Server price/stock must authoritative হবে। Empty cart-এ “কেনাকাটা শুরু করুন”; long name wrap, mobile summary stack। |
| Dialog/drawer | must accessible title, close button, content/actions; `surface.card`, `card.padding`, `shadow.2` | must open-এ meaningful initial focus, modal Tab containment, background inert, Escape/close এবং focus restoration। Touch outside dismissal should হবে, কিন্তু একমাত্র close উপায় নয়। Mobile viewport-এ inner scroll; close button দৃশ্যমান must থাকবে। |

সব কম্পোনেন্টে body line-height must `font.lineHeight.ratio` হবে। Section heading must `type.heading`, supporting metadata must `type.meta` হবে। Touch form field should `font.size.md` ব্যবহার করবে। নতুন spacing/typography প্রয়োজন হলে shared token হিসেবে যুক্ত করতে হবে (must), স্থানীয় exception হিসেবে নয়।

## ৪. অ্যাক্সেসিবিলিটি ও pass/fail শর্ত

লক্ষ্য WCAG 2.2 AA। নিচের checks must বাস্তব UI-তে পরীক্ষা করতে হবে; নির্দেশনা থাকা মানেই pass নয়।

| পরীক্ষা | Pass; অন্যথায় Fail |
|---|---|
| Text contrast | must normal text ≥4.5:1; ≥24px regular বা ≥18.66px bold text ≥3:1 হবে; hover/active/error-ও পরীক্ষা |
| Control contrast | must অর্থবহ boundary/icon/focus adjacent color-এর বিপরীতে ≥3:1; decorative divider ব্যতিক্রম |
| Keyboard | must mouse ছাড়া search, filter, product navigation, add, quantity ও checkout entry সম্পন্ন হবে; trap থাকবে শুধু active modal-এর ভিতরে |
| Focus | must প্রতিটি interactive item-এ visible focus থাকবে; sticky bar-এ ঢাকবে না; modal close-এ trigger-এ ফিরবে |
| Target size | must app controls কমপক্ষে `size.control` hit area পাবে; inline prose links-এ WCAG 2.5.8 exception ছাড়া ≥24×24 CSS px বা প্রয়োজনীয় spacing থাকবে |
| Reflow | must 320 CSS px viewport এবং 1280px-এ 400% zoom-এ page horizontal scroll ছাড়া ব্যবহারযোগ্য হবে |
| Text scaling | must 200% text scaling এবং line-height 1.5, paragraph spacing 2em, letter spacing .12em, word spacing .16em-এ content/action হারাবে না |
| Labels/semantics | must সব input-এর programmatic label, button-এর name এবং heading hierarchy থাকবে; document `lang` বর্তমান ভাষার সঙ্গে মিলবে |
| Feedback | must cart/result update `aria-live="polite"` status-এ শোনা যাবে; জরুরি submission error যথাযথ alert হবে; পুরো grid live region হবে না |
| Forms | must field error-এর সঙ্গে field যুক্ত থাকবে; failed submission-এ error summary/failing field-এ focus যাবে; লেখা data হারাবে না |
| Motion | must `prefers-reduced-motion`-এ nonessential animation ও autoplay বন্ধ হবে; flashing থাকবে না |
| Images | must informative product image-এ meaningful alt; পাশের একই labelled link-এর পুনরাবৃত্ত decorative image-এ empty alt হবে |
| Authentication | must paste/password manager অনুমোদিত থাকবে; checkout login-এ unsupported cognitive puzzle বাধ্যতামূলক হবে না |
| Checkout | must চূড়ান্ত order submit-এর আগে item, address ও total পর্যালোচনা/সংশোধনের সুযোগ থাকবে; পূর্বে দেওয়া তথ্য অযথা আবার চাইবে না |

Automated axe/Lighthouse scan should manual keyboard ও screen-reader পরীক্ষার সঙ্গে চালাতে হবে; automated score একা conformant হওয়ার প্রমাণ নয়।

## ৫. লেখা ও টোন

লেখা must ছোট, স্পষ্ট এবং কাজের ফল বোঝায় এমন হবে। বর্তমান locale must সব UI label, সংখ্যা ও currency formatting-এ অনুসরণ করবে; অনুবাদ key should বিদ্যমান `useI18n` ব্যবস্থায় যুক্ত হবে।

| প্রসঙ্গ | ব্যবহারযোগ্য লেখা | নিষিদ্ধ অস্পষ্ট লেখা |
|---|---|---|
| Add | “কার্টে যোগ করুন” | “এখানে ক্লিক” |
| Pending | “যোগ হচ্ছে…” | “অপেক্ষা” |
| Search empty | “কোনো পণ্য পাওয়া যায়নি। অন্য নাম দিয়ে খুঁজুন।” | “No data” |
| Network error | “পণ্য লোড হয়নি। আবার চেষ্টা করুন।” | “কিছু একটা হয়েছে” |
| Stock limit | “সর্বোচ্চ ৫টি কেনা যাবে।” | “Invalid quantity” |
| Cart success | “চাল কার্টে যোগ হয়েছে।” | “Success” |

মূল্য must locale-aware currency format-এ হবে; discount must বর্তমান ও পূর্বের দাম আলাদা করে বোঝাবে। Shipping charge অজানা থাকলে must “ঠিকানা দিলে হিসাব হবে” বলবে, শূন্য খরচ হিসেবে দেখাবে না।

## ৬. নিষিদ্ধ প্যাটার্ন, migration ও edge case

- must `outline: none` দিয়ে বিকল্প ছাড়া focus মুছবে না; clickable `div`, nested links/buttons এবং hover-only menu ব্যবহার করবে না।
- must stock/error/selected অবস্থা শুধু রং দিয়ে বোঝাবে না; disabled করতে শুধু opacity বা `pointer-events` ব্যবহার করবে না।
- must প্রতিটি পণ্যের card-এ অতিরিক্ত Tab stop যোগ করবে না; primary product link ও প্রয়োজনীয় action-ই থাকবে।
- must ৫৮৭ লিংক/১৭৮ বাটন অর্জনের জন্য কৃত্রিম content যোগ করবে না। Section pagination ও অনুরোধভিত্তিক loading should ব্যবহার হবে।
- must failing section-এর কারণে পুরো homepage ফাঁকা করবে না; stale data দেখালে তা বোঝাবে এবং retry দেবে।
- must দীর্ঘ বাংলা/ইংরেজি নাম, image 404, zero banner, zero results, slow network, offline, stock change ও repeated click সামলাবে।
- should আগে `frontend/src/style.css`-এ scoped storefront tokens বসিয়ে `ProductCard.vue`, header/search, তারপর `Home.vue` ও cart-এ ধাপে প্রয়োগ করবে। বর্তমান cart API এবং translation contract must বজায় থাকবে।
- must CSS token names বদলালে সব consumer migrate করবে; প্রয়োজনীয় সাময়িক alias রেখে পরে obsolete token সরাবে। Backend migration এই UI পরিবর্তনের অংশ নয়; `migrate:fresh` must কখনো চালানো যাবে না।

## ৭. QA checklist

- [ ] must সব raw রং primitive স্তরে এবং component style-এ semantic alias আছে।
- [ ] must প্রতিটি component-এর সাতটি state এবং recovery action যাচাই হয়েছে।
- [ ] must 320, 640, 768, 1024px এবং বড় viewport-এ long content ঠিক আছে।
- [ ] must contrast, keyboard, focus, target size, reflow ও text-spacing checks pass করেছে।
- [ ] must screen reader-এ labels, errors, cart status ও modal title সঠিক শোনা যায়।
- [ ] must reduced-motion, empty/one-slide carousel ও missing images পরীক্ষিত।
- [ ] must slow/failing request, duplicate add, quantity limit ও server stock update সামলানো হয়েছে।
- [ ] must locale, product links, search, filter, cart total এবং checkout review কাজ করছে।
- [ ] must implementation-এর পরে frontend production build সফল; ফল নথিবদ্ধ।
- [ ] should representative homepage ও cart-এ automated accessibility scan চালানো হয়েছে।

বর্তমান অবস্থা: নির্দেশনা প্রস্তুত; UI implementation ও উপরের runtime QA এখনও সম্পন্ন হয়নি।
